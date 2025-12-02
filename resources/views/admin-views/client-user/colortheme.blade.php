@extends('layouts.admin.app')
@section('title', 'Color Themes')
@section('content')

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .select2-selection--single {
            height: 42px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            padding: 8px 12px !important;
            font-size: 1rem;
            background-color: #fff;
            transition: all 0.15s ease-in-out;
        }

        .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            outline: none !important;
        }

        .select2-selection__rendered {
            padding: 0 !important;
            line-height: 24px !important;
            color: #495057;
        }

        .select2-selection__placeholder {
            color: #000000 !important;
        }

        .select2-selection__arrow {
            height: 40px !important;
            right: 10px !important;
        }

        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .select2-results__option {
            padding: 12px 16px;
            font-size: 1rem;
            transition: background-color 0.15s ease-in-out;
        }

        .select2-results__option--highlighted {
            background-color: #0d6efd !important;
            color: white !important;
        }

        .select2-results__option--selected {
            background-color: #e7f3ff;
            color: #0d6efd;
            font-weight: 500;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 8px 12px;
            font-size: 1rem;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Success state */
        .select2-container.is-valid .select2-selection--single {
            border-color: #198754 !important;
        }

        .is-valid~.valid-feedback {
            display: block;
        }

        .valid-feedback {
            color: #198754;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/condition.png') }}" class="w--26" alt="">
                </span>
                <span>
                    Color Theme List
                </span>
            </h1>
        </div>
        @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.client-side.color_theme') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Color Theme Info -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Theme Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ old('name') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                            value="{{ old('start_date') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                            value="{{ old('end_date') }}" required>
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

                            <h5>Static Colors</h5>
                            <div class="container">

                                <?php
                                $colors = ['primary_color', 'secondary_color', 'background_color', 'text_color', 'button_color', 'button_text_color', 'navbar_color', 'navbar_text_color'];
                                ?>

                                @foreach ($colors as $color)
                                    <div class="row mb-4 p-3 border rounded">

                                        <!-- Label -->
                                        <div class="col-md-12 mb-2">
                                            <h6 class="fw-bold">{{ ucwords(str_replace('_', ' ', $color)) }}</h6>
                                        </div>

                                        <!-- Color Picker -->
                                        <div class="col-md-2">
                                            <label class="form-label">Color Picker</label>
                                            <input type="color" id="{{ $color }}"
                                                class="form-control form-control-color"
                                                value="{{ old('colors.' . $color . '.value', '#000000') }}"
                                                onchange="document.getElementById('{{ $color }}_text').value = this.value">
                                        </div>

                                        <!-- Hex Input -->
                                        <div class="col-md-3">
                                            <label class="form-label">Hex Value</label>
                                            <input type="text" id="{{ $color }}_text"
                                                name="colors[{{ $color }}][value]" class="form-control"
                                                value="{{ old('colors.' . $color . '.value', '#000000') }}"
                                                oninput="document.getElementById('{{ $color }}').value = this.value">
                                        </div>

                                        <!-- Color Name -->
                                        <div class="col-md-3">
                                            <label class="form-label">Color Name</label>
                                            <input type="text" name="colors[{{ $color }}][name]"
                                                class="form-control" placeholder="Enter name"
                                                value="{{ old('colors.' . $color . '.name') }}">
                                        </div>
                                        <!-- Gradient Toggle -->
                                        <div class="col-md-2">
                                            <label class="form-label d-block">Gradient</label>
                                            <div class="form-check">
                                                <label class="toggle-switch toggle-switch-sm"
                                                    for="gradient-{{ $color }}">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                        id="gradient-{{ $color }}"
                                                        name="colors[{{ $color }}][gradient]" value="1"
                                                        {{ old('colors.' . $color . '.gradient') ? 'checked' : '' }}>
                                                    <span class="toggle-switch-label mx-auto">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>



                                    </div>
                                @endforeach

                            </div>




                            <div class="btn--container justify-content-end mt-4">
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <button type="submit" class="btn btn-primary">Save Theme</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                Color Theme List<span class="badge badge-soft-dark ml-2" id="itemCount"></span>
                            </h5>
                            <form class="search-form">
                                <!-- Search -->

                                <div class="input-group input--group">
                                    <input id="datatableSearch_" value="{{ request()?->search ?? null }}" type="search"
                                        name="search" class="form-control" placeholder="Ex: Banner"
                                        aria-label="Search">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                            @if (request()->get('search'))
                                <button type="reset" class="btn btn--primary ml-2 location-reload-to-base"
                                    data-url="{{ url()->full() }}">{{ translate('messages.reset') }}</button>
                            @endif

                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th class="border-0">{{ translate('sl') }}</th>
                                    <th class="border-0">Theme Name</th>
                                    <th class="border-0">Colors</th>
                                    <th class="border-0">Start Date</th>
                                    <th class="border-0">End Date</th>

                                    <th class="border-0">Status</th>
                                    <th class="border-0">Action</th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php $i = 1; ?>

                                @foreach ($themes as $theme)
                                    <tr class="text-center">
                                        <td>{{ $i++ }}</td>

                                        <td>{{ $theme->name }}</td>
                                        <td>{{ $theme->start_date }}</td>
                                        <td>{{ $theme->end_date }}</td>

                                        <td>
                                            @foreach ($theme->colorCodes as $color)
                                                <strong>{{ $color->color_name }}</strong><br>
                                                <strong>{{ $color->color_code }}</strong>
                                                <!-- Color Preview Box -->
                                                <span
                                                    style="
                                                        display:inline-block;
                                                        width:20px;
                                                        height:20px;
                                                        background: {{ $color->color_code }};
                                                        border:1px solid #ccc;
                                                        margin-left: 10px;
                                                        border-radius:4px;">
                                                </span>

                                                <br>
                                            @endforeach
                                        </td>

                                        <td class="text-center">
                                            <label class="toggle-switch toggle-switch-sm"
                                                for="status-{{ $theme->id }}">
                                                <input type="checkbox" class="toggle-switch-input status-toggle"
                                                    id="status-{{ $theme->id }}" data-id="{{ $theme->id }}"
                                                    {{ $theme->status == 'active' ? 'checked' : '' }}>
                                                <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>

                                        <td class="text-center">
                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.client-side.edit_color_theme', $theme->id) }}"
                                                class="btn action-btn btn--primary btn-outline-primary " title="Edit">
                                                <i class="tio-edit"></i>
                                            </a>

                                            <!-- Delete Form (hidden or separate) -->
                                            <form id="delete-theme-{{ $theme->id }}"
                                                action="{{ route('admin.client-side.delete_color_theme', $theme->id) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <!-- Delete Button outside the form -->
                                            <button type="button" class="btn action-btn btn--danger btn-outline-danger"
                                                onclick="if(confirm('Are you sure you want to delete this theme?')){ document.getElementById('delete-theme-{{ $theme->id }}').submit(); }"
                                                title="Delete">
                                                <i class="tio-delete-outlined"></i>
                                            </button>
                                        </td>



                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/view-pages/client-side-index.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // -------------------- Segment Select2 --------------------
            $('.segment-select').select2({
                placeholder: "-- Select Segment --",
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true,
                minimumResultsForSearch: 3,
                templateResult: formatOption,
                templateSelection: formatSelection
            });

            // -------------------- Client Select2 --------------------
            $('.Clients-select').select2({
                placeholder: "-- Select Clients --",
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true,
                minimumResultsForSearch: 3,
                templateResult: formatOption,
                templateSelection: formatSelection
            });

            // -------------------- Option Formatter --------------------
            function formatOption(option) {
                if (!option.id) return option.text;

                const parts = option.text.split(' / ');
                if (parts.length === 2) {
                    const name = parts[0];
                    const type = parts[1];
                    const typeClass = type === 'free' ? 'success' : type === 'paid' ? 'primary' : 'warning';

                    return $(
                        '<div class="d-flex justify-content-between align-items-center">' +
                        '<span>' + name + '</span>' +
                        '<span class="badge bg-' + typeClass + '">' + type + '</span>' +
                        '</div>'
                    );
                }
                return option.text;
            }

            function formatSelection(option) {
                return option.text || option.placeholder;
            }

            // -------------------- Client Change => Load Segments --------------------
            $('.Clients-select').on('change', function() {
                let clientId = $(this).val();
                if (!clientId) return;

                let url = "{{ route('admin.client-side.getSegments', ':id') }}".replace(':id', clientId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        // Clear and refill segment dropdown
                        $('#segment_type').empty().append('<option></option>');

                        $.each(res, function(index, item) {
                            $('#segment_type').append(
                                '<option value="' + item.id + '">' + item.name +
                                ' / ' + item.type + '</option>'
                            );
                        });

                        // Refresh Select2
                        $('#segment_type').trigger('change');
                    },
                    error: function() {
                        alert("Error loading segments!");
                    }
                });
            });

            // -------------------- Segment Select Validation --------------------
            $('.segment-select').on('select2:select', function(e) {
                const data = e.params.data;
                $('#selectedValue').removeClass('alert-info alert-warning')
                    .addClass('alert-success')
                    .html('<i class="fas fa-check-circle me-2"></i>Selected: <strong>' + data.text +
                        '</strong>');
                $(this).addClass('is-valid');
            });

            $('.segment-select').on('select2:clear', function() {
                $('#selectedValue').removeClass('alert-success')
                    .addClass('alert-info')
                    .html('No segment selected yet');
                $(this).removeClass('is-valid');
            });

            // -------------------- Clients Select Validation --------------------
            $('.Clients-select').on('select2:select', function(e) {
                const data = e.params.data;
                $('#selectedValue').removeClass('alert-info alert-warning')
                    .addClass('alert-success')
                    .html('<i class="fas fa-check-circle me-2"></i>Selected: <strong>' + data.text +
                        '</strong>');
                $(this).addClass('is-valid');
            });

            $('.Clients-select').on('select2:clear', function() {
                $('#selectedValue').removeClass('alert-success')
                    .addClass('alert-info')
                    .html('No Clients selected yet');
                $(this).removeClass('is-valid');
            });

            // -------------------- Submit Demo --------------------
            $('#submitBtn').on('click', function() {
                const selectedClients = $('.Clients-select').val();
                const selectedSegment = $('.segment-select').val();
                const clientName = $('#client_name').val();

                if (!selectedClients) {
                    alert('Please select a Client first!');
                    return;
                }

                if (!selectedSegment) {
                    alert('Please select a Segment first!');
                    return;
                }

                if (!clientName) {
                    alert('Please enter client name!');
                    return;
                }

                alert(
                    'Client saved successfully!\n' +
                    'Client: ' + $('.Clients-select option:selected').text() +
                    '\nSegment: ' + $('.segment-select option:selected').text() +
                    '\nName: ' + clientName
                );
            });
        });
    </script>

    <script>
        document.querySelectorAll('.status-toggle').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                let themeId = this.dataset.id;
                let status = this.checked ? 'active' : 'inactive';

                // Generate base URL with placeholder
                let url = "{{ route('admin.client-side.status_color_theme', ['id' => ':id']) }}";
                url = url.replace(':id', themeId); // replace placeholder with actual ID

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success('Status updated to ' + status);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    })
                    .catch(error => {
                        toastr.error('Error: ' + error);
                    });
            });
        });
    </script>
@endpush
