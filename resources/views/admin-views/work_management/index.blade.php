@extends('layouts.admin.app')

@section('title',"Work Management List")

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
<style>
    /* Dropdown options - selected option ka background highlight */
    .select2-results__option[aria-selected="true"] {
        background-color: #005555 !important; /* Bootstrap primary */
        color: #fff !important;
    }

    /* Hover effect on options */
    .select2-results__option--highlighted[aria-selected] {
        background-color: #005555 !important;
        color: #fff !important;
    }

    /* Selected tags (neeche input me show hone wale items) */
    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
        background-color: #005555;   /* blue tag */
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
                    <img src="{{asset('public/assets/admin/img/condition.png')}}" class="w--26" alt="">
                </span>
                <span>
                    Work Management
                </span>
            </h1>
        </div>
        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))
        <!-- End Page Header -->
        <div class="row g-3">

            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                Work Management<span class="badge badge-soft-dark ml-2" id="itemCount"></span>
                            </h5>
                            <form  class="search-form">
                                <!-- Search -->

                                <div class="input-group input--group">
                                    <input id="datatableSearch_" value="{{ request()?->search ?? null }}" type="search" name="search" class="form-control"
                                            placeholder="Ex: Voucher Name" aria-label="Search" >
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                            @if(request()->get('search'))
                            <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
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
                                    <th class="border-0">{{translate('sl')}}</th>
                                    <th class="border-0">Voucher Type</th>
                                    <th class="border-0">Guide Title</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Last Modified</th>
                                    <th class="border-0">Action</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach($WorkManagements as $key => $UsageTerm)
                                    <tr>
                                        {{-- Serial No --}}
                                        <td class="text-center">
                                            <span class="mr-3">
                                                {{ $WorkManagements->firstItem() + $key }}
                                            </span>
                                        </td>

                                        {{-- Voucher Type --}}
                                        <td class="text-center">
                                            <span title="{{ $UsageTerm->voucher_name }}" class="font-size-sm text-body mr-3">
                                                {{ Str::limit($UsageTerm->voucher_name, 20, '...') }}
                                            </span>
                                        </td>

                                        {{-- Guide Title --}}
                                        <td class="text-center">
                                            <span title="{{ $UsageTerm->guide_title }}" class="font-size-sm text-body mr-3">
                                                {{ Str::limit($UsageTerm->guide_title, 20, '...') }}
                                            </span>
                                        </td>

                                        {{-- Status Toggle --}}
                                        <td class="text-center">
                                            <label class="toggle-switch toggle-switch-sm" for="status-{{ $UsageTerm->id }}">
                                                <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                                    {{ $UsageTerm->status == 'active' ? 'checked' : '' }}
                                                    data-id="status-{{ $UsageTerm->id }}"
                                                    data-type="status"
                                                    id="status-{{ $UsageTerm->id }}">
                                                <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <form action="{{ route('admin.workmanagement.status', [$UsageTerm->id]) }}"
                                                method="post" id="status-{{ $UsageTerm->id }}_form">
                                                @csrf
                                            </form>
                                        </td>

                                        {{-- Last Modified --}}
                                        <td class="text-center">
                                            <span title="{{ $UsageTerm->updated_at }}" class="font-size-sm text-body mr-3">
                                                {{ Str::limit($UsageTerm->updated_at, 20, '...') }}
                                            </span>
                                        </td>

                                        {{-- Action Buttons --}}
                                        <td>
                                            <div class="btn--container justify-content-center">
                                                {{-- View Button --}}
                                                <a class="btn action-btn btn--primary btn-outline-primary   "
                                                href="javascript:void(0)"
                                                onclick="viewWorkDetails({{ $UsageTerm->id }})"
                                                title="View Details">
                                                    <i class="tio-visible"></i>
                                                </a>

                                                {{-- Edit Button --}}
                                                <a class="btn action-btn btn--primary btn-outline-primary"
                                                href="{{ route('admin.workmanagement.edit', [$UsageTerm->id]) }}"
                                                title="Edit">
                                                    <i class="tio-edit"></i>
                                                </a>

                                                {{-- Delete Button --}}
                                                <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                                href="javascript:"
                                                data-id="client-{{ $UsageTerm->id }}"
                                                data-message="Want to delete this work management item?"
                                                title="Delete">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                                <form action="{{ route('admin.workmanagement.delete', [$UsageTerm->id]) }}"
                                                    method="post" id="client-{{ $UsageTerm->id }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>



                    @if(count($WorkManagements) !== 0)
                    <hr>
                    @endif
                    <div class="page-area">
                        {!! $WorkManagements->links() !!}
                    </div>
                    @if(count($WorkManagements) === 0)
                    <div class="empty--data">
                        <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                    @endif
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>


        {{-- View Details Modal --}}
        <div class="modal fade" id="viewWorkModal" tabindex="-1" aria-labelledby="viewWorkModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title pb-3" id="viewWorkModalLabel" style="    font-size: 20px;color: white;">
                            Work Management Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                          {{-- Loading Spinner --}}
                        <div id="modalLoading" class="text-center py-5">
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <div class="spinner-border text-primary me-2" role="status" style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden"></span>
                                </div>
                                {{-- <div class="spinner-grow text-primary" role="status" style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden"></span>
                                </div> --}}
                            </div>
                            <p class="text-muted mt-2">Loading details...</p>
                        </div>

                        {{-- Content Container --}}
                        <div id="modalContent" style="display: none;">
                            {{-- Guide Title --}}
                            <div class="mb-4">
                                <h6 class="text-muted mb-1">Guide Title</h6>
                                <h4 class="fw-bold" id="modalGuideTitle"></h4>
                            </div>

                            {{-- Voucher Type --}}
                            {{-- <div class="mb-4">
                                <h6 class="text-muted mb-1">Voucher Type</h6>
                                <span class="badge bg-info" id="modalVoucherType"></span>
                            </div> --}}

                            {{-- Status --}}
                            <div class="mb-4">
                                <h6 class="text-muted mb-1">Status</h6>
                                <span class="badge" id="modalStatus"></span>
                            </div>

                            {{-- Dates --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Created At</h6>
                                    <p class="mb-0" id="modalCreatedAt"></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Last Updated</h6>
                                    <p class="mb-0" id="modalUpdatedAt"></p>
                                </div>
                            </div>

                            <hr>

                            {{-- Sections --}}
                            <div class="mb-3">
                                <h5 class="fw-bold mb-3">
                                    <i class="tio-list me-2"></i>Sections & Steps
                                </h5>
                                <div id="modalSections"></div>
                            </div>
                        </div>

                        {{-- Error Container --}}
                        <div id="modalError" style="display: none;">
                            <div class="alert alert-danger" role="alert">
                                <i class="tio-error me-2"></i>
                                <span id="modalErrorMessage">Error loading data. Please try again.</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="tio-clear me-1"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>


@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/segments-index.js"></script>

 <script src="{{asset('public/assets/admin')}}/js/view-pages/client-side-index.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
    $(function () {
        $('#type').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: $('#type').data('placeholder'),
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>

{{-- JavaScript --}}
<script>
function viewWorkDetails(workId) {
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('viewWorkModal'));
    modal.show();

    // Reset modal state
    $('#modalLoading').show();
    $('#modalContent').hide();
    $('#modalError').hide();

    // Fetch data
    $.ajax({
        url: "{{ route('admin.workmanagement.show', '') }}/" + workId,
        type: "GET",
        dataType: "json",

        success: function(response) {
            // Hide loading
            $('#modalLoading').hide();
            $('#modalContent').show();

            // Populate data
            $('#modalGuideTitle').text(response.guide_title || 'N/A');

            // Status badge
            const statusBadge = $('#modalStatus');
            if (response.status === 'active') {
                statusBadge.removeClass('bg-danger').addClass('bg-success').text('Active');
            } else {
                statusBadge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
            }

            // Dates
            $('#modalCreatedAt').text(response.created_at || 'N/A');
            $('#modalUpdatedAt').text(response.updated_at || 'N/A');

            // Sections (already array)
            let sections = Array.isArray(response.sections) ? response.sections : [];

            let sectionsHtml = '';
            if (sections.length > 0) {
                $.each(sections, function(index, section) {
                    sectionsHtml += `
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="tio-folder-bookmarked me-2"></i>${section.title}
                                </h6>
                            </div>
                            <div class="card-body">
                                <ol class="mb-0">
                    `;

                    $.each(section.steps, function(stepIndex, step) {
                        sectionsHtml += `<li class="mb-2">${step}</li>`;
                    });

                    sectionsHtml += `
                                </ol>
                            </div>
                        </div>
                    `;
                });
            } else {
                sectionsHtml = '<p class="text-muted fst-italic">No sections available</p>';
            }

            $('#modalSections').html(sectionsHtml);
        },

        error: function(xhr, status, error) {
            // Hide loading
            $('#modalLoading').hide();
            $('#modalError').show();

            let errorMessage = 'Error loading data. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            $('#modalErrorMessage').text(errorMessage);
        }
    });
}

</script>
@endpush
