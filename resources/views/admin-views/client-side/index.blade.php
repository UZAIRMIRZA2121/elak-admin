@extends('layouts.admin.app')

@section('title', 'Client List')

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
                    <img src="{{ asset('public/assets/admin/img/condition.png') }}" class="w--26" alt="">
                </span>
                <span>
                    Add Client
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
                        <form action="{{ route('admin.client-side.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @if ($language)
                                <div class="row">

                                    <div class="col-6 col-md-4">
                                        <div class="lang_form" id="default-form">
                                            <div class="form-group">
                                                <label class="input-label" for="name"> Client Name
                                                </label>
                                                <input type="text" name="name" id="name" class="form-control"
                                                    placeholder="Enter Client Name">
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="lang_form" id="default-form">
                                            <div class="form-group">
                                                <label class="input-label" for="email">Email
                                                </label>
                                                <input type="text" name="email" id="email"class="form-control"
                                                    placeholder="Enter Email">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="lang_form" id="default-form">
                                            <div class="form-group">
                                                <label class="input-label" for="password">Password </label>
                                                <input type="text" name="password" id="password" class="form-control"
                                                    placeholder="Enter Password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="lang_form" id="default-form">
                                            <div class="form-group">
                                                <label class="input-label" for="logo_image">Logo </label>
                                                <input type="file" name="logo_image" id="logo_image"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="lang_form" id="default-form">
                                            <div class="form-group">
                                                <label class="input-label" for="cover_image">Cover </label>
                                                <input type="file" name="cover_image" id="cover_image"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-12 col-md-6">
                                        <div class="lang_form" id="default-form">
                                            <div class="form-group">
                                                <label class="input-label" for="type">Segment Types</label>
                                                <select name="type[]" id="type" class="form-control select2"
                                                    multiple="multiple" data-placeholder="-- Select Types --">
                                                    @foreach ($Segment as $item)
                                                        <option value="{{ $item->id }}"
                                                            @if (collect(old('type', []))->contains($item->id)) selected @endif>
                                                            {{ $item->name }} / {{ $item->type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            @endif
                            <div class="btn--container justify-content-end mt-5">
                                <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
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
                                Client List<span class="badge badge-soft-dark ml-2" id="itemCount"></span>
                            </h5>
                            <form class="search-form">
                                <!-- Search -->

                                <div class="input-group input--group">
                                    <input id="datatableSearch_" value="{{ request()?->search ?? null }}" type="search"
                                        name="search" class="form-control" placeholder="Ex: Client Name"
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
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th class="border-0">{{ translate('sl') }}</th>
                                    <th class="border-0">Name</th>
                                    <th class="border-0">Email</th>
                                    <th class="border-0">types</th>
                                    <th class="border-0">Logo</th>
                                    <th class="border-0">Cover</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Action</th>
                                </tr>

                            </thead>

                            <tbody id="set-rows">
                                @foreach ($clients as $key => $client)
                                    <tr>
                                        {{-- Serial No --}}
                                        <td class="text-center">
                                            <span class="mr-3">
                                                {{ $clients->firstItem() + $key }}
                                            </span>
                                        </td>

                                        {{-- Client Name --}}
                                        <td class="text-center">
                                            <span title="{{ $client->name }}" class="font-size-sm text-body mr-3">
                                                {{ Str::limit($client->name, 20, '...') }}
                                            </span>
                                        </td>

                                        {{-- Client Email --}}
                                        <td class="text-center">
                                            <span class="bg-gradient-light text-dark">
                                                {{ $client->email }}
                                            </span>
                                        </td>
                                        {{-- Client Types --}}
                                        <td class="text-center">
                                            @if (!empty($client->segments))
                                                @foreach ($client->segments as $segment)
                                                    <span
                                                        class="badge {{ $segment->status === 'inactive' ? 'badge-danger' : 'badge-success' }} d-block mb-1">
                                                        {{ $segment->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        {{-- Client Created At --}}
                                        <td class="text-center">
                                            <div class="d-inline-block" style="width:50px; height:50px; cursor:pointer;">
                                                <img src="{{ asset($client->logo) }}"
                                                    class="img-fluid rounded open-image-modal" alt="Client Logo"
                                                    style="width:100%; height:100%; object-fit:cover;">
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <div class="d-inline-block" style="width:50px; height:50px; cursor:pointer;">
                                                <img src="{{ asset($client->cover) }}"
                                                    class="img-fluid rounded open-image-modal" alt="Client Logo"
                                                    style="width:100%; height:100%; object-fit:cover;">
                                            </div>
                                        </td>

                                        {{-- Status Toggle (Active/Inactive) --}}
                                        <td class="text-center">
                                            <label class="toggle-switch toggle-switch-sm"
                                                for="status-{{ $client->id }}">
                                                <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                                    {{ $client->status == 'active' ? 'checked' : '' }}
                                                    data-id="status-{{ $client->id }}" data-type="status"
                                                    id="status-{{ $client->id }}">
                                                <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <form action="{{ route('admin.client-side.status', [$client->id]) }}"
                                                method="post" id="status-{{ $client->id }}_form">
                                                @csrf
                                            </form>
                                        </td>
                                        {{-- Action Buttons --}}
                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <a class="btn action-btn btn--primary btn-outline-primary"
                                                    href="javascript:void(0)"
                                                    onclick="loadClientSegments({{ $client->id }})" title="Edit">
                                                    <i class="tio-edit"></i>
                                                </a>

                                                <a class="btn action-btn btn--primary btn-outline-primary"
                                                    href="{{ route('admin.client-side.edit', [$client->id]) }}"
                                                    title="Edit">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                                    href="javascript:" data-id="client-{{ $client->id }}"
                                                    data-message="Want to delete this client ?" title="Delete">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                                <form action="{{ route('admin.client-side.delete', [$client->id]) }}"
                                                    method="post" id="client-{{ $client->id }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    @if (count($clients) !== 0)
                        <hr>
                    @endif
                    <div class="page-area">
                        {!! $clients->links() !!}
                    </div>
                    @if (count($clients) === 0)
                        <div class="empty--data">
                            <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                            <h5>
                                {{ translate('no_data_found') }}
                            </h5>
                        </div>
                    @endif
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>
    <!-- Segment Modal -->
    <div class="modal fade" id="segmentModal" tabindex="-1" aria-labelledby="segmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="segmentModalLabel">Manage Client Segments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Segments Container -->
                    <div id="segmentFieldsContainer"></div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <button type="button" class="btn btn-secondary" onclick="addSegmentRow()">
                            Add More
                        </button>
                        <button type="button" class="btn btn-primary" onclick="saveMultipleSegments()">
                            Save All
                        </button>
                    </div>

                </div>

            </div>
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
    <script>
        let selectedClientId = null;

        // -------------------------------
        // Add Segment Row
        // -------------------------------
        function addSegmentRow(name = "", type = "free", days = "", status = "active", id = null) {
            let checked = status === "active" ? "checked" : "";

            let row = `
    <div class="segment-row row mb-3">
        <input type="hidden" name="segment_id[]" value="${id ?? ''}">

        <div class="col-md-3">
            <label>Name</label>
            <input type="text" name="name[]" class="form-control" value="${name}">
        </div>

        <div class="col-md-2">
            <label>Type</label>
            <select name="type[]" class="form-control">
                <option value="free" ${type === "free" ? "selected" : ""}>Free</option>
                <option value="paid" ${type === "paid" ? "selected" : ""}>Paid</option>
            </select>
        </div>

        <div class="col-md-2">
            <label>Validity Days</label>
            <input type="number" name="validity_days[]" class="form-control" value="${days}">
        </div>

        <div class="col-md-2 d-flex align-items-center">
            <label class="toggle-switch toggle-switch-sm w-100 mt-2">
                <input type="checkbox" name="status_checkbox[]" class="toggle-switch-input segment-status" ${checked}>
                <span class="toggle-switch-label mx-auto">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </div>

        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-danger remove-segment-btn">×</button>
        </div>
    </div>
    `;

            $("#segmentFieldsContainer").append(row);
        }

        // Remove a row
        $(document).on("click", ".remove-segment-btn", function() {
            $(this).closest(".segment-row").remove();
        });

        // -------------------------------
        // Load Client Segments
        // -------------------------------
        function loadClientSegments(clientId) {
            selectedClientId = clientId;

            $.ajax({
                url: "{{ route('admin.segments.get-segment', ':id') }}".replace(':id', clientId),
                method: "GET",
                success: function(response) {

                    $("#segmentFieldsContainer").html(""); // clear old rows

                    if (response.segments.length === 0) {
                        addSegmentRow(); // at least one row
                    } else {
                        response.segments.forEach(seg => {
                            addSegmentRow(seg.name, seg.type, seg.validation_date, seg.status, seg.id);
                        });
                    }

                    $("#segmentModal").modal("show");
                }
            });
        }

        // -------------------------------
        // Save Multiple Segments
        // -------------------------------
        function saveMultipleSegments() {

            let names = $("input[name='name[]']").map((_, el) => el.value).get();
            let types = $("select[name='type[]']").map((_, el) => el.value).get();
            let days = $("input[name='validity_days[]']").map((_, el) => el.value).get();

            // Convert checkbox to enum 'active' / 'inactive'
            let statuses = $(".segment-status").map((_, el) => el.checked ? "active" : "inactive").get();

            let segment_ids = $("input[name='segment_id[]']").map((_, el) => el.value).get();

            $.ajax({
                url: "{{ url('admin/segments/store-multiple') }}",
                method: "POST",
                data: {
                    client_id: selectedClientId,
                    name: names,
                    type: types,
                    validity_days: days,
                    status: statuses,
                    segment_id: segment_ids,
                    _token: "{{ csrf_token() }}"
                },

                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: "Segments Saved Successfully",
                        toast: true,
                        position: "top-right",
                        showConfirmButton: false,
                        timer: 2000
                    });

                    $("#segmentModal").modal("hide");
                    window.location.reload();
                },

                error: function(xhr) {
                    console.log("Error:", xhr.responseText);
                }
            });
        }
    </script>
@endpush
