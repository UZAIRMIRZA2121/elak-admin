@extends('layouts.admin.app')

@section('title', translate('Item Preview'))

@push('css_or_js')
    <style>
        .modern-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateX(4px);
        }

        .condition-card strong {
            color: #2d3748;
            font-size: 15px;
            display: block;
            margin-bottom: 8px;
        }

        .condition-card ul,
        .condition-card ol {
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

            .info-table th,
            .info-table td {
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

                <div class="row mt-4">
                    <div class="col-md-12">
                            <h5 class="section-title mb-3"><i class="fas fa-cogs mr-2"></i> Voucher Type</h5>
                                <div class="condition-card p-3 border rounded shadow-sm mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="w-25">Voucher Name:</strong>
                                            <span>{{ $product->voucher_ids }}</span>
                                        </div>
                                        <hr class="my-2">
                                </div>
                        
                    </div>
                </div>

                  <!-- Client Info Section -->
               <div class="row mt-4"> 
                    <div class="col-md-12">
                        @if ($product->clients()->isNotEmpty() || $product->apps()->isNotEmpty() || $product->segments()->isNotEmpty())
                            <h5 class="section-title mb-3"><i class="fas fa-cogs mr-2"></i> Client Info</h5>

                            <!-- Clients -->
                            @if ($product->clients()->isNotEmpty())
                                <h6 class="mb-2"><i class="fas fa-user mr-2"></i>Clients</h6>
                                <div class="condition-card p-3 border rounded shadow-sm mb-3">
                                    @foreach ($product->clients() as $client)
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="w-25">Client Name:</strong>
                                            <span>{{ $client->name }}</span>
                                        </div>
                                      
                                    @endforeach
                                    @if ($product->apps()->isNotEmpty())
                                        <h6 class="mb-2"><i class="fas fa-mobile-alt mr-2"></i>Apps</h6>
                                        <div class="condition-card p-3 border rounded shadow-sm mb-3">
                                            @foreach ($product->apps() as $app)
                                                <div class="d-flex align-items-center mb-2">
                                                    <strong class="w-25">App Name:</strong>
                                                    <span>{{ $app->app_name }}</span>
                                                </div>
                                                <hr class="my-2">
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @if ($product->segments()->isNotEmpty())
                                        <h6 class="mb-2"><i class="fas fa-layer-group mr-2"></i>Segments</h6>
                                        <div class="condition-card p-3 border rounded shadow-sm mb-3">
                                            @foreach ($product->segments() as $segment)
                                                <div class="d-flex align-items-center mb-2">
                                                    <strong class="w-25">Segment Name:</strong>
                                                    <span>{{ $segment->name }}</span>
                                                </div>
                                                <hr class="my-2">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                     <!-- Segments -->
                            @endif

                        @else
                            <p>No client info available.</p>
                        @endif
                    </div>
                </div>

              

                    <!-- Left Column -->
                    <div class="row mt-4">
                        <h5 class="section-title">Partner Information</h5>
                        <table class="info-table">
                            <tbody>
                                <tr>
                                    <th><i class="fas fa-heading mr-2"></i>Store </th>
                                    <td> 
                                        {{ $product->store->name }}
                                    </td>
                                </tr>

                                @php
                                    use App\Models\Category;

                                    $categoryIds = collect(json_decode($product->category_ids, true))
                                                    ->pluck('id')
                                                    ->toArray();

                                    $categories = Category::whereIn('id', $categoryIds)->get();

                                    $mainCategories = $categories->where('parent_id', 0);
                                    $subCategories  = $categories->where('parent_id', '!=', 0);
                                @endphp



                             <tr>
                                <th><i class="fas fa-align-left mr-2"></i>Category</th>
                                <td>
                                    @if($mainCategories->count())
                                        @foreach($mainCategories as $cat)
                                            <span class="badge badge-primary mr-1">
                                                {{ $cat->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span>No category</span>
                                    @endif
                                </td>
                            </tr>

                               <tr>
                                    <th><i class="fas fa-align-left mr-2"></i>Sub Category</th>
                                    <td>
                                        @if($subCategories->count())
                                            @foreach($subCategories as $sub)
                                                <span class="badge badge-info mr-1">
                                                    {{ $sub->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span>No sub category</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th><i class="fas fa-tag mr-2"></i>Branch</th>
                                    <td>
                                          @if($product->branches && $product->branches->count())
                                            @foreach ($product->branches as $branch)
                                                <div class="mb-2 p-2 border rounded">
                                                    <div class="d-flex mb-1">
                                                        <strong>Name:</strong>&nbsp; {{ $branch->name }}
                                                    </div>

                                                    <div class="d-flex mb-1">
                                                        <strong>Type:</strong>&nbsp; {{ $branch->type }}
                                                    </div>
                                                   
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No branches available.</p>
                                        @endif


                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <!-- Right Column -->
                     <div class="row mt-4">
                        <h5 class="section-title">Voucher Details</h5>
                        <table class="info-table">
                            <tbody>
                                <tr>
                                    <th><i class="fas fa-folder mr-2"></i>Voucher Title </th>
                                    <td>    {{ $product->name ?? "N/A" }} </td>
                                </tr>
                                <tr>
                                    
                                    <th><i class="fas fa-folder mr-2"></i> Images</th>
                                 <td>
                                    @php
                                        $images = json_decode($product->images, true);
                                    @endphp

                                    @if(!empty($images))
                                        @foreach($images as $image)
                                            @if(isset($image['img']))
                                                <img class="avatar avatar-lg mr-2 mb-2 onerror-image"
                                                    src="{{ asset('storage/' . $image['img']) }}"
                                                    data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                    alt="{{ $product->name }} image">
                                            @endif
                                        @endforeach
                                    @else
                                        <img class="avatar avatar-lg mr-3"
                                            src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                            alt="No image">
                                    @endif
                                </td>


                                </tr>
                                <tr>
                                    <th><i class="fas fa-folder mr-2"></i>Thumnail Image </th>
                                    <td>   <img class="avatar avatar-lg mr-3 onerror-image"
                                            src="{{ $product->image ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                            data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                            alt="{{ $product->name }} image"> </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-folder mr-2"></i>Description </th>
                                    <td>  {{ $product->description ?? " N/A" }}  </td>
                                </tr>
                               
                                @if (isset($product->tags_ids))
                                    <tr>
                                        <th><i class="fas fa-hashtag mr-2"></i>Tags</th>
                                        <td>{{ $product->tags_ids ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                 <div class="row mt-4">
                    <div class="col-md-12">
                            <h5 class="section-title mb-3"><i class="fas fa-cogs mr-2"></i> Bundle Products Configuration </h5>
                                <div class="condition-card p-3 border rounded shadow-sm mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="w-25">Bundle Type Selection :</strong>
                                            <span>{{ $product->bundle_type ?? "N/A" }}</span>
                                        </div>
                                        <hr class="my-2">
                                        @php
                                            $productsA = !empty($product->product)
                                                ? json_decode($product->product, true)
                                                : [];

                                            $productsB = !empty($product->product_b)
                                                ? json_decode($product->product_b, true)
                                                : [];
                                        @endphp

                                     @if(empty($productsB))
                                        {{-- ================= PRODUCT A ONLY ================= --}}
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="w-25">Products :</strong>
                                            <div>
                                                @foreach($productsA as $prod)
                                                    <div>
                                                        <strong>{{ $prod['product_name'] }}</strong>
                                                        (Price: {{ $prod['base_price'] }})

                                                        @if(!empty($prod['variations']))
                                                            <ul class="mb-1">
                                                                @foreach($prod['variations'] as $var)
                                                                    <li>{{ $var }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <small>No variations</small>
                                                        @endif
                                                    </div>
                                                    <hr>
                                                @endforeach
                                            </div>
                                        </div>

                                    @else
                                        {{-- ================= PRODUCT A ================= --}}
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="w-25">Products A :</strong>
                                            <div>
                                                @foreach($productsA as $prod)
                                                    <div>
                                                        <strong>{{ $prod['product_name'] }}</strong>
                                                        (Price: {{ $prod['base_price'] }})

                                                        @if(!empty($prod['variations']))
                                                            <ul class="mb-1">
                                                                @foreach($prod['variations'] as $var)
                                                                    <li>{{ $var }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <small>No variations</small>
                                                        @endif
                                                    </div>
                                                    <hr>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- ================= PRODUCT B ================= --}}
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="w-25">Products B :</strong>
                                            <div>
                                                @foreach($productsB as $prod)
                                                    <div>
                                                        <strong>{{ $prod['product_name'] }}</strong>
                                                        (Price: {{ $prod['base_price'] }})

                                                        @if(!empty($prod['variations']))
                                                            <ul class="mb-1">
                                                                @foreach($prod['variations'] as $var)
                                                                    <li>{{ $var }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <small>No variations</small>
                                                        @endif
                                                    </div>
                                                    <hr>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif



                                </div>
                        
                    </div>
                </div>

                   <div class="row mt-4">
                    <div class="col-md-12">
                            <h5 class="section-title mb-3"><i class="fas fa-cogs mr-2"></i> Price Information
                            </h5>
                            <div class="condition-card p-3 border rounded shadow-sm mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="w-25">Price:</strong>
                                        <span>{{ $product->price ?? "N/A" }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="w-25">Offer Type:</strong>
                                        <span>{{ $product->offer_type ?? "N/A" }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="w-25">   Discount Type:</strong>
                                        <span>{{ $product->discount_type ?? "N/A" }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="w-25">Discount Value:</strong>
                                        <span>{{ $product->discount ?? "N/A" }}</span>
                                    </div>
                                    <hr class="my-2">
                                    @if(!is_null($product->required_quantity) && $product->required_quantity > 0)
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="w-25">Required Quantity :</strong>
                                            <span>{{ $product->required_quantity }}</span>
                                        </div>
                                        <hr class="my-2">
                                    @endif


                            </div>
                    
                    </div>
                </div>


                <!-- @if(isset($product->VoucherSetting))
                    <div class="mt-4">
                        <h5 class="section-title">Voucher Settings</h5>

                        <table class="info-table">
                            <tbody>

                                {{-- Validity Period --}}
                                @if($product->VoucherSetting->validity_period)
                                <tr>
                                    <th><i class="fas fa-calendar mr-2"></i>Validity Period</th>
                                    <td>
                                        Status: {{ $product->VoucherSetting->validity_period['active'] ?? 'N/A' }} <br>
                                        Start: {{ $product->VoucherSetting->validity_period['start'] ?? 'N/A' }} <br>
                                        End: {{ $product->VoucherSetting->validity_period['end'] ?? 'N/A' }}
                                    </td>
                                </tr>
                                @endif

                                {{-- Specific Days of Week --}}
                                @if($product->VoucherSetting->specific_days_of_week)
                                <tr>
                                    <th><i class="fas fa-clock mr-2"></i>Day Wise Timing</th>
                                    <td>
                                        @foreach($product->VoucherSetting->specific_days_of_week as $day => $time)
                                            <span class="badge-custom badge-info">
                                                {{ ucfirst($day) }}: {{ $time['start'] ?? 'N/A' }} - {{ $time['end'] ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif

                                {{-- Holidays / Occasions --}}
                                @if($product->HolidayOccasion && $product->HolidayOccasion->count())
                                <tr>
                                    <th><i class="fas fa-umbrella-beach mr-2"></i>Holidays</th>
                                    <td>
                                        @foreach($product->HolidayOccasion as $holiday)
                                            <span class="badge-custom badge-warning">{{ $holiday->name_en ?? $holiday->name_ar }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif

                                {{-- Custom Blackout Dates --}}
                                @if($product->CustomBlackoutDates && $product->CustomBlackoutDates->count())
                                <tr>
                                    <th><i class="fas fa-ban mr-2"></i>Blackout Dates</th>
                                    <td>
                                        @foreach($product->CustomBlackoutDates as $date)
                                            <span class="badge-custom badge-danger">{{ $date->date }} - {{ $date->description }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif

                                {{-- General Restrictions --}}
                                @if($product->GeneralRestrictions && $product->GeneralRestrictions->count())
                                <tr>
                                    <th><i class="fas fa-exclamation-triangle mr-2"></i>General Restrictions</th>
                                    <td>
                                        @foreach($product->GeneralRestrictions as $restriction)
                                            <span class="badge-custom badge-secondary">{{ $restriction->name ?? 'N/A' }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif

                                {{-- Age Restriction --}}
                                <tr>
                                    <th><i class="fas fa-user mr-2"></i>Age Restriction</th>
                                    <td>{{ $product->VoucherSetting->age_restriction ?? 'N/A' }}</td>
                                </tr>

                                {{-- Group Size --}}
                                <tr>
                                    <th><i class="fas fa-users mr-2"></i>Group Size</th>
                                    <td>{{ $product->VoucherSetting->group_size_requirement ?? 'N/A' }}</td>
                                </tr>

                                {{-- Usage Limit Per User --}}
                                @if($product->VoucherSetting->usage_limit_per_user)
                                <tr>
                                    <th><i class="fas fa-user-check mr-2"></i>Usage Per User</th>
                                    <td>
                                        {{ $product->VoucherSetting->usage_limit_per_user[0] ?? 'N/A' }}
                                        ({{ $product->VoucherSetting->usage_limit_per_user[1] ?? 'N/A' }})
                                    </td>
                                </tr>
                                @endif

                                {{-- Usage Limit Per Store --}}
                                @if($product->VoucherSetting->usage_limit_per_store)
                                <tr>
                                    <th><i class="fas fa-store mr-2"></i>Usage Per Store</th>
                                    <td>
                                        {{ $product->VoucherSetting->usage_limit_per_store[0] ?? 'N/A' }}
                                        ({{ $product->VoucherSetting->usage_limit_per_store[1] ?? 'N/A' }})
                                    </td>
                                </tr>
                                @endif

                                {{-- Offer Validity After Purchase --}}
                                <tr>
                                    <th><i class="fas fa-hourglass mr-2"></i>Offer Validity</th>
                                    <td>{{ $product->VoucherSetting->offer_validity_after_purchase ?? 'N/A' }}</td>
                                </tr>

                                {{-- Status --}}
                                <tr>
                                    <th><i class="fas fa-toggle-on mr-2"></i>Status</th>
                                    <td>
                                        <span class="badge-custom badge-success">
                                            {{ ucfirst($product->VoucherSetting->status) }}
                                        </span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    @endif

                @if ($product->bundle_type == 'simple')
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="section-title mb-3"><i class="fas fa-cogs mr-2"></i>Simple Product Info</h5>


                        </div>
                    </div>
                @endif
                   @if ($product->bundle_type == 'bogo_free')
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="section-title mb-3"><i class="fas fa-cogs mr-2"></i>BOGO Product Info</h5>


                        </div>
                    </div>
                @endif -->


              

                 <!-- Store Info Section -->
                <!-- <div class="row mt-4">
                    <div class="col-md-12">
                        @if ($product->store)
                            <h5 class="section-title mb-3"><i class="fas fa-cogs mr-2"></i> Store Info</h5>

                            <div class="condition-card p-3 border rounded shadow-sm">

                                <h6 class="mb-2"><i class="fas fa-code-branch mr-2"></i>Main Store</h6>
                                <div class="mb-2 d-flex">
                                    <strong class="w-25"><i class="fas fa-store mr-2"></i>Name:</strong>
                                    <span>{{ $product->store->name }}</span>
                                </div>
                                <div class="mb-2 d-flex">
                                    <strong class="w-25"><i class="fas fa-envelope mr-2"></i>Email:</strong>
                                    <span>{{ $product->store->email }}</span>
                                </div>
                                <div class="mb-3 d-flex">
                                    <strong class="w-25"><i class="fas fa-phone mr-2"></i>Phone:</strong>
                                    <span>{{ $product->store->phone }}</span>
                                </div>

                                <h6 class="mb-2"><i class="fas fa-code-branch mr-2"></i>Selected Branches</h6>
                                @if ($product->branches()->isNotEmpty())
                                    @foreach ($product->branches() as $branch)
                                        <div class="mb-2 p-2 border rounded">
                                            @php
                                                $badge = $branch->parent_id ? 'Sub-branch' : 'Main';
                                                $badgeClass = $branch->parent_id ? 'badge-warning' : 'badge-success';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} mb-2">{{ $badge }}</span>

                                            <div class="d-flex mb-1">
                                                <strong class="w-25"><i class="fas fa-store mr-2"></i>Name:</strong>
                                                <span>{{ $branch->name }}</span>
                                            </div>
                                            <div class="d-flex mb-1">
                                                <strong class="w-25"><i class="fas fa-envelope mr-2"></i>Email:</strong>
                                                <span>{{ $branch->email }}</span>
                                            </div>
                                            <div class="d-flex mb-1">
                                                <strong class="w-25"><i class="fas fa-phone mr-2"></i>Phone:</strong>
                                                <span>{{ $branch->phone }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p>No branches available.</p>
                                @endif

                            </div>
                        @endif
                    </div>
                </div>  -->


                <!-- How to Work & Terms Section in 2 Columns -->
                <div class="row mt-4">
                    <!-- How to Work Section - Left Column -->
                    <div class="col-md-12">
                        @if ($product->how_conditions && $product->how_conditions->count() > 0)
                            <h5 class="section-title"><i class="fas fa-cogs mr-2"></i>How It Works</h5>
                            @foreach ($product->how_conditions as $condition)
                                <div class="condition-card">
                                    <strong><i class="fas fa-book mr-2"></i>{{ $condition->guide_title }}</strong>
                                    @php($sections = $condition->sections)
                                    @if (is_array($sections))
                                        <ul style="list-style: none; padding-left: 0;">
                                            @foreach ($sections as $section)
                                                <li style="margin-bottom: 16px;">
                                                    <strong style="color: #667eea;">{{ $section['title'] ?? '' }}</strong>
                                                    @if (!empty($section['steps']) && is_array($section['steps']))
                                                        <ol style="margin-top: 8px;">
                                                            @foreach ($section['steps'] as $step)
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
                        @if ($product->terms_conditions && $product->terms_conditions->count() > 0)
                            <h5 class="section-title"><i class="fas fa-file-contract mr-2"></i>Terms & Conditions</h5>
                            @foreach ($product->terms_conditions as $term)
                                <div class="terms-card">
                                    <strong><i
                                            class="fas fa-check-circle mr-2"></i>{{ $term->baseinfor_condition_title }}</strong>

                                    @if (!empty($term->baseinfor_description))
                                        <p><i class="fas fa-info-circle mr-2"></i>{{ $term->baseinfor_description }}</p>
                                    @endif

                                    @if (is_array($term->timeandday_config_days))
                                        <p>
                                            <strong><i class="fas fa-calendar-day mr-2"></i>Available Days:</strong>
                                            @foreach ($term->timeandday_config_days as $day)
                                                <span class="badge-custom badge-primary">{{ $day }}</span>
                                            @endforeach
                                        </p>
                                    @endif

                                    <p>
                                        <strong><i class="fas fa-clock mr-2"></i>Time Range:</strong>
                                        <span
                                            class="badge-custom badge-info">{{ $term->timeandday_config_time_range_from }}
                                            - {{ $term->timeandday_config_time_range_to }}</span>
                                    </p>

                                    <p>
                                        <strong><i class="fas fa-calendar-check mr-2"></i>Valid Period:</strong>
                                        <span
                                            class="badge-custom badge-success">{{ $term->timeandday_config_valid_from_date }}
                                            to {{ $term->timeandday_config_valid_until_date }}</span>
                                    </p>

                                    @if (is_array($term->holiday_occasions_holiday_restrictions))
                                        <p>
                                            <strong><i class="fas fa-ban mr-2"></i>Holiday Restrictions:</strong>
                                            @foreach ($term->holiday_occasions_holiday_restrictions as $restriction)
                                                <span class="badge-custom badge-warning">{{ $restriction }}</span>
                                            @endforeach
                                        </p>
                                    @endif

                                    @if (is_array($term->holiday_occasions_special_occasions))
                                        <p>
                                            <strong><i class="fas fa-star mr-2"></i>Special Occasions:</strong>
                                            @foreach ($term->holiday_occasions_special_occasions as $occasion)
                                                <span class="badge-custom badge-success">{{ $occasion }}</span>
                                            @endforeach
                                        </p>
                                    @endif

                                    <p>
                                        <strong><i class="fas fa-user-clock mr-2"></i>Usage Limit:</strong>
                                        <span class="badge-custom badge-warning">{{ $term->usage_limits_limit_per_user }}
                                            /
                                            {{ ucfirst($term->usage_limits_period) }}</span>
                                    </p>

                                    @if (is_array($term->location_availability_venue_types))
                                        <p>
                                            <strong><i class="fas fa-map-pin mr-2"></i>Available At:</strong>
                                            @foreach ($term->location_availability_venue_types as $venue)
                                                <span class="badge-custom badge-info">{{ $venue }}</span>
                                            @endforeach
                                        </p>
                                    @endif

                                    @if (is_array($term->restriction_polices_restriction_type))
                                        <p>
                                            <strong><i class="fas fa-exclamation-triangle mr-2"></i>Restrictions:</strong>
                                            @foreach ($term->restriction_polices_restriction_type as $restriction)
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
     
    </div>

    {{-- Add Quantity Modal --}}
    <div class="modal fade update-quantity-modal" id="update-quantity" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 12px; border: none;">
                <div class="modal-header"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-bottom: none;">
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
                            <button type="reset" data-dismiss="modal" class="btn btn-secondary"
                                style="border-radius: 8px;">
                                <i class="fas fa-times mr-1"></i>{{ translate('cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary"
                                style="border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
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
