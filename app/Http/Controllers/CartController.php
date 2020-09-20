<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\AbstractSessionRepository;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use function Symfony\Component\VarDumper\Dumper\esc;

class CartController extends Controller
{
    private $repository;
    private $productRepository;

    public function __construct(CartRepository $repository, ProductRepository $productRepository)
    {
        $this->repository = $repository;
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
        return view('carts.index',['products'=>$product_cart]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $product_id = $request->product_id;
        $cart_product = $this->repository->find($product_id);

        if (is_null($cart_product)){
            $product = $this->productRepository->findOne($product_id);
            $cart = new \stdClass();
            $cart->id = $product->id;
            $cart->name = $product->name;
            $cart->price = $product->price;
            $cart->quantity = 1;
            $cart->product_image = $product->product_image;
            $this->repository->save($product_id,$cart);
        }else{
            if ($cart_product->quantity >= 2){
                return back()->with(['errorMessage' => 'Stock Limited. You can add up to 2 items of each product!']);

            }
            $cart_product->quantity = $cart_product->quantity + 1;
            $this->repository->save($product_id,$cart_product);
        }
        return back()->with(['message' => 'Product added to cart']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $products = $this->repository->find($id);
        if ($products->quantity > 1){
            $products->quantity = $products->quantity - 1;
            $this->repository->update($id,$products);
            return back()->with(['message' => 'One Product removed from cart']);
        }
        $resposne = $this->repository->delete($id);
        if ($resposne){
            return back()->with(['message' => 'Product removed from cart']);
        }
        return back()->with(['errorMessage' => 'Product not found in cart']);

    }
}
