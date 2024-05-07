<div>
    @foreach ($tellbot_services->message as $key)
    <div class="row service-row">
        <div style="font-size: 11px" class="col-5 service-name">
            {{ $key->name }}
        </div>
        <div style="font-size: 11px" class="col">
            @php $cost = (int) $get_rate2 * (int) $key->price + (int) $margin2 @endphp
            <strong>N{{ number_format($cost, 2) }}</strong>
        </div>


        <div class="col">
        <a wire:click="tellabot_order_now('{{ $key->name }}', '{{ $key->price }}', '{{ $cost }}')">
                <i class="fa fa-shopping-bag"></i>
            </a>
            
        </div>


        <hr style="border-color: #cccccc" class=" my-2">
    </div>
    @endforeach
</div>