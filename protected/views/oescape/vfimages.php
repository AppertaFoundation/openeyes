<div class="mdl-tabs mdl-js-tabs">
    <div class="mdl-tabs__tab-bar">
        <a href="#tab1-panel" class="mdl-tabs__tab is-active">Fields</a>
        <a href="#tab2-panel" class="mdl-tabs__tab">OCT</a>
        <a href="#tab3-panel" class="mdl-tabs__tab">Photo</a>
    </div>
    <div class="mdl-tabs__panel is-active" id="tab1-panel">
        <div class="right-side mdl-cell-top-right">
            <span class="side-title">Right</span>
            <div id="vfgreyscale_right" class="vfimage">

            </div>
            <span id="vfgreyscale_right_cache" class="vfthumbnail-hidden">
            </span>
            <div id="vfcolorplot_right" class="vfcolorplot">
                <?php $this->renderPartial('//oescape/vfcolourplot_right');?>
            </div>

        </div>
        <div class="left-side mdl-cell-top-right">
            <span class="side-title">Left</span>
            <div id="vfgreyscale_left" class="vfimage">

            </div>
            <span id="vfgreyscale_left_cache" class="vfthumbnail-hidden">
            </span>
            <div id="vfcolorplot_left" class="vfcolorplot">
                <?php $this->renderPartial('//oescape/vfcolourplot_left');?>
            </div>
        </div>
        <div id="regression_chart" class="mdl-cell-bottom-right">

        </div>
    </div>
    <div class="mdl-tabs__panel" id="tab2-panel">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                <div id="oct_images" class="octimage">

                </div>
                <span id="oct_images_cache" class="octimage-hidden">

                </span>
            </div>
        </div>
    </div>
    <div class="mdl-tabs__panel" id="tab3-panel">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--6-col">
                <span class="side-title">Right</span>
                <div id="kowa_right" class="kowaimage">

                </div>
            </div>
            <div class="mdl-cell mdl-cell--6-col">
                <span class="side-title">Left</span>
                <div id="kowa_left" class="kowaimage">

                </div>
            </div>
        </div>
    </div>
</div>


