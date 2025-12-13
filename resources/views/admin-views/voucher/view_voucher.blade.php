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
                                <th>{{ translate('Voucher IDs') }}</th>
                                <td>{{ $product->id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th width="40%">{{ translate('Voucher Title') }}</th>
                                <td>{{ $product->name ?? 'N/A' }}</td>
                            </tr>
                              <tr>
                                <th>{{ translate('Description') }}</th>
                                <td>{{ $product->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Voucher Type') }}</th>
                                <td>{{ ucfirst($product->type) ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ translate('Bundle Type') }}</th>
                                <td>{{ ucfirst($product->bundle_type) ?? 'N/A' }}</td>
                            </tr>
                            <?php if(isset($product->valid_until)) { ?> 
                            <tr>
                                <th>{{ translate('Valid Until') }}</th>
                                <td>{{ $product->valid_until ? \Carbon\Carbon::parse($product->valid_until)->format('d M Y') : 'N/A' }}</td>
                            </tr>
                            <?php }?>
                             <tr>
                                <th>{{ translate('Price') }}</th>
                                <td>{{ ucfirst($product->price) ?? 'N/A' }}</td>
                            </tr>
                             <tr>
                                <th>{{ translate('Discount') }}</th>
                                <td>{{ ucfirst($product->discount) ?? '0' }} %</td>
                            </tr>
                             <tr>
                                <th>{{ translate('Discount Type') }}</th>
                                <td>{{ ucfirst($product->discount_type) ?? 'N/A' }}</td>
                            </tr>
                            @if(!empty($product->required_quantity) && $product->required_quantity > 0)
                            <tr>
                                <th>{{ translate('Required Quantity') }}</th>
                                <td>{{ $product->required_quantity }}</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tbody>
                           <tr>
                                <th width="40%">{{ translate('Categories') }}</th>
                                <td>
                                    @if($product->categories && $product->categories->isNotEmpty())
                                        {{ $product->categories->pluck('name')->implode(', ') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                               <?php if(isset($product->sub_categories)) { ?> 
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
                              <?php }?>
                          <tr>
                            <th>{{ translate('Branches') }}</th>
                            <td>
                                @if(!empty($product->branches) && $product->branches->count() > 0)
                                    {{ $product->branches->pluck('name')->implode(', ') }}
                                @else
                                    N/A
                                @endif
                            </td>
                         </tr>

                            <?php if(isset($product->valid_until)) { ?> 
                        <tr>
                            <th>{{ translate('Tags') }}</th>
                            <td>{{ $product->tags_ids ?? 'N/A' }}</td>
                        </tr>
                              <?php }?>
                          <tr>
                                <th>{{ translate('How to Work') }}</th>
                                <td>
                                 @if($product->how_conditions && $product->how_conditions->count() > 0)
                                    @foreach($product->how_conditions as $condition)
                                        <div class="mb-2">
                                            <strong>{{ $condition->guide_title }}</strong>

                                            @php($sections = $condition->sections)

                                            @if(is_array($sections))
                                                <ul class="mt-1">
                                                    @foreach($sections as $section)
                                                        <li>
                                                            <strong>{{ $section['title'] ?? '' }}</strong>

                                                            @if(!empty($section['steps']) && is_array($section['steps']))
                                                                <ol>
                                                                    @foreach($section['steps'] as $step)
                                                                        <li>{{ $step }}</li>
                                                                    @endforeach
                                                                </ol>
                                                            @endif

                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    N/A
                                @endif

                                </td>
                            </tr>

                           <tr>
                            <th>{{ translate('Terms & Conditions') }}</th>
                            <td>
                              @if($product->terms_conditions && $product->terms_conditions->count() > 0)

                                @foreach($product->terms_conditions as $term)
                                    <div class="mb-3 p-2 border rounded">

                                        {{-- Title --}}
                                        <strong>{{ $term->baseinfor_condition_title }}</strong>

                                        {{-- Description --}}
                                        @if(!empty($term->baseinfor_description))
                                            <p class="mb-1">{{ $term->baseinfor_description }}</p>
                                        @endif

                                        {{-- Days --}}
                                        @if(is_array($term->timeandday_config_days))
                                            <p>
                                                <strong>Days:</strong>
                                                {{ implode(', ', $term->timeandday_config_days) }}
                                            </p>
                                        @endif

                                        {{-- Time Range --}}
                                        <p>
                                            <strong>Time:</strong>
                                            {{ $term->timeandday_config_time_range_from }}
                                            -
                                            {{ $term->timeandday_config_time_range_to }}
                                        </p>

                                        {{-- Validity --}}
                                        <p>
                                            <strong>Valid:</strong>
                                            {{ $term->timeandday_config_valid_from_date }}
                                            to
                                            {{ $term->timeandday_config_valid_until_date }}
                                        </p>

                                        {{-- Holiday Restrictions --}}
                                        @if(is_array($term->holiday_occasions_holiday_restrictions))
                                            <p>
                                                <strong>Holiday Restrictions:</strong>
                                                {{ implode(', ', $term->holiday_occasions_holiday_restrictions) }}
                                            </p>
                                        @endif

                                        {{-- Special Occasions --}}
                                        @if(is_array($term->holiday_occasions_special_occasions))
                                            <p>
                                                <strong>Special Occasions:</strong>
                                                {{ implode(', ', $term->holiday_occasions_special_occasions) }}
                                            </p>
                                        @endif

                                        {{-- Usage Limits --}}
                                        <p>
                                            <strong>Usage Limit:</strong>
                                            {{ $term->usage_limits_limit_per_user }}
                                            /
                                            {{ ucfirst($term->usage_limits_period) }}
                                        </p>

                                        {{-- Location --}}
                                        @if(is_array($term->location_availability_venue_types))
                                            <p>
                                                <strong>Available At:</strong>
                                                {{ implode(', ', $term->location_availability_venue_types) }}
                                            </p>
                                        @endif

                                        {{-- Restrictions --}}
                                        @if(is_array($term->restriction_polices_restriction_type))
                                            <p>
                                                <strong>Restrictions:</strong>
                                                {{ implode(', ', $term->restriction_polices_restriction_type) }}
                                            </p>
                                        @endif

                                    </div>
                                @endforeach

                            @else
                                N/A
                            @endif

                            </td>
                          </tr>
                          
                        </tbody>
                    </table>
                </div>
                  @if(isset($product->product_details) && $product->product_details->count() > 0)
                    <div class="col-md-12 mt-3">
                        <h5>{{ translate('Products') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ translate('ID') }}</th>
                                        <th>{{ translate('Name') }}</th>
                                        <!-- <th>{{ translate('Image') }}</th> -->
                                        <th>{{ translate('Price') }}</th>
                                        <th>{{ translate('Description') }}</th>
                                        <th>{{ translate('Stock') }}</th>
                                        <th>{{ translate('Organic') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->product_details as $prod)
                                        <tr>
                                            <td>{{ $prod->id }}</td>
                                            <td>{{ $prod->name ?? 'N/A' }}</td>
                                            <!-- <td>
                                                 @php($images = is_string($prod->images ?? []) ? json_decode($prod->images, true) : ($prod->images ?? []))


                                                @if(is_array($images) && count($images) > 0)
                                                    <img src="{{ asset('storage/'.$images[0]['img']) }}" alt="{{ $prod->name }}" width="50" height="50">
                                                @else
                                                    N/A
                                                @endif
                                            </td> -->
                                            <td>{{ $prod->price ?? 'N/A' }}</td>
                                            <td>{{ $prod->description ?? 'N/A' }}</td>
                                            <td>{{ $prod->stock ?? 'N/A' }}</td>
                                            <td>{{ $prod->organic ? 'Yes' : 'No' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if(isset($product->product_details_b) && $product->product_details_b->count() > 0)
                <div class="col-md-12 mt-3">
                    <h5>{{ translate('Products B') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ translate('ID') }}</th>
                                    <th>{{ translate('Name') }}</th>
                                    <!-- <th>{{ translate('Image') }}</th> -->
                                    <th>{{ translate('Price') }}</th>
                                    <th>{{ translate('Description') }}</th>
                                    <th>{{ translate('Stock') }}</th>
                                    <th>{{ translate('Organic') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->product_details_b as $prod)
                                    <tr>
                                        <td>{{ $prod->id }}</td>
                                        <td>{{ $prod->name ?? 'N/A' }}</td>
                                        <!-- <td>
                                            @php($images = is_string($prod->images ?? []) ? json_decode($prod->images, true) : ($prod->images ?? []))
                                               
                                            @if(is_array($images) && count($images) > 0)
                                                <img src="{{ asset('storage/'.$images[0]['img']) }}" alt="{{ $prod->name }}" width="50" height="50">
                                            @else
                                                N/A
                                            @endif
                                        </td> -->
                                        <td>{{ $prod->price ?? 'N/A' }}</td>
                                        <td>{{ $prod->description ?? 'N/A' }}</td>
                                        <td>{{ $prod->stock ?? 'N/A' }}</td>
                                        <td>{{ $prod->organic ? 'Yes' : 'No' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif



            </div>
            
        
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