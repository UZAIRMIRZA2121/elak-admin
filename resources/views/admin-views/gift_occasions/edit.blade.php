@extends('layouts.admin.app')

@section('title', ' Gift Occasions Edit')

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
                    Edit Gift Occasions
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.GiftOccasions.update', [$GiftOccasions['id']]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                    @php($language = $language->value ?? null)
                    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                    @if ($language)
                        @php($messages = json_decode($GiftOccasions->message, true) ?? ($GiftOccasions->message ? [$GiftOccasions->message] : []))
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="title">Title</label>
                                    <input type="text" name="title" value="{{ $GiftOccasions->title }}" id="title" class="form-control" placeholder="Enter Title" required>
                                </div>
                            </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="icon">Icon (Multiple)</label>
                                    <input type="file" name="icon[]" id="icon" class="form-control" multiple>
                                </div>
                            </div>
                        </div>
                          <div class="row ">
                            <div class="col-12">
                                <label class="input-label">Current Icons</label>
                                @if (!empty($GiftOccasions->icon))
                                    @php($icons = json_decode($GiftOccasions->icon, true))
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach ($icons as $key => $img)
                                            <div id="img-{{ $img }}" class="position-relative d-inline-block mr-2 mb-2"
                                                style="width: 120px; overflow: hidden; border-radius: 6px; border: 1px solid #ddd;">
                                                <a class="btn btn-danger deleteOccasion"
                                                    data-id="{{ $GiftOccasions->id }}"
                                                    data-img-id="{{ $img }}"
                                                    style="position: absolute; top: 4px; right: 4px; padding: 0; width: 22px; height: 22px; line-height: 18px; font-size: 14px; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 10;">
                                                    <i class="tio-delete"></i>
                                                </a>
                                                <img src="{{ asset('public/' . $img) }}" class="img-thumbnail"
                                                    style="width: 120px; height: 120px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div id="message_container">
                            <div class="row align-items-end mb-3">
                                <div class="col-md-10">
                                    <div class="form-group mb-0">
                                        <label class="input-label">Message</label>
                                        <input type="text" name="messages[]" value="{{ count($messages) > 0 ? $messages[0] : '' }}" class="form-control" placeholder="Enter Message" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-block" onclick="addMessageField()">Add More</button>
                                </div>
                            </div>

                            @for ($i = 1; $i < count($messages); $i++)
                                <div class="row align-items-end mb-3 message-row">
                                    <div class="col-md-10">
                                        <div class="form-group mb-0">
                                            <input type="text" name="messages[]" value="{{ $messages[$i] }}" class="form-control" placeholder="Enter Message" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-block" onclick="removeMessageField(this)">Remove</button>
                                    </div>
                                </div>
                            @endfor
                        </div>

                      
                    @endif

                    <div class="btn--container justify-content-end mt-5">
                        <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/view-pages/client-side-index.js"></script>
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

        $(document).on('click', '.deleteOccasion', function() {
            let id = $(this).data('id');
            let img = $(this).data('img-id');
            let url = "{{ route('admin.GiftOccasions.galleryDestroy', ':id') }}".replace(':id', id);

            if (confirm('Are you sure you want to delete this image?')) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: {
                        img: img,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        window.location.reload();
                    }
                });
            }
        });
    </script>
@endpush
