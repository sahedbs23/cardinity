<x-app-layout>
    <x-slot name="header">
        <span class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Carts') }}
        </span>
        <a style="float: right" href="{{route('payment_form')}}">{{ __('Proceed to payment') }}</a>

    </x-slot>
    @if(Session::has('errorMessage'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Something wrong!</strong>
            <span class="block sm:inline">{{ Session::get('errorMessage') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg onclick="console.log(this.parent.parent)" class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
        </div>
    @endif
    @if(Session::has('message'))
        <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert">
            <div class="flex">
                <div>
                    <p class="font-bold">{{ Session::get('message') }}</p>
                    <p ><a class="text-sm" href="{{route('cart_products')}}">Checkout cart</a></p>
                </div>
            </div>
        </div>
    @endif
        <table class="table-fixed container mx-auto">
            <thead>
            <tr>
                <th class="px-4 py-2">SL#</th>
                <th class="px-4 py-2">Product Image</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Quantity</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Total</th>
                <th class="px-4 py-2">Action</th>

            </tr>
            </thead>
            <tbody>
            @php $total = 0; @endphp
                @foreach($products as $key =>$product)
                    <tr>
                        <td class="border px-4 py-2">{{$key}}</td>
                        <td class="border px-4 py-2">
                            <img style="height: 75px;width: 100px;" src="{{$product->product_image}}" alt="{{$product->name}}">
                        </td>
                        <td class="border px-4 py-2">{{$product->name}}</td>
                        <td class="border px-4 py-2">{{$product->quantity}}</td>
                        <td class="border px-4 py-2">{{$product->price}}</td>
                        <td class="border px-4 py-2 text-left">
                            @php $total += $product->quantity * $product->price; @endphp
                            {{$product->quantity * $product->price}}
                        </td>
                        <td class="border px-4 py-2">
{{--                            <table>--}}
{{--                                <tbody>--}}
{{--                                    <tr>--}}
{{--                                        <td>--}}
{{--                                            <form action="{{route('add_to_cart')}}" method="POST">--}}
{{--                                                @csrf--}}
{{--                                                <input type="hidden" name="product_id" value="{{$product->id}}">--}}
{{--                                                <button type="submit" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"--}}
{{--                                                        title="Add one more to bag"--}}
{{--                                                        style="transform: rotate(-90deg);"--}}
{{--                                                        value="1" > >--}}
{{--                                                </button>--}}
{{--                                            </form>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td>--}}
{{--                                                &nbsp;&nbsp;&nbsp;2--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td>--}}
{{--                                            <form action="{{route('add_to_cart')}}" method="POST">--}}
{{--                                                @csrf--}}
{{--                                                <input type="hidden" name="product_id" value="{{$product->id}}">--}}
{{--                                                <button type="submit" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"--}}
{{--                                                        title="Remove one from bag"--}}
{{--                                                        style="transform: rotate(90deg);"--}}
{{--                                                        value="1" > >--}}
{{--                                                </button>--}}
{{--                                            </form>--}}

{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
                            @if($product->quantity < 2)
                            <form action="{{route('add_to_cart')}}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{$product->id}}">
                                <div class="flex flex-wrap">
                                    <div class="w-full  mb-4 text-center">
                                        <button type="submit"
                                                class=" bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
                                                value="1" >
                                            Add One More
                                        </button>
                                    </div>

                                </div>
                            </form>
                            @endif
                            <form action="{{route('remove_from_cart',$product->id)}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="flex flex-wrap">
                                    <div class="w-full  mb-4 text-center">
                                        <button type="submit"
                                                class=" bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded"
                                                value="1" >
                                            Remove one
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="5" class="border px-4 py-2 text-right">Total</th>
                <th colspan="2" class="border px-4 py-2 text-left">{{$total}}</th>
            </tr>
            </tfoot>
        </table>

</x-app-layout>
