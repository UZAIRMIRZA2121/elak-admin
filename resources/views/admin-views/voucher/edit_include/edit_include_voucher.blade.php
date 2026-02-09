



{{-- How It Works --}}
<div class="section-card rounded p-4 mb-4 section3 two_four_complete" id="how_it_work_main">
    <h3 class="h5 fw-semibold mb-4"> {{ translate('How It Works') }}</h3>
    <p class="text-muted">Instructions for using your voucher</p>
    <div class="card ">
        <div class="card-body" id="workList">

        </div>
    </div>
</div>

{{--  Terms & Conditions --}}
<div class="section-card rounded p-4 mb-4  section3  " id="term_condition_main">
    <h3 class="h5 fw-semibold mb-2"> {{ translate('Terms & Conditions') }}</h3>
    <p class="text-muted">Set your business terms</p>
    <div class="card border shadow-sm mt-3">
        <div class="card-body">
            <div id="usageTerms" class="row">
            </div>
        </div>
    </div>
</div> 


<!-- Action Buttons -->
<div class="col-md-12">
    <div class="btn--container justify-content-end">
        <button type="reset" id="reset_btn"
            class="btn btn--reset">{{ translate('messages.reset') }}</button>
        <button type="submit" id="submitButton"  class="btn btn--primary">{{ translate('messages.submit') }}</button>
    </div>
</div>
