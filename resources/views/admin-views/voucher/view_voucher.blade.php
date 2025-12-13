@extends('layouts.admin.app')

@section('title', translate('Item Preview'))

@push('css_or_js')
<style>
    .modern-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .modern-card-header {
        background-color: #005555;
        padding: 20px 24px;
        border-bottom: none;
    }
    
    .modern-card-header h4 {
        color: white;
        font-weight: 700;
        font-size: 20px;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .modern-card-header h4 i {
        margin-right: 10px;
        font-size: 24px;
    }
    
    .modern-card-body {
        padding: 24px;
        background: #fff;
    }
    
    .info-table {
        width: 100%;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .info-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.3s ease;
    }
    
    .info-table tbody tr:hover {
        background: #f8f9ff;
    }
    
    .info-table tbody tr:last-child {
        border-bottom: none;
    }
    
    .info-table th {
        font-weight: 700;
        color: #1a1a1a;
        font-size: 14px;
        padding: 16px 20px;
        width: 40%;
        background: #f8f9fa;
        border-right: 2px solid #e9ecef;
    }
    
    .info-table td {
        padding: 16px 20px;
        color: #4a5568;
        font-size: 14px;
    }
    
    .badge-custom {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        margin-right: 8px;
        margin-bottom: 8px;
    }
    
    .badge-primary {
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .badge-success {
        background: #e8f5e9;
        color: #388e3c;
    }
    
    .badge-warning {
        background: #fff3e0;
        color: #f57c00;
    }
    
    .badge-info {
        background: #e1f5fe;
        color: #0288d1;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 3px solid #667eea;
        display: inline-block;
    }
    
    .products-table {
        width: 100%;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    
    .products-table thead {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }
    
    .products-table thead th {
        color: white;
        font-weight: 600;
        padding: 16px 20px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .products-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .products-table tbody tr:hover {
        background: #f8f9ff;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .products-table tbody td {
        padding: 16px 20px;
        color: #4a5568;
        font-size: 14px;
    }
    
    .condition-card {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        transition: all 0.3s ease;
    }
    
    .condition-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(4px);
    }
    
    .condition-card strong {
        color: #2d3748;
        font-size: 15px;
        display: block;
        margin-bottom: 8px;
    }
    
    .condition-card ul, .condition-card ol {
        margin-left: 20px;
        margin-top: 8px;
        margin-bottom: 8px;
    }
    
    .condition-card li {
        margin-bottom: 6px;
        color: #4a5568;
        line-height: 1.6;
    }
    
    .terms-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .terms-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
    }
    
    .terms-card strong {
        color: #667eea;
        font-size: 16px;
        display: block;
        margin-bottom: 12px;
    }
    
    .terms-card p {
        margin-bottom: 8px;
        color: #4a5568;
        line-height: 1.6;
    }
    
    .terms-card p strong {
        display: inline;
        color: #2d3748;
        font-size: 14px;
    }
    
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }
    
    @media (max-width: 768px) {
        .modern-card-body {
            padding: 16px;
        }
        
        .info-table th, .info-table td {
            padding: 12px 16px;
            font-size: 13px;
        }
        
        .grid-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    
    <!-- Voucher Details Card -->
    <div class="modern-card">
        <div class="modern-card-header">
            <h4><i class="fas fa-ticket-alt"></i> {{ translate('Voucher Details') }}</h4>
        </div>
        <div class="modern-card-body">
            <div class="grid-container">
                <!-- Left Column -->
                <div>
                    <h5 class="section-title">Basic Information</h5>
                    <table class="info-table">
                        <tbody>
                            <tr>
                                <th><i class="fas fa-hashtag mr-2"></i>Voucher ID</th>
                                <td><span class="badge-custom badge-primary">{{ $product->id ?? 'N/A' }}</span></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-heading mr-2"></i>Voucher Title</th>
                                <td><strong>{{ $product->name ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-align-left mr-2"></i>Description</th>
                                <td>{{ $product->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-tag mr-2"></i>Voucher Type</th>
                                <td><span class="badge-custom badge-info">{{ ucfirst($product->type) ?? 'N/A' }}</span></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-box mr-2"></i>Bundle Type</th>
                                <td><span class="badge-custom badge-success">{{ ucfirst($product->bundle_type) ?? 'N/A' }}</span></td>
                            </tr>
                            @if(isset($product->valid_until))
                            <tr>
                                <th><i class="fas fa-calendar-check mr-2"></i>Valid Until</th>
                                <td><strong class="text-danger">{{ $product->valid_until ? \Carbon\Carbon::parse($product->valid_until)->format('d M Y') : 'N/A' }}</strong></td>
                            </tr>
                            @endif
                            <tr>
                                <th><i class="fas fa-dollar-sign mr-2"></i>Price</th>
                                <td><strong style="color: #388e3c; font-size: 16px;">${{ ucfirst($product->price) ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-percent mr-2"></i>Discount</th>
                                <td><span class="badge-custom badge-warning">{{ ucfirst($product->discount) ?? '0' }}%</span></td>
                            </tr>
                            @if(!empty($product->required_quantity) && $product->required_quantity > 0)
                            <tr>
                                <th><i class="fas fa-tags mr-2"></i>Discount Type</th>
                                <td>{{ ucfirst($product->discount_type) ?? 'N/A' }}</td>
                            </tr>
                            @if(!empty($product->required_quantity) && $product->required_quantity > 0)
                            <tr>
                                <th><i class="fas fa-shopping-cart mr-2"></i>Required Quantity</th>
                                <td><span class="badge-custom badge-primary">{{ $product->required_quantity }}</span></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Right Column -->
                <div>
                    <h5 class="section-title">Category & Location</h5>
                    <table class="info-table">
                        <tbody>
                            <tr>
                                <th><i class="fas fa-folder mr-2"></i>Categories</th>
                                <td>
                                    @if($product->categories && $product->categories->isNotEmpty())
                                        @foreach($product->categories as $category)
                                            <span class="badge-custom badge-primary">{{ $category->name }}</span>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            @if(isset($product->sub_categories))
                            <tr>
                                <th><i class="fas fa-sitemap mr-2"></i>Sub Categories</th>
                                <td>
                                    @if(isset($product->sub_categories) && $product->sub_categories->count() > 0)
                                        @foreach($product->sub_categories as $subCat)
                                            <span class="badge-custom badge-info">{{ $subCat->name }}</span>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th><i class="fas fa-map-marker-alt mr-2"></i>Branches</th>
                                <td>
                                    @if(!empty($product->branches) && $product->branches->count() > 0)
                                        @foreach($product->branches as $branch)
                                            <span class="badge-custom badge-success">{{ $branch->name }}</span>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            @if(isset($product->tags_ids))
                            <tr>
                                <th><i class="fas fa-hashtag mr-2"></i>Tags</th>
                                <td>{{ $product->tags_ids ?? 'N/A' }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- How to Work & Terms Section in 2 Columns -->
            <div class="row mt-4">
                <!-- How to Work Section - Left Column -->
                <div class="col-md-12">
                    @if($product->how_conditions && $product->how_conditions->count() > 0)
                        <h5 class="section-title"><i class="fas fa-cogs mr-2"></i>How It Works</h5>
                        @foreach($product->how_conditions as $condition)
                            <div class="condition-card">
                                <strong><i class="fas fa-book mr-2"></i>{{ $condition->guide_title }}</strong>
                                @php($sections = $condition->sections)
                                @if(is_array($sections))
                                    <ul style="list-style: none; padding-left: 0;">
                                        @foreach($sections as $section)
                                            <li style="margin-bottom: 16px;">
                                                <strong style="color: #667eea;">{{ $section['title'] ?? '' }}</strong>
                                                @if(!empty($section['steps']) && is_array($section['steps']))
                                                    <ol style="margin-top: 8px;">
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
                    @endif
                </div>
                </div>
                <div class="row mt-4">
                <!-- Terms & Conditions Section - Right Column -->
                <div class="col-md-12">
                    @if($product->terms_conditions && $product->terms_conditions->count() > 0)
                        <h5 class="section-title"><i class="fas fa-file-contract mr-2"></i>Terms & Conditions</h5>
                        @foreach($product->terms_conditions as $term)
                            <div class="terms-card">
                                <strong><i class="fas fa-check-circle mr-2"></i>{{ $term->baseinfor_condition_title }}</strong>
                                
                                @if(!empty($term->baseinfor_description))
                                    <p><i class="fas fa-info-circle mr-2"></i>{{ $term->baseinfor_description }}</p>
                                @endif

                                @if(is_array($term->timeandday_config_days))
                                    <p>
                                        <strong><i class="fas fa-calendar-day mr-2"></i>Available Days:</strong>
                                        @foreach($term->timeandday_config_days as $day)
                                            <span class="badge-custom badge-primary">{{ $day }}</span>
                                        @endforeach
                                    </p>
                                @endif

                                <p>
                                    <strong><i class="fas fa-clock mr-2"></i>Time Range:</strong>
                                    <span class="badge-custom badge-info">{{ $term->timeandday_config_time_range_from }} - {{ $term->timeandday_config_time_range_to }}</span>
                                </p>

                                <p>
                                    <strong><i class="fas fa-calendar-check mr-2"></i>Valid Period:</strong>
                                    <span class="badge-custom badge-success">{{ $term->timeandday_config_valid_from_date }} to {{ $term->timeandday_config_valid_until_date }}</span>
                                </p>

                                @if(is_array($term->holiday_occasions_holiday_restrictions))
                                    <p>
                                        <strong><i class="fas fa-ban mr-2"></i>Holiday Restrictions:</strong>
                                        @foreach($term->holiday_occasions_holiday_restrictions as $restriction)
                                            <span class="badge-custom badge-warning">{{ $restriction }}</span>
                                        @endforeach
                                    </p>
                                @endif

                                @if(is_array($term->holiday_occasions_special_occasions))
                                    <p>
                                        <strong><i class="fas fa-star mr-2"></i>Special Occasions:</strong>
                                        @foreach($term->holiday_occasions_special_occasions as $occasion)
                                            <span class="badge-custom badge-success">{{ $occasion }}</span>
                                        @endforeach
                                    </p>
                                @endif

                                <p>
                                    <strong><i class="fas fa-user-clock mr-2"></i>Usage Limit:</strong>
                                    <span class="badge-custom badge-warning">{{ $term->usage_limits_limit_per_user }} / {{ ucfirst($term->usage_limits_period) }}</span>
                                </p>

                                @if(is_array($term->location_availability_venue_types))
                                    <p>
                                        <strong><i class="fas fa-map-pin mr-2"></i>Available At:</strong>
                                        @foreach($term->location_availability_venue_types as $venue)
                                            <span class="badge-custom badge-info">{{ $venue }}</span>
                                        @endforeach
                                    </p>
                                @endif

                                @if(is_array($term->restriction_polices_restriction_type))
                                    <p>
                                        <strong><i class="fas fa-exclamation-triangle mr-2"></i>Restrictions:</strong>
                                        @foreach($term->restriction_polices_restriction_type as $restriction)
                                            <span class="badge-custom badge-warning">{{ $restriction }}</span>
                                        @endforeach
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    @if(isset($product->product_details) && $product->product_details->count() > 0)
    <div class="modern-card">
        <div class="modern-card-body" style="padding: 0; background: transparent;">
            <div style="background: #005555; padding: 20px 24px; border-radius: 12px 12px 0 0;">
                <h4 style="color: white; font-weight: 700; font-size: 20px; margin: 0;"><i class="fas fa-box-open mr-2"></i>{{ translate('Products A') }}</h4>
            </div>
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag mr-2"></i>ID</th>
                            <th><i class="fas fa-tag mr-2"></i>Name</th>
                            <th><i class="fas fa-dollar-sign mr-2"></i>Price</th>
                            <th><i class="fas fa-align-left mr-2"></i>Description</th>
                            <th><i class="fas fa-warehouse mr-2"></i>Stock</th>
                            <th><i class="fas fa-leaf mr-2"></i>Organic</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->product_details as $prod)
                            <tr>
                                <td><span class="badge-custom badge-primary">{{ $prod->id }}</span></td>
                                <td><strong>{{ $prod->name ?? 'N/A' }}</strong></td>
                                <td><strong style="color: #388e3c;">${{ $prod->price ?? 'N/A' }}</strong></td>
                                <td>{{ $prod->description ?? 'N/A' }}</td>
                                <td><span class="badge-custom badge-info">{{ $prod->stock ?? 'N/A' }}</span></td>
                                <td>
                                    @if($prod->organic)
                                        <span class="badge-custom badge-success"><i class="fas fa-check mr-1"></i>Yes</span>
                                    @else
                                        <span class="badge-custom badge-warning"><i class="fas fa-times mr-1"></i>No</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Products B Section -->
  <!-- Products B Section -->
@if(isset($product->product_details_b) && $product->product_details_b->count() > 0)
<div class="modern-card">
    <div class="modern-card-header" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
        <h4><i class="fas fa-boxes"></i> {{ translate('Products B') }}</h4>
    </div>
    <div class="modern-card-body">
        <div class="table-responsive">
            <table class="products-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag mr-2"></i>ID</th>
                        <th><i class="fas fa-tag mr-2"></i>Name</th>
                        <th><i class="fas fa-dollar-sign mr-2"></i>Price</th>
                        <th><i class="fas fa-align-left mr-2"></i>Description</th>
                        <th><i class="fas fa-warehouse mr-2"></i>Stock</th>
                        <th><i class="fas fa-leaf mr-2"></i>Organic</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->product_details_b as $prod)
                        <tr>
                            <td><span class="badge-custom badge-primary">{{ $prod->id }}</span></td>
                            <td><strong>{{ $prod->name ?? 'N/A' }}</strong></td>
                            <td><strong style="color: #388e3c;">${{ $prod->price ?? 'N/A' }}</strong></td>
                            <td>{{ $prod->description ?? 'N/A' }}</td>
                            <td><span class="badge-custom badge-info">{{ $prod->stock ?? 'N/A' }}</span></td>
                            <td>
                                @if($prod->organic)
                                    <span class="badge-custom badge-success"><i class="fas fa-check mr-1"></i>Yes</span>
                                @else
                                    <span class="badge-custom badge-warning"><i class="fas fa-times mr-1"></i>No</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
</div>

{{-- Add Quantity Modal --}}
<div class="modal fade update-quantity-modal" id="update-quantity" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-bottom: none;">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Update Stock Quantity</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-3">
                <form action="{{ route('admin.Voucher.stock-update') }}" method="post">
                    @csrf
                    <div class="mt-2 rest-part w-100"></div>
                    <div class="btn--container justify-content-end mt-4">
                        <button type="reset" data-dismiss="modal" class="btn btn-secondary" style="border-radius: 8px;">
                            <i class="fas fa-times mr-1"></i>{{ translate('cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary" style="border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                            <i class="fas fa-check mr-1"></i>{{ translate('update_stock') }}
                        </button>
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
            confirmButtonColor: '#667eea',
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