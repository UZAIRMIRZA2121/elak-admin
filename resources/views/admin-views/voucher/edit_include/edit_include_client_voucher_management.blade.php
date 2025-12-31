<!-- Step 1: Select Voucher Type -->
<div class="section-card rounded p-4 mb-4">
    <h2 class="fw-semibold h5 mb-4">
        <i class="fas fa-bullseye me-2"></i> Step 1: Select Voucher Type
    </h2>
    <div class="row g-3">
        @php $i = 1; @endphp
        @foreach (\App\Models\VoucherType::get() as $voucherType)
            <div class="col-md-3">
                <div class="voucher-card border rounded py-4 text-center h-70
                    {{ ($i == 1 && (Request::is('admin/Voucher/add-new') && $voucherType->name == 'Delivery/Pickup')) ? 'selected' : '' }}
                    {{ ($i == 2 && (Request::is('admin/Voucher/add-new-store') && $voucherType->name == 'In-Store')) ? 'selected' : '' }}
                    {{ ($i == 3 && (Request::is('admin/Voucher/add-flat-discount') && $voucherType->name == 'Flat discount')) ? 'selected' : '' }}
                    {{ ($i == 4 && (Request::is('admin/Voucher/add-gift') && $voucherType->name == 'Gift')) ? 'selected' : '' }}
                    {{ (isset($product) && $product->voucher_ids == $voucherType->name) ? 'selected' : '' }}
                    "
                    onclick="section_one('{{ $i }}', '{{ $voucherType->id }}', '{{ $voucherType->name }}')"
                    data-value="{{ $voucherType->name }}">

                    <div class="display-4 mb-2">
                        <img src="{{ asset('public/'.$voucherType->logo) }}" alt="{{ $voucherType->name }}" style="width: 40px;" />
                    </div>

                    <h6 class="fw-semibold">{{ $voucherType->name }}</h6>
                    <small class="text-muted">{{ $voucherType->desc }}</small>
                </div>
            </div>
            @php $i++; @endphp
        @endforeach
    </div>
</div>

