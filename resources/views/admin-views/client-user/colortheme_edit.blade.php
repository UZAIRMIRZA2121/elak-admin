@extends('layouts.admin.app')

@section('title', 'Color Theme Edit')

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
                    Edit Color Theme
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">


                <form action="{{ route('admin.client-side.update_color_theme', [$colorTheme['id']]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <!-- Theme Name -->
                        <div class="col-md-3">
                            <label for="name" class="form-label">Theme Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $colorTheme->name) }}" required>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ old('start_date',$colorTheme->start_date) }}" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ old('end_date',$colorTheme->end_date) }}" required>
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status-{{ $colorTheme->id ?? 'new' }}" class="d-block">Status</label>
                                <div class="form-check form-switch">
                                    <label class="toggle-switch toggle-switch-sm"
                                        for="status-{{ $colorTheme->id ?? 'new' }}">
                                        <input type="checkbox" class="toggle-switch-input" name="status"
                                            id="status-{{ $colorTheme->id ?? 'new' }}" value="active"
                                            {{ old('status', $colorTheme->status ?? 'active') == 'active' ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Optional: JS to update label dynamically -->
                        <script>
                            const toggle = document.getElementById('status-{{ $colorTheme->id ?? 'new' }}');
                            toggle.addEventListener('change', function() {
                                // You can add additional JS if you want to show "Active"/"Inactive" text somewhere
                                console.log('Status is now:', this.checked ? 'Active' : 'Inactive');
                            });
                        </script>



                    </div>

                    <hr>

                    <!-- Colors -->
                    <div class="row">
                        <?php
                        $predefinedColors = ['primary_color', 'secondary_color', 'background_color', 'text_color', 'button_color', 'button_text_color', 'navbar_color', 'navbar_text_color'];
                        ?>

                        @foreach ($predefinedColors as $key)
                            @php
                                $color = $colors[$key] ?? [
                                    'value' => '#000000',
                                    'name' => ucwords(str_replace('_', ' ', $key)),
                                    'gradient' => 0,
                                ];
                            @endphp

                            <div class="col-md-12 mb-4 p-3 border rounded">
                                <h6 class="fw-bold">{{ ucwords(str_replace('_', ' ', $key)) }}</h6>

                                <div class="row g-3 align-items-center">
                                    <!-- Color Picker -->
                                    <div class="col-md-2">
                                        <label class="form-label">Color Picker</label>
                                        <input type="color" id="{{ $key }}"
                                            class="form-control form-control-color"
                                            name="colors[{{ $key }}][value]"
                                            value="{{ old('colors.' . $key . '.value', $color['value']) }}"
                                            onchange="document.getElementById('{{ $key }}_text').value = this.value">
                                    </div>

                                    <!-- Hex Input -->
                                    <div class="col-md-3">
                                        <label class="form-label">Hex Value</label>
                                        <input type="text" id="{{ $key }}_text" class="form-control"
                                            name="colors[{{ $key }}][value]"
                                            value="{{ old('colors.' . $key . '.value', $color['value']) }}"
                                            oninput="document.getElementById('{{ $key }}').value = this.value">
                                    </div>

                                    <!-- Color Name -->
                                    <div class="col-md-3">
                                        <label class="form-label">Color Name</label>
                                        <input type="text" class="form-control" name="colors[{{ $key }}][name]"
                                            placeholder="Enter name"
                                            value="{{ old('colors.' . $key . '.name', $color['name']) }}">
                                    </div>

                                    <!-- Gradient Toggle -->
                                    <div class="col-md-2">
                                        <label class="form-label d-block">Gradient</label>
                                        <div class="form-check">
                                            <label class="toggle-switch toggle-switch-sm"
                                                for="gradient-{{ $key }}">
                                                <input type="checkbox" class="toggle-switch-input"
                                                    id="gradient-{{ $key }}"
                                                    name="colors[{{ $key }}][gradient]" value="1"
                                                    {{ old('colors.' . $key . '.gradient', $color['gradient'] ?? 0) ? 'checked' : '' }}>
                                                <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success">Update Color Theme</button>
                        </div>
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
    <!-- Optional JS: Update status label dynamically -->
    <script>
        const statusToggle = document.getElementById('status');
        const statusLabel = statusToggle.nextElementSibling;

        statusToggle.addEventListener('change', function() {
            statusLabel.textContent = this.checked ? 'Active' : 'Inactive';
        });
    </script>
@endpush
