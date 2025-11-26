@extends('layouts.admin.app')

@section('title', 'Banner Edit')

@push('css_or_js')
@endpush

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet">
    <style>
        /* Dropdown options - selected option ka background highlight */
        .select2-results__option[aria-selected="true"] {
            background-color: #005555 !important;
            /* Bootstrap primary */
            color: #fff !important;
        }

        /* Hover effect on options */
        .select2-results__option--highlighted[aria-selected] {
            background-color: #005555 !important;
            color: #fff !important;
        }

        /* Selected tags (neeche input me show hone wale items) */
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #005555;
            /* blue tag */
            border: none;
            color: #fff;
            padding: 4px 10px;
            margin: 3px 4px 0 0;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        /* Tag ke andar remove (x) button */
        .select2-container--bootstrap4 .select2-selection__choice__remove {
            margin-right: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Input field height thoda sa neat */
        .select2-container--bootstrap4 .select2-selection--multiple {
            min-height: 46px;
            border: 1px solid #ced4da;
            border-radius: .5rem;
            padding: 4px;
        }

        /* Dropdown ka max height with scroll */
        .select2-results__options {
            max-height: 220px !important;
            overflow-y: auto !important;
        }

        /* Dropdown search bar */
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 6px 10px;
            width: 100% !important;
            outline: none;
        }
    </style>

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/edit.png') }}" class="w--26" alt="">
                </span>
                <span>
                    Edit Banner
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.client-side.update_banner', [$Banner['id']]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                    @php($language = $language->value ?? null)
                    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                    @if ($language)

                        <div class="row g-3">
                            <!-- App Owner / App -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label" for="app_id">App Owner</label>
                                    <select name="app_id" id="app_id" class="form-control js-select2-custom" required>
                                        <option disabled>---select App Owner---</option>
                                        @foreach ($apps as $app)
                                            <option value="{{ $app->id }}"
                                                {{ $Banner->app_id == $app->id ? 'selected' : '' }}>
                                                {{ $app->app_name }} ({{ $app->client->name ?? 'No Client' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label" for="title_name">Title</label>
                                    <input type="text" name="title_name" id="title_name"
                                        value="{{ $Banner->title ?? '' }}" class="form-control" required>
                                </div>
                            </div>

                            <!-- Zone -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label" for="zone_id">{{ translate('messages.zone') }}</label>
                                    <select name="zone_id" id="zone_id" class="form-control js-select2-custom" required>
                                        <option disabled>---{{ translate('messages.select') }}---</option>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}"
                                                {{ $Banner->zone_id == $zone->id ? 'selected' : '' }}>
                                                {{ $zone->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Banner Type -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="banner_type">{{ translate('messages.banner_type') }}</label>
                                    <select name="banner_type" id="banner_type" class="form-control" required>
                                        <option value="store_wise"
                                            {{ $Banner->banner_type == 'store_wise' ? 'selected' : '' }}>Store</option>
                                        <option value="voucher" {{ $Banner->banner_type == 'voucher' ? 'selected' : '' }}>
                                            Voucher</option>
                                        <option value="category"
                                            {{ $Banner->banner_type == 'category' ? 'selected' : '' }}>Category</option>
                                        <option value="voucher_type"
                                            {{ $Banner->banner_type == 'voucher_type' ? 'selected' : '' }}>Voucher Type
                                        </option>
                                        <option value="external_link"
                                            {{ $Banner->banner_type == 'external_link' ? 'selected' : '' }}>External link
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Display Number -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label" for="display_number">Display Number (Banner Position
                                        Show)</label>
                                    <input type="number" name="display_number" id="display_number"
                                        value="{{ $Banner->type_priority ?? '' }}" class="form-control">
                                </div>
                            </div>

                            <!-- Store -->
                            <div class="col-md-4" id="store_wise">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('messages.store') }}</label>
                                    <select name="store_id" id="store_id" class="form-control js-data-example-ajax">
                                        <option value="" disabled>---{{ translate('messages.select_store') }}---
                                        </option>
                                        <option value="{{ $Banner->store_id }}" selected>{{ $Banner->store_id ?? '' }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- External Link -->
                            <div class="col-md-4" id="external_link">
                                <div class="form-group">
                                    <label class="input-label">External Link</label>
                                    <input type="text" name="external_lnk" id="external_lnk"
                                        value="{{ $Banner->external_lnk ?? '' }}" class="form-control">
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="col-md-4" id="category">
                                <div class="form-group">
                                    <label class="input-label">Category</label>
                                    <select name="category_id" id="category" class="form-control js-select2-custom">
                                        <option disabled>---select Category---</option>
                                        @foreach ($category as $category_item)
                                            <option value="{{ $category_item->id }}"
                                                {{ $Banner->category_id == $category_item->id ? 'selected' : '' }}>
                                                {{ $category_item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Voucher -->
                            <div class="col-md-4" id="voucher">
                                <div class="form-group">
                                    <label class="input-label">Voucher</label>
                                    <select name="voucher_id" id="voucher" class="form-control js-select2-custom">
                                        <option disabled>---select Voucher---</option>
                                        <option value="Voucher_1"
                                            {{ $Banner->voucher_id == 'Voucher_1' ? 'selected' : '' }}>Voucher 1</option>
                                        <option value="Voucher_2"
                                            {{ $Banner->voucher_id == 'Voucher_2' ? 'selected' : '' }}>Voucher 2</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Voucher Type -->
                            <div class="col-md-4" id="voucher_type">
                                <div class="form-group">
                                    <label class="input-label">Voucher Type</label>
                                    <select name="voucher_type" id="voucher_type" class="form-control js-select2-custom">
                                        <option disabled>---select Voucher Type---</option>
                                        <option value="Voucher_type_1"
                                            {{ $Banner->voucher_type == 'Voucher_type_1' ? 'selected' : '' }}>Voucher Type
                                            1</option>
                                        <option value="Voucher_type_2"
                                            {{ $Banner->voucher_type == 'Voucher_type_2' ? 'selected' : '' }}>Voucher Type
                                            2</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Banner Image -->
                            <div class="col-md-4">
                                <div class="h-100 d-flex flex-column text-center">
                                    <label class="mt-auto mb-0 d-block">{{ translate('messages.banner_image') }}
                                        <small class="text-danger">* ( {{ translate('messages.ratio') }} 3:1 )</small>
                                    </label>
                                    <div class="py-3">
                                        <img class="img--vertical" id="viewer"
                                            src="{{ asset($Banner->image_or_video ?? 'public/assets/admin/img/900x400/img1.jpg') }}"
                                            alt="banner image" />
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1"
                                            class="custom-file-input"
                                            accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label"
                                            for="customFileEg1">{{ translate('messages.choose_file') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif
                    <div class="btn--container justify-content-end mt-5">
                        <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.update') }}</button>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/view-pages/client-side-index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

    <script>
        $(function() {
            $('#type').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: $('#type').data('placeholder'),
                allowClear: true,
                closeOnSelect: false
            });
        });
    </script>
@endpush