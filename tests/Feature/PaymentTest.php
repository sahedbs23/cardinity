<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $user = User::first();
        $response =  $this->actingAs($user)
            ->get('/payment');
        if ($response->getStatusCode() === 200) {
            $response->assertViewHas('products','array|null');
        }else{
            $response->assertRedirect(route('product_list'));
        }
    }

    public function testStore3DEnrolledAuthorizationFailed()
    {
        $user = User::first();
        $product = Product::first();

        $input['card_holder'] ='Sahed';
        $input['product'][$product->id] =1;
        $input['total'] =125.34;
        $input['card'] ="4200000000000000";
        $input['month'] =12;
        $input['year'] =2021;
        $input['ccv'] ="456";

        $cart = new \stdClass();
        $cart->id = $product->id;
        $cart->name = $product->name;
        $cart->price = $product->price;
        $cart->quantity = 1;

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$cart]])
            ->POST('/payment',$input);
        $response->assertStatus(302);
    }
}
