<x-app-layout>
    <x-slot name="header">
        <span class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payments') }}
        </span>

    </x-slot>
    <div >
        <p>
            If your browser does not start loading the page,
            press the button below.
            You will be sent back to this site after you
            authorize the transaction.
        </p>
        <form id="ThreeDForm" name="ThreeDForm" method="POST" action="{{$url}}" onload="document.getElementById('submit').click()">
            <button id="submit" type=submit>Click Here</button>
            <input type="hidden" name="PaReq" value="{{$data}}" />
            <input type="hidden" name="TermUrl" value="{{route('payment_3dsecure_callback')}}" />
            <input type="hidden" name="MD" value="{{$md}}" />
        </form>
    </div>

</x-app-layout>

