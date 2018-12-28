
<div class="analytics-options">

  <div class="select-analytics flex-layout">

    <h3>Select options</h3>
    <ul class="oescape-icon-btns">
      <li class="icon-btn">
        <a href="/analytics/ad" class="inactive">AD</a>
      </li>
      <li class="icon-btn">
        <a href="/analytics/glaucoma" class="active">GL</a>
      </li>
      <li class="icon-btn">
        <a href="/analytics/medicalRetina" class="selected">MR</a>
      </li>
      <li class="icon-btn">
        <a href="/analytics/vr" class="inactive">VR</a>
      </li>
    </ul>
    <!-- icon-btns -->
  </div>
  <div class="specialty">Medical Retina</div>
  <div class="service flex-layout">
    <div class="service-selected" id="js-service-selected-filter">James Morgan</div><!-- OE UI Filter options (id: select-service) -->
    <div class="oe-filter-options" id="oe-filter-options-select-service" data-filter-id="select-service"><!-- simple button to popup filter options -->
      <button class="oe-filter-btn green hint" id="oe-filter-btn-select-service">
          <i class="oe-i filter pro-theme"></i>
      </button><!-- Filter options. Popup is JS positioned: top-left, top-right, bottom-left, bottom-right -->
      <div class="filter-options-popup" id="filter-options-popup-select-service" style="display: none;"><!-- provide close (for touch) -->
        <div class="close-icon-btn">
          <i class="oe-i remove-circle medium pro-theme"></i>
        </div>
        <div class="flex-layout flex-top">
          <div class="options-group" data-filter-ui-id="js-service-selected-filter">
            <!-- <h3>Title (optional)</h3> -->
            <ul class="btn-list">
              <li>David Haider</li>
              <li>Afsar Jafree</li>
              <li>Luke Membrey</li>
              <li class="selected">James Morgan</li>
              <li>Malcolm Woodcock</li>
              <li>Glaucoma (All)</li>
              <li>Medical Retina (All)</li>
              <li>Vitreoretinal (All)</li>
            </ul>
          </div><!-- options-group -->
        </div><!-- .flex -->
      </div><!-- filter-options-popup -->
    </div><!-- .oe-filter-options -->
  </div>
  <div class="specialty-options">

    <div class="view-mode flex-layout">
      <button class="analytics-section pro-theme cols-3" id="js-btn-clinical"
              data-section="#js-hs-chart-analytics-clinical"
              data-tab="#js-charts-clinical" >
          Clinical
      </button>
      <button class="analytics-section pro-theme cols-3 selected" id="js-btn-service"
              data-section="#js-hs-chart-analytics-service"
              data-tab="#js-charts-service" >
          Service
      </button>
      <button class="analytics-section pro-theme cols-3" id="js-btn-custom"
              data-section="#js-hs-chart-analytics-custom"
              data-tab="#js-custom-data-filter" >
          Custom
      </button>
      <button class="analytics-section pro-theme cols-3 disabled" id="js-btn-research">
          Research
      </button>
    </div>


    <div id="js-charts-clinical" style="display: none;">
      <ul class="charts">
        <li><a href="#" id="js-hs-diagnoses">Diagnoses (2556)</a></li>
      </ul>
    </div>

    <div id="js-charts-service" style="">
      <ul class="charts">
        <li><a href="#" id="js-hs-app-new" class="selected">Appointments: New (248)</a></li>
        <li><a href="#" id="js-hs-app-follow-up">Appointments: Follow Up (397)</a></li>
        <li><a href="#" id="js-hs-app-follow-up">Appointments: Delayed (1)</a></li>
      </ul>
    </div><div id="js-custom-data-filter" style="display: none;"><h3>Custom Data Filters</h3>

      <div class="custom-filters flex-layout">
        <ul class="filters-selected cols-9">
          <li>Ages: <span id="js-chart-filter-age">All</span></li>
          <li>Ages:
            <select>
                <?php for ($i=0; $i<120; $i++) { ?>
                    <option><?= $i ?></option>
                <?php } ?>
            </select>
              to
            <select>
                <?php for ($i=0; $i<120; $i++) { ?>
                    <option><?= $i ?></option>
                <?php } ?>
            </select>
          </li>
          <li>Diagnosis: <span id="js-chart-filter-diagnosis">All</span></li>
          <li>Plot: <span id="js-chart-filter-plot">VA (absolute)</span></li>
          <li>Protocol: <span id="js-chart-filter-protocol">None</span></li>
        </ul>

        <div class="flex-item-bottom"><!-- OE UI Filter options (id: custom-filters) -->
          <div class="oe-filter-options" id="oe-filter-options-custom-filters" data-filter-id="custom-filters"><!-- simple button to popup filter options -->
            <button class="oe-filter-btn green hint" id="oe-filter-btn-custom-filters"><i class="oe-i filter pro-theme"></i></button><!-- Filter options. Popup is JS positioned: top-left, top-right, bottom-left, bottom-right -->
            <div class="filter-options-popup" id="filter-options-popup-custom-filters" style="display: none;"><!-- provide close (for touch) -->
              <div class="close-icon-btn">
                <i class="oe-i remove-circle medium pro-theme"></i>
              </div>
              <div class="flex-layout flex-top">
                <div class="options-group" data-filter-ui-id="js-chart-filter-age">
                  <h3>Age</h3>
                  <ul class="btn-list">
                    <li class="selected">All</li>
                    <li>Range</li>
                  </ul>
                </div><!-- options-group -->
                <div class="options-group" data-filter-ui-id="js-chart-filter-diagnosis">
                  <h3>Diagnosis</h3>
                  <ul class="btn-list">
                    <li class="selected">All</li>
                    <li>Macular degeneration</li>
                    <li>Diabetic Macular Oedema</li>
                    <li>BRVO</li>
                    <li>CRVO</li>
                    <li>Hemivein</li>
                  </ul>
                </div><!-- options-group -->
                <div class="options-group" data-filter-ui-id="js-chart-filter-plot">
                  <h3>Plot</h3>
                  <ul class="btn-list">
                    <li class="selected">VA (absolute)</li>
                    <li>VA (change)</li>
                    <li>SFT</li>
                  </ul>
                </div><!-- options-group -->
                <div class="options-group" data-filter-ui-id="js-chart-filter-protocol">
                  <h3>Protocol</h3>
                  <ul class="btn-list">
                    <li class="selected">None</li>
                    <li>PRN</li>
                    <li>Treat and extend</li>
                    <li>Protocol EK</li>
                    <li>Protocol Maidstone</li>
                  </ul>
                </div><!-- options-group -->
              </div><!-- .flex -->
            </div><!-- filter-options-popup -->
          </div><!-- .oe-filter-options -->
        </div>
      </div><!-- .chart-filters -->
    </div><!-- #js-custom-data-filter -->

		<h3>Filter by Date</h3>
    <div class="flex-layout">
      <input type="text" class="pro-theme cols-5" placeholder="from">
      <input type="text" class="pro-theme cols-5" placeholder="to">
    </div>

    <div class="row">
      <button id="js-clear-date-range" class="pro-theme">View all dates</button>
    </div>


    <button class="pro-theme green hint cols-full update-chart-btn">Update Chart</button>

    <div class="extra-actions">
      <button class="pro-theme cols-full">Download (CSV)</button>
    </div></div><!-- .specialty-options -->
</div>