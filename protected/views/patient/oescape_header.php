<nav class="oescape-header flex-layout">
    <div>
        <!-- resize left chart area and Left & Right Eye buttons -->
        <!-- note: no whitespace (gaps) in HTML, SVG is styled by CSS -->
        <button class="js-oes-area-resize" data-area="small">
            <svg class="svg-size-icon" width="38" height="19" viewBox="0 0 38 19">
                <title>oescape-chart-size-small</title>
                <rect x="1.5" y="1.5" width="34" height="15"></rect>
                <rect x="3.5" y="3.5" width="8" height="11"></rect>
            </svg>
        </button>
        <button class="js-oes-area-resize selected" data-area="medium">
            <svg class="svg-size-icon" width="38" height="19" viewBox="0 0 38 19">
                <title>oescape-chart-size-medium</title>
                <rect x="1.5" y="1.5" width="34" height="15"></rect>
                <rect x="3.5" y="3.5" width="16" height="11"></rect>
            </svg>
        </button>
        <button class="js-oes-area-resize" data-area="large">
            <svg class="svg-size-icon" width="38" height="19" viewBox="0 0 38 19">
                <title>oescape-chart-size-large</title>
                <rect x="1.5" y="1.5" width="34" height="15"></rect>
                <rect x="3.5" y="3.5" width="24" height="11"></rect>
            </svg>
        </button>
        <button class="js-oes-area-resize" data-area="full">
            <svg class="svg-size-icon" width="38" height="19" viewBox="0 0 38 19">
                <title>oescape-chart-size-full</title>
                <rect x="1.5" y="1.5" width="34" height="15"></rect>
                <rect x="3.5" y="3.5" width="30" height="11"></rect>
            </svg>
        </button>
        <button class="js-oes-eyeside-right selected">Right Eye</button>
        <button class="js-oes-eyeside-left">Left Eye</button>
    </div>

    <div class="nav-title">
        <div class="title"><?= isset($episode)? $episode->getSubspecialtyText():'OEscape' ?></div>
            <?php $episodes_list = array();
            $subspecialty_labels = array();
            extract($this->getEpisodes());
            if (is_array($ordered_episodes)):
                foreach ($ordered_episodes as $specialty_episodes): ?>
                  <ul class="oescape-icon-btns" style="font-size: 0;">
                      <?php
                      foreach ($specialty_episodes['episodes'] as $i => $episode) {
                          // TODO deal with support services possibly?
                          $id = $episode->getSubspecialtyID();
                          $subspecialty_name = $episode->getSubspecialtyText();
                          if ($id) {
                              $tag = $episode->subspecialty->ref_spec;
                          }
                          if (!array_key_exists($id, $subspecialty_labels)) {
                              $subspecialty_labels[$id] = $subspecialty_name;
                          }
                          if (!array_key_exists($id, $episodes_list)) {
                              $episodes_list[$id] = $episode;
                          }
                      }
                      $subspecialties = array_map(function ($v) {
                          return array($v->id, $v->name, $v->ref_spec);
                      }, Subspecialty::model()->findAllByAttributes(array('name'=> array('Cataract', 'Glaucoma', 'Medical Retina', 'General Ophthalmology'))));
                      foreach ($subspecialties as $subspecialty) { ?>
                            <li class="icon-btn"
                                data-subspecialty-id="<?= $subspecialty[0] ?>">
                              <a class="<?= in_array($subspecialty[0], array_keys($episodes_list))?'active':'inactive' ?>"
                                 href=" <?= in_array($subspecialty[0], array_keys($episodes_list))?Yii::app()->createUrl('/patient/oescape/' . $episodes_list[$subspecialty[0]]->id):'' ?>">
                                  <?= $subspecialty[2] ?>
                              </a>
                            </li>
                          <?php
                      } ?>
                  </ul>
                <?php endforeach;
            endif; ?>
            <!-- icon-btns -->
    </div>

    <!-- exit oes and go back to previous page -->
    <div id="js-exit-oescape"
         data-link="<?php $core_api = new CoreAPI();
         echo $core_api->generateEpisodeLink($this->patient)?>">
        <i class="oe-i remove-circle"></i>
    </div>
</nav>