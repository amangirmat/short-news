<div class="short-news-admin-wrapper" style="padding: 10px 0;">
    <div class="row">
        <div class="col-md-9">
            <div class="form-group mb-3">
                <label class="control-label"><strong>Main Highlights (3 Points)</strong></label>
                <textarea name="short_news_points" 
                          class="form-control" 
                          rows="4" 
                          placeholder="1. First Point&#10;2. Second Point&#10;3. Third Point">{{ $points }}</textarea>
                <p class="help-block">
                    <small>Enter each point on a new line. (e.g., for Amharic, enter Amharic points here).</small>
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label class="control-label"><strong>Vertical Cover (9:16)</strong></label>
                {!! Form::mediaImage('short_news_v_image', $vImage) !!}
                <p class="help-block"><small>Portrait image. Falls back to featured image if empty.</small></p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Force the meta box to be visible even if Botble tries to hide it */
    #short_news_additional_fields, 
    .meta-boxes[id*="short_news"] {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
    }
</style>