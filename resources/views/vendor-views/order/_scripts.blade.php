<script>
    "use strict";

    $(document).ready(function() {

        $('.self-delivery-warning').on('click', function(event) {
            event.preventDefault();
            toastr.info("{{ translate('messages.Self_Delivery_is_Disable') }}", {
                CloseButton: true,
                ProgressBar: true
            });
        });

        $('.cancelled-status').on('click', function() {
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: '{{ translate('messages.Change status to canceled ?') }}',
                type: 'warning',
                html: `
                <select class="form-control js-select2-custom mx-1" name="reason" id="reason">
                    @foreach ($reasons as $r)
                        <option value="{{ $r->reason }}">{{ $r->reason }}</option>
                    @endforeach
                </select>
            `,
                showCancelButton: true,
                confirmButtonColor: '#FC6A57',
                confirmButtonText: '{{ translate('messages.yes') }}',
                cancelButtonText: '{{ translate('messages.no') }}',
                reverseButtons: true,
                didOpen: () => {
                    $('.js-select2-custom').select2({
                        minimumResultsForSearch: 5,
                        width: '100%',
                        placeholder: "Select Reason",
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let reason = $('#reason').val();
                    window.location.href = '{!! route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'canceled']) !!}&reason=' + reason;
                }
            });
        });

        $('.order-status-change-alert').on('click', function() {

            let route = $(this).data('url');
            let message = $(this).data('message');
            let verification = $(this).data('verification');
            let processing = $(this).data('processing-time') ?? false;

            if (verification) {

                Swal.fire({
                    title: '{{ translate('Enter order verification code') }}',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonColor: '#FC6A57',
                    confirmButtonText: '{{ translate('messages.submit') }}',
                    preConfirm: (otp) => {
                        window.location.href = route + '&otp=' + otp;
                    }
                });

            } else if (processing) {

                Swal.fire({
                    title: '{{ translate('messages.Are you sure ?') }}',
                    showCancelButton: true,
                    confirmButtonColor: '#FC6A57',
                    confirmButtonText: '{{ translate('messages.submit') }}',

                    html: `
                    ${message}<br/><br/>
                    <label>{{ translate('Enter Processing time') }}</label>

                    <div class="d-flex gap-2">
                        <input type="number" id="processing_value" class="swal2-input" style="margin:0;" />
                        <select id="processing_unit" class="swal2-input" style="margin:0;">
                            <option value="min">Min</option>
                            <option value="hour">Hour</option>
                            <option value="day">Day</option>
                        </select>
                    </div>
                `,

                    preConfirm: () => {
                        let value = $('#processing_value').val();
                        let unit = $('#processing_unit').val();

                        if (!value) {
                            Swal.showValidationMessage('Please enter processing time');
                            return false;
                        }

                        let minutes = parseFloat(value);

                        if (unit === 'hour') minutes *= 60;
                        if (unit === 'day') minutes *= 1440;

                        window.location.href = route + '&processing_time=' + minutes;
                    }
                });

            } else {

                Swal.fire({
                    title: '{{ translate('messages.Are you sure ?') }}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FC6A57',
                    confirmButtonText: '{{ translate('messages.Yes') }}',
                    cancelButtonText: '{{ translate('messages.No') }}',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = route;
                    }
                });

            }

        });

    });
</script>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="modalPreviewImage" src="" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const previewImages = document.querySelectorAll(".preview-image");
        const modalImage = document.getElementById("modalPreviewImage");
        const imageModal = new bootstrap.Modal(document.getElementById("imagePreviewModal"));

        previewImages.forEach(img => {
            img.addEventListener("click", function() {
                modalImage.src = this.getAttribute("data-image");
                imageModal.show();
            });
        });
    });
</script>
