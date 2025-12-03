@extends('layouts.admin.app')

@section('title', translate('Item Preview'))

@push('css_or_js')
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
                                <td>{{ $product->category_ids_display ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Sub Category IDs') }}</th>
                                <td>{{ $product->sub_category_ids_display ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Branch IDs') }}</th>
                                <td>{{ $product->branch_ids_display ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Tags') }}</th>
                                <td>{{ $product->tags_display ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('How & Conditions') }}</th>
                                <td>{{ $product->conditions_display ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Terms & Conditions') }}</th>
                                <td>{{ $product->terms_display ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Client Section -->
            @if($product->clients_section && $product->clients_section != '[]')
            <div class="row mt-3">
                <div class="col-12">
                    <h5>{{ translate('Client Section') }}</h5>
                    <pre class="bg-light p-3" style="max-height: 200px; overflow-y: auto;">{{ $product->clients_display ?? 'N/A' }}</pre>
                </div>
            </div>
            @endif
            
            <!-- Products -->
            @if($product->product && $product->product != '[]')
            <div class="row mt-3">
                <div class="col-12">
                    <h5>{{ translate('Products') }}</h5>
                    <pre class="bg-light p-3" style="max-height: 200px; overflow-y: auto;">{{ $product->products_display ?? 'N/A' }}</pre>
                </div>
            </div>
            @endif
            
            <!-- Product B -->
            @if($product->product_b && $product->product_b != '[]')
            <div class="row mt-3">
                <div class="col-12">
                    <h5>{{ translate('Product B') }}</h5>
                    <pre class="bg-light p-3" style="max-height: 200px; overflow-y: auto;">{{ json_decode($product->product_b, JSON_PRETTY_PRINT) ?? 'N/A' }}</pre>
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
