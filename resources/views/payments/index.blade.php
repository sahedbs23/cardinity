<x-app-layout>
    <x-slot name="header">
        <span class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payments') }}
        </span>

    </x-slot>
    @if(Session::has('errorMessage'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-2 py-3 rounded relative" role="alert">
            <strong class="font-bold">Something wrong!</strong>
            <span class="block sm:inline">{{ Session::get('errorMessage') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-2 py-3">
            <svg onclick="console.log(this.parent.parent)" class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
        </div>
    @endif
    @if(Session::has('message'))
        <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-2 py-3 shadow-md" role="alert">
            <div class="flex">
                <div>
                    <p class="font-bold">{{ Session::get('message') }}</p>
                    <p ><a class="text-sm" href="{{route('cart_products')}}">Checkout cart</a></p>
                </div>
            </div>
        </div>
    @endif

    @if(Session::has('paymentError') &&Session::has('paymentStatus'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">{{ Session::get('paymentStatus') }}</strong><br>
            <span class="block sm:inline">{{ Session::get('paymentError') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg  class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
        </div>
    @endif
    <form action="{{route('pay_payment')}}" method="POST">
        @csrf
        <div class="flex mb-4">
        <div class="w-1/2  h-12">
            <table class="table-fixed container mx-auto">
                <thead>
                <tr>
                    <th class="px-2 py-2">SL#</th>
                    <th class="px-2 py-2">Product Image</th>
                    <th class="px-2 py-2">Name</th>
                    <th class="px-2 py-2">Quantity</th>
                    <th class="px-2 py-2">Price</th>
                    <th class="px-2 py-2">Total</th>

                </tr>
                </thead>
                <tbody>
                @php $total = 0; @endphp
                @foreach($products as $key =>$product)
                    <input type="hidden" name="product[{{$product->id}}]" value="{{$product->quantity}}">
                    <tr>
                        <td class="border px-2 py-2">{{$key}}</td>
                        <td class="border px-2 py-2">
                            <img style="height: 75px;width: 100px;" src="{{$product->product_image}}" alt="{{$product->name}}">
                        </td>
                        <td class="border px-2 py-2">{{$product->name}}</td>
                        <td class="border px-2 py-2">{{$product->quantity}}</td>
                        <td class="border px-2 py-2">{{$product->price}}</td>
                        <td class="border px-2 py-2 text-left">
                            @php $total += $product->quantity * $product->price; @endphp
                            {{$product->quantity * $product->price}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5" class="border px-2 py-2 text-right">Total</th>
                    <th  class="border px-2 py-2 text-left">
                        {{$total}}
                        <input type="hidden" name="total" value="{{$total}}">
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="w-1/2 h-12 ">
            <br>
            <div class="px-4 py-2">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/1 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="card_holder">
                                Card Holder
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                  name="card_holder" id="card_holder" type="text" placeholder="Mike Dough"  required>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/1 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="card">
                                Card number
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                  name="card" id="card" type="text" placeholder="xxxxxxxxxxxxxxxx" minlength="16" maxlength="16" pattern="[0-9]{16}" required>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-2">
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="month">
                                Expiration
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                   id="month" name="month" type="number" placeholder="09" min="1" max="12" required>
                        </div>
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="year">
                                &nbsp;
                            </label>
                            <div class="relative">
                                <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                       name="year" id="year" type="number" placeholder="2021" min="2020" max="2030" required>
                            </div>
                        </div>
                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="ccv">
                                CCV
                            </label>
                            <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                   name="ccv" id="ccv" type="text" placeholder="3434" required minlength="3" maxlength="4" pattern="[0-9]{3,4}">
                        </div>
                    </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/1 px-4 py-6 mb-6 md:mb-0">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Submit Payment') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </form>

</x-app-layout>
