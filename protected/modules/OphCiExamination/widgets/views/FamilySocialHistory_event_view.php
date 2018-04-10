<?php
if ($element->getElementTypeName() == 'Family History'
    ||
    ($element->getElementTypeName() == 'Social History'
        &&
        !$element->event->getElementByClass('OEModule\OphCiExamination\models\FamilyHistory')
    )) {
  ?>
  <section class="element tile view-family-social-history">
    <header class="element-header">
      <h3 class="element-title">Family and Social</h3>
    </header>
    <div class="element-data full-width">
      <div class="data-row">
        <div class="data-value">
          <div class="tile-data-overflow">
            <table class="last-left">
              <tbody>
              <tr>
                <td><?= $element->event->getElementByClass('OEModule\OphCiExamination\models\FamilyHistory'); ?></td>
              </tr>
              <tr>
                <td><?= $element->event->getElementByClass('OEModule\OphCiExamination\models\SocialHistory'); ?></td>
              </tr>
              </tbody>
            </table>
          </div> <!-- .tile-data-overflow -->
        </div>
      </div>
    </div>
  </section>
<?php } ?>