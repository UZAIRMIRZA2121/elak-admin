@extends('layouts.admin.app')

@section('title', translate('Item Preview'))

@push('css_or_js')
<style>
    .json-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .segment-badge {
        display: inline-block;
        margin: 2px;
        padding: 3px 8px;
        background: #e9ecef;
        border-radius: 4px;
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Voucher Details Card -->
    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title">{{ translate('Voucher Details') }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="40%">{{ translate('Voucher Title') }}</th>
                                <td>{{ $product->voucher_title ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Voucher Type') }}</th>
                                <td>{{ ucfirst($product->voucher_type) ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Bundle Type') }}</th>
                                <td>{{ ucfirst($product->bundle_type) ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Voucher IDs') }}</th>
                                <td>{{ $product->voucher_ids ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Valid Until') }}</th>
                                <td>{{ $product->valid_until ? \Carbon\Carbon::parse($product->valid_until)->format('d M Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Food & Product Type') }}</th>
                                <td>{{ $product->food_and_product_type ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Required Quantity') }}</th>
                                <td>{{ $product->required_quantity ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="40%">{{ translate('Category IDs') }}</th>
                                <td>
                                    @if(isset($product->categories) && $product->categories->count() > 0)
                                        {{ $product->categories->pluck('name')->implode(', ') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ translate('Sub Category IDs') }}</th>
                                <td>
                                    @if(isset($product->sub_categories) && $product->sub_categories->count() > 0)
                                        {{ $product->sub_categories->pluck('name')->implode(', ') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ translate('Branch IDs') }}</th>
                                <td>
                                    @if(isset($product->branches) && $product->branches->count() > 0)
                                        {{ $product->branches->pluck('name')->implode(', ') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ translate('Tags') }}</th>
                                <td>{{ $product->tags_display ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('How & Conditions') }}</th>
                               <td>
                                    @php
                                        $ids = array_filter(explode(',', $product->how_and_condition_ids));
                                    @endphp

                                    @if(count($ids) > 0)
                                        {{ implode(', ', $ids) }}
                                    @else
                                        N/A
                                    @endif
                                </td>

                            </tr>
                            <tr>
                                <th>{{ translate('Terms & Conditions') }}</th>
                                <td>
                                    @if(!empty($product->term_and_condition_ids))
                                        {{ implode(', ', (array) explode(',', $product->term_and_condition_ids)) }}
                                    @else
                                        N/A
                                    @endif
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Product Details -->
            @if(isset($product->product_details) && $product->product_details->count() > 0)
            <div class="row mt-3">
                <div class="col-12">
                    <h5>{{ translate('Products') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ translate('Product ID') }}</th>
                                    <th>{{ translate('Product Name') }}</th>
                                    <th>{{ translate('Price') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->product_details as $prod)
                                <tr>
                                    <td>{{ $prod->id }}</td>
                                    <td>{{ $prod->name ?? 'N/A' }}</td>
                                    <td>{{ $prod->price ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Client Section -->
            @if($product->clients_section && $product->clients_section != '[]')
            <div class="row mt-3">
                <div class="col-12">
                    <h5>{{ translate('Client Section') }}</h5>
                    <div class="table-responsive">
                        @php
                            try {
                                $clients = json_decode($product->clients_section, true);
                                if(is_array($clients) && count($clients) > 0) {
                        @endphp
                        <table class="table table-bordered json-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Client ID') }}</th>
                                    <th>{{ translate('App Name ID') }}</th>
                                    <th>{{ translate('App Name') }}</th>
                                    <th>{{ translate('Segments') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $index => $client)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $client['client_id'] ?? 'N/A' }}</td>
                                    <td>{{ $client['app_name_id'] ?? 'N/A' }}</td>
                                    <td>{{ $client['app_name'] ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($client['segment']) && is_array($client['segment']))
                                            @foreach($client['segment'] as $segment)
                                                <span class="segment-badge">{{ $segment }}</span>
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @php
                                } else {
                                    echo '<p class="text-muted">No client data found.</p>';
                                }
                            } catch (Exception $e) {
                                echo '<pre class="bg-light p-3">' . $product->clients_section . '</pre>';
                            }
                        @endphp
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Product JSON Data -->
            @if($product->product && $product->product != '[]')
            <div class="row mt-3">
                <div class="col-12">
                    <h5>{{ translate('Products Data') }}</h5>
                    <div class="table-responsive">
                        @php
                            try {
                                $products = json_decode($product->product, true);
                                if(is_array($products) && count($products) > 0) {
                        @endphp
                        <table class="table table-bordered json-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Product ID') }}</th>
                                    <th>{{ translate('Product Name') }}</th>
                                    <th>{{ translate('Base Price') }}</th>
                                    <th>{{ translate('Variations') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $prod)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $prod['product_id'] ?? 'N/A' }}</td>
                                    <td>{{ $prod['product_name'] ?? 'N/A' }}</td>
                                    <td>{{ $prod['base_price'] ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($prod['variations']) && is_array($prod['variations']))
                                            {{ implode(', ', $prod['variations']) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @php
                                } else {
                                    echo '<p class="text-muted">No product data found.</p>';
                                }
                            } catch (Exception $e) {
                                echo '<pre class="bg-light p-3">' . $product->product . '</pre>';
                            }
                        @endphp
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Product B -->
            @if($product->product_b && $product->product_b != '[]')
            <div class="row mt-3">
                <div class="col-12">
                    <h5>{{ translate('Product B') }}</h5>
                    <div class="table-responsive">
                        @php
                            try {
                                $productB = json_decode($product->product_b, true);
                                if(is_array($productB) && count($productB) > 0) {
                        @endphp
                        <table class="table table-bordered json-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Data') }}</th>
                                    <th>{{ translate('Value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productB as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td colspan="2">
                                        @if(is_array($item))
                                            @foreach($item as $key => $value)
                                                <div><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                    @if(is_array($value))
                                                        {{ implode(', ', $value) }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            {{ $item }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @php
                                } else {
                                    echo '<p class="text-muted">No product B data found.</p>';
                                }
                            } catch (Exception $e) {
                                echo '<pre class="bg-light p-3">' . $product->product_b . '</pre>';
                            }
                        @endphp
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <!-- End Voucher Details Card -->
</div>

{{-- Add Quantity Modal --}}
<div class="modal fade update-quantity-modal" id="update-quantity" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-0">
                <form action="{{ route('admin.Voucher.stock-update') }}" method="post">
                    @csrf
                    <div class="mt-2 rest-part w-100"></div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" data-dismiss="modal" aria-label="Close"
                            class="btn btn--reset">{{ translate('cancel') }}</button>
                        <button type="submit" id="submit_new_customer"
                            class="btn btn--primary">{{ translate('update_stock') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    "use strict";
    $(".status_form_alert").on("click", function(e) {
        const id = $(this).data('id');
        const message = $(this).data('message');
        e.preventDefault();
        Swal.fire({
            title: '{{ translate('messages.are_you_sure') }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{ translate('messages.no') }}',
            confirmButtonText: '{{ translate('messages.yes') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#' + id).submit()
            }
        })
    })

    $('.update-quantity').on('click', function() {
        let val = $(this).data('id');
        $.get({
            url: '{{ route('admin.Voucher.get_stock') }}',
            data: {
                id: val
            },
            dataType: 'json',
            success: function(data) {
                $('.rest-part').empty().html(data.view);
                update_qty();
            },
        });
    })

    function update_qty() {
        let total_qty = 0;
        let qty_elements = $('input[name^="stock_"]');
        for (let i = 0; i < qty_elements.length; i++) {
            total_qty += parseInt(qty_elements.eq(i).val());
        }
        if (qty_elements.length > 0) {
            $('input[name="current_stock"]').attr("readonly", 'readonly');
            $('input[name="current_stock"]').val(total_qty);
        } else {
            $('input[name="current_stock"]').attr("readonly", false);
        }
    }
</script>
@endpush