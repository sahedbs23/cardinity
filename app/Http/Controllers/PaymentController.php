<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentValidation;
use App\Models\Payment;
use App\Repositories\CartRepository;
use App\Repositories\Contracts\SessionRepository;
use App\Repositories\OrderDetailsRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Cardinity\Client;
use Cardinity\Method\Payment as CardinityPayment;
use Cardinity\Exception;

class PaymentController extends Controller
{
    private $repository;
    private $paymentRepository;
    private $orderRepository;
    private $orderDetailsRepository;
    private $productRepository;

    public function __construct(
        CartRepository $repository,
        PaymentRepository $paymentRepository,
        OrderRepository $orderRepository,
        OrderDetailsRepository $orderDetailsRepository,
        ProductRepository $productRepository
    )
    {
        $this->repository = $repository;
        $this->paymentRepository = $paymentRepository;
        $this->orderRepository = $orderRepository;
        $this->orderDetailsRepository = $orderDetailsRepository;
        $this->productRepository = $productRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        $product_cart  = $this->repository->find(0);
        if (!$product_cart){
            return redirect(route('product_list'))->with(['errorMessage' => 'Cart is empty!']);
        }
        ksort($product_cart);
        return view('payments.index',['products'=>$product_cart]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        return view('payments.status');
    }

    /**
     * @param PaymentValidation $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(PaymentValidation $request)
    {

        $client = $this->clientCreate();


        /**
         * In case payment could not be processed exception will be thrown.
         * In this example only Declined and ValidationFailed exceptions are handled. However there is more of them.
         * See Error Codes section for detailed list.
         */
        DB::beginTransaction();
        try {

            // Save Order
            $dbOrder['total'] = $request->total;
            $dbOrder['user_id'] = Auth::id();
            $order = $this->orderRepository->save($dbOrder);

            $method = new CardinityPayment\Create([
                'amount' => floatval($request->total),
                'currency' => 'EUR',
                'settle' => false,
                'description' => 'some description',
                'order_id' => strval('00'.$order->id),
                'country' => 'LT',
                'payment_method' => CardinityPayment\Create::CARD,
                'payment_instrument' => [
                    'pan' => $request->card,
                    'exp_year' => +$request->year,
                    'exp_month' => +$request->month,
                    'cvc' => $request->ccv,
                    'holder' => $request->card_holder
                ],
            ]);


            //Save Order Details
            foreach ($request->product as $productId => $quantity):
                $dbOrderDetail['order_id'] = $order->id;
                $dbOrderDetail['product_id'] = $productId;
                $product = $this->productRepository->findOne($productId);
                $dbOrderDetail['unit_price'] = $product->price;
                $dbOrderDetail['quantity'] = $quantity;
                $this->orderDetailsRepository->save($dbOrderDetail);
            endforeach;

            $payment = $client->call($method);
            $status = $payment->getStatus();

            //Save Payments
            $dbPayment['cardinity_payment_id'] = $payment->getId();
            $dbPayment['user_id'] = Auth::id();
            $dbPayment['order_id'] = $order->id;
            $dbPayment['paid_amount'] = $request->total;
            $dbPayment['status'] = $status ;
            $this->paymentRepository->save($dbPayment);

            if($status == 'pending') {
                // Retrieve information for 3D-Secure authorization
                $url = $payment->getAuthorizationInformation()->getUrl();
                $data = $payment->getAuthorizationInformation()->getData();
                DB::commit();
                return redirect(route('payment_3dsecure_form',['url' => $url,'data'=>$data,'md'=>$payment->getId()]));
            }

            $order_update["status"] = $status;
            $this->orderRepository->update($order, $order_update);
            $this->repository->delete(0,true);
            DB::commit();
            $this->redirectOnSuucess("successfully","Payment successfully paid.");
            return redirect(route('product_list'))->with(['message' => "Payment successfully paid."]);

        } catch (Exception\Declined $exception) {
            DB::rollBack();
            $payment = $exception->getResult();
            $message = $payment->getStatus(); // value will be 'declined'
            $errors = $exception->getErrors(); // list of errors occured
            $errors = collect($errors)->toJson();
            $this->redirectOnFail($message,$errors);
            return redirect(route('payment_form'))->with(['paymentStatus'=>$message,'paymentError'=>$errors]);
        }catch (Exception\ValidationFailed $exception) {
            DB::rollBack();
            $payment = $exception->getResult();
            $message = $payment->getStatus(); // value will be 'declined'
            $errors = $exception->getErrors(); // list of errors occured
            $errors = collect($errors)->toJson();
            $this->redirectOnFail($message,$errors);
            return redirect(route('payment_form'))->with(['paymentStatus'=>$message,'paymentError'=>$errors]);
        }catch (\Exception $exception){
            DB::rollBack();
            $error = $exception->getMessage(); // list of errors occured
            $this->redirectOnFail("Error",$error);
            return redirect(route('payment_form'))->with(['paymentStatus'=>"Error",'paymentError'=>$error]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Payment $payment
     * @return Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|Response|View
     */
    public function edit(Request $request)
    {
        $data = $request->data;
        $url = $request->url;
        $md = $request->md;
        return view('payments.3dsecure',['data' =>$data,'url'=>$url,'md'=>$md]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function update(Request $request)
    {
        $client = $this->clientCreate();
        $method = new CardinityPayment\Finalize($request->MD, $request->PaRes);
        try {
            $payment = $client->call($method);
            $this->paymentFinalize($payment);
            $this->redirectOnSuucess("successfully","Payment successfully paid.");
            return redirect(route('product_list'))->with(['message' => "Payment successfully paid."]);
        }catch (Exception\Declined $exception) {
            $payment = $exception->getResult();
            $message = $payment->getStatus(); // value will be 'declined'
            $errors = $exception->getErrors(); // list of errors occured
            $this->paymentFinalize($payment);
            $errors = collect($errors)->toJson();
//            dd($message,$errors,Auth::user());
            $this->redirectOnFail($message,$errors);
            return redirect('/')->with(['paymentStatus'=>$message,'paymentError'=>$errors]);
        } catch (Exception\ValidationFailed $exception) {
            $payment = $exception->getResult();
            $message = $payment->getStatus(); // value will be 'declined'
            $errors = $exception->getErrors(); // list of errors occured
            $this->paymentFinalize($payment);
            $errors = collect($errors)->toJson();
//            dd($message,$errors,Auth::user());
            $this->redirectOnFail($message,$errors);
            return redirect('/')->with(['paymentStatus'=>$message,'paymentError'=>$errors]);
        }catch (\Exception $exception){
            $this->removeOrderAndPaymentLocalDetailsOnUnexpectedError($request);
            $error = $exception->getMessage(); // list of errors occured
//            dd($error);
            $this->redirectOnFail("Error",$error);
            return redirect('/')->with(['paymentStatus'=>"Error",'paymentError'=>$error]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param $payment
     * @return void
     */
    private function paymentFinalize($payment)
    {
        $dbPayment = $this->paymentRepository->findOneBy(['cardinity_payment_id' => $payment->getId()]);
        $arr['status'] = $payment->getStatus();
        $this->paymentRepository->update($dbPayment,$arr);
        $dborder = $this->orderRepository->findOne(+$payment->getorderId());
        $this->orderRepository->update($dborder,$arr);
        $this->repository->delete(0, true);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @return void
     */
    private function removeOrderAndPaymentLocalDetailsOnUnexpectedError(Request  $request)
    {
        $dbPayment = $this->paymentRepository->findOneBy(['cardinity_payment_id' => $request->MD]);
        $arr['status'] = 'failed';
        $this->paymentRepository->update($dbPayment,$arr);
    }

    private function redirectOnFail($message, $errors){
        echo  \view('payments.payment_status')->with(['message'=>$message,'errors'=>$errors]);
        dd();
    }
    private function redirectOnSuucess($message, $errors){
        echo  \view('payments.payment_status')->with(['message'=>$message,'errors'=>$errors]);
        dd();

    }



    private function clientCreate()
    {
        return Client::create([
            'consumerKey' => env('CARDINITY_KEY','test_jhcm1kuiowcs2s9dj03vryr4v8yf4e'),
            'consumerSecret' => env('CARDINITY_SECRET','uczqtwmhh2dj1m2vkulspssqisqc2qzjo8v23auqssux4opvag'),
        ]);
    }
}
