@extends('layouts.admin.app')

@section('title', 'Gift Occasions List')

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
                    Add Gift Occasions
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
                        <form action="{{ route('admin.GiftOccasions.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @if ($language)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label" for="title">Title</label>
                                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Title" required>
                                        </div>
                                    </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label" for="icon">Icons (Multiple)</label>
                                            <input type="file" name="icon[]" id="icon" class="form-control" multiple>
                                        </div>
                                    </div>
                                </div>

                                <div id="message_container">
                                    <div class="row align-items-end mb-3">
                                        <div class="col-md-10">
                                            <div class="form-group mb-0">
                                                <label class="input-label">Message</label>
                                                <input type="text" name="messages[]" class="form-control" placeholder="Enter Message" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary btn-block" onclick="addMessageField()">Add More</button>
                                        </div>
                                    </div>
                                </div>

                         
                            @endif
                            <div class="btn--container justify-content-end mt-5">
                                <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header py-2 border-0">
                                <div class="search--button-wrapper">
                                    <h5 class="card-title">
                                        Gift Occasions<span class="badge badge-soft-dark ml-2" id="itemCount"></span>
                                    </h5>
                                    <form class="search-form">
                                        <!-- Search -->

                                        <div class="input-group input--group">
                                            <input id="datatableSearch_" value="{{ request()?->search ?? null }}"
                                                type="search" name="search" class="form-control"
                                                placeholder="Ex: Gift Occasions" aria-label="Search">
                                            <button type="submit" class="btn btn--secondary"><i
                                                    class="tio-search"></i></button>
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
                                            <th class="border-0">Title</th>
                                            <th class="border-0">Messages</th>
                                            <th class="border-0">Gallery</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Action</th>
                                        </tr>

                                    </thead>

                                    <tbody id="set-rows">
                                        @foreach ($GiftOccasions as $key => $item)
                                            <tr>
                                                {{-- Serial No --}}
                                                <td class="text-center">
                                                    <span class="mr-3">
                                                        {{ $GiftOccasions->firstItem() + $key }}
                                                    </span>
                                                </td>


                                                {{-- Title --}}
                                                <td class="text-center">
                                                    <span title="{{ $item->title }}" class="font-size-sm text-body mr-3">
                                                        {{ Str::limit($item->title, 20, '...') }}
                                                    </span>
                                                </td>

                                                {{-- Messages --}}
                                                <td class="text-center">
                                                    @php($messages = $item->message ?: [])
                                                    <span class="font-size-sm text-body mr-3">
                                                        @if(is_array($messages))
                                                            {{ Str::limit(implode(', ', $messages), 30, '...') }}
                                                        @else
                                                            {{ Str::limit($item->message, 30, '...') }}
                                                        @endif
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <a class="btn action-btn btn--primary btn-outline-primary   "
                                                        href="javascript:void(0)"
                                                        onclick="showGalleryModal({{ $item->id }})"
                                                        title="View Details">
                                                        <i class="tio-visible"></i>
                                                    </a>

                                                </td>
                                                {{-- Status Toggle (Active/Inactive) --}}
                                                <td class="text-center">
                                                    <label class="toggle-switch toggle-switch-sm"
                                                        for="status-{{ $item->id }}">
                                                        <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                                            {{ $item->status == 'active' ? 'checked' : '' }}
                                                            data-id="status-{{ $item->id }}" data-type="status"
                                                            id="status-{{ $item->id }}">
                                                        <span class="toggle-switch-label mx-auto">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                    <form action="{{ route('admin.GiftOccasions.status', [$item->id]) }}"
                                                        method="post" id="status-{{ $item->id }}_form">
                                                        @csrf
                                                    </form>
                                                </td>
                                                {{-- Action Buttons --}}
                                                <td>
                                                    <div class="btn--container justify-content-center">
                                                        <a class="btn action-btn btn--primary btn-outline-primary"
                                                            href="{{ route('admin.GiftOccasions.edit', [$item->id]) }}"
                                                            title="Edit">
                                                            <i class="tio-edit"></i>
                                                        </a>
                                                        <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                                            href="javascript:" data-id="client-{{ $item->id }}"
                                                            data-message="Want to delete this client ?" title="Delete">
                                                            <i class="tio-delete-outlined"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.GiftOccasions.delete', [$item->id]) }}"
                                                            method="post" id="client-{{ $item->id }}">
                                                            @csrf @method('delete')
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                            @if (count($GiftOccasions) !== 0)
                                <hr>
                            @endif
                            <div class="page-area">
                                {!! $GiftOccasions->links() !!}
                            </div>
                            @if (count($GiftOccasions) === 0)
                                <div class="empty--data">
                                    <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                                        alt="public">
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

            <!-- Gallery Modal -->
            <div class="modal fade" id="galleryModal" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="galleryModalLabel">Gallery Images</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="gallery-content" class="row">
                                <!-- Images yahan load hongi -->
                                <div class="col-12 text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endsection

        @push('script_2')
            <script src="{{ asset('public/assets/admin') }}/js/view-pages/segments-index.js"></script>

            <script src="{{ asset('public/assets/admin') }}/js/view-pages/client-side-index.js"></script>
            {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

            <script>
                function addMessageField() {
                    let html = `
                    <div class="row align-items-end mb-3 message-row">
                        <div class="col-md-10">
                            <div class="form-group mb-0">
                                <input type="text" name="messages[]" class="form-control" placeholder="Enter Message" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-block" onclick="removeMessageField(this)">Remove</button>
                        </div>
                    </div>`;
                    $('#message_container').append(html);
                }

                function removeMessageField(btn) {
                    $(btn).closest('.message-row').remove();
                }

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
                function showGalleryModal(occasionId) {
                    // Modal open karein
                    $('#galleryModal').modal('show');

                    // Gallery content load karein
                    $.ajax({
                        url: '{{ route('admin.GiftOccasions.gallery', ':id') }}'.replace(':id', occasionId),
                        type: 'GET',
                        success: function(response) {
                            let galleryHtml = '';

                            if (response.images && response.images.length > 0) {
                                response.images.forEach(function(image) {
                                    // Assuming `image.url` is like 'uploads/file.jpg' (relative to storage/app/public)
                                    const publicUrl = `${image.url}`;

                                    galleryHtml += `
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="card">
                                            <img src="public/${publicUrl}" 
                                                class="card-img-top" 
                                                alt="Gallery Image" 
                                                style="height: 200px; object-fit: cover; cursor: pointer;" 
                                                onclick="viewFullImage('public${publicUrl}')">
                                        </div>
                                    </div>
                                `;
                                });

                            } else {
                                galleryHtml = '<div class="col-12 text-center"><p>No images found</p></div>';
                            }

                            $('#gallery-content').html(galleryHtml);
                        },
                        error: function() {
                            $('#gallery-content').html(
                                '<div class="col-12 text-center"><p class="text-danger">Error loading images</p></div>'
                                );
                        }
                    });
                }

                function viewFullImage(imageUrl) {
                    window.open(imageUrl, '_blank');
                }
            </script>
        @endpush
