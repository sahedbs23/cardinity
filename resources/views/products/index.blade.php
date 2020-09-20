<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}

        </h2>
    </x-slot>
    @if(Session::has('errorMessage'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Something wrong!</strong>
        <span class="block sm:inline">{{ Session::get('errorMessage') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg  class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path
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
    @foreach($products->chunk(3) as $chunk)
    <div class="flex mb-4">
        @foreach ($chunk as $product)
            <div class="w-1/3 ">
                <div class="max-w-sm w-full lg:max-w-full lg:flex" style="padding: 10px">
                    <div class="h-48 lg:h-auto lg:w-48 flex-none bg-cover rounded-t lg:rounded-t-none lg:rounded-l text-center overflow-hidden" style="background-image: url('{{$product->product_image}}')" title="{{$product->name}}">
                    </div>
                    <div class="border-r border-b border-l border-gray-400 lg:border-l-0 lg:border-t lg:border-gray-400 bg-white rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal">
                        <div class="mb-8">
                            <p class="text-sm text-gray-600 flex items-center">
                                <svg class="fill-current text-gray-500 w-3 h-3 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M4 8V6a6 6 0 1 1 12 0v2h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z" />
                                </svg>
                                Limited edition
                            </p>
                            <p class="text-sm text-gray-600 flex items-center">
                                <svg class="fill-current text-gray-500 w-3 h-3 mr-2" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 435 435" style="enable-background:new 0 0 435 435;" xml:space="preserve">
<path d="M235.642,405c-46.281,0-83.933-37.652-83.933-83.932V270.21h159.962v-30H151.709v-40h159.962v-30H151.709v-56.278
	c0-46.281,37.652-83.932,83.933-83.932c46.28,0,83.932,37.652,83.932,83.932h30C349.574,51.11,298.464,0,235.642,0
	c-62.823,0-113.933,51.11-113.933,113.932v56.278h-35v30h35v40H85.426v30h36.284v50.857c0,62.823,51.11,113.932,113.933,113.932
	c62.822,0,113.932-51.11,113.932-113.932h-30C319.574,367.348,281.922,405,235.642,405z"/>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
                                    <g>
                                    </g>
</svg>

                                {{$product->price}}
                            </p>
                            <div class="text-gray-900 font-bold text-xl mb-2">{{$product->name}}</div>
                            <p class="text-gray-700 text-base">{{$product->description}}</p>
                        </div>
                            <form action="{{route('add_to_cart')}}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{$product->id}}">
                                <div class="flex flex-wrap">
                                    <div class="w-full  mb-4 text-center">
                                        <button type="submit"
                                                @if( is_array($cart) && isset($cart[$product->id]) && ($cart[$product->id]->quantity >= 2)  )
                                                disabled
                                                title="You reached maximum limit. You can add up to 2 items of each product!"
                                                class=" bg-transparent bg-gray-500 text-white py-2 px-4 border border-gray-500 border-transparent rounded"
                                                style="cursor:help;"
                                                @else
                                                class=" bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
                                                @endif
                                                value="1" >
                                            Add to cart
                                        </button>
                                    </div>

                                </div>
                            </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endforeach

</x-app-layout>
