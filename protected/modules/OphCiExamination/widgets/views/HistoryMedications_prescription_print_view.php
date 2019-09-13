<?php $el_id = CHtml::modelName($element) . '_element'; ?>
<?php $visible_sections = ['current_entries' => 'Current', "closed_entries" => "Stopped"]; ?>
<header class=" element-header">
    <h3 class="element-title">Eye medications</h3>
</header>
<?php foreach ($visible_sections as $key=>$section_name): ?>
    <?php $entries = array_filter($element->$key, function($e){ return in_array($e->route_id, array(MedicationRoute::ROUTE_EYE, MedicationRoute::ROUTE_INTRAVITREAL )); }); ?>
        <?php if (!empty($entries)): ?>
        <div class="label"><?php echo $section_name; ?>:</div>
        <div class="element-data">
            <div class="data-value">
                    <div class="tile-data-overflow">
                        <table>
                            <colgroup>
                                <col>
                                <col width="55px">
                                <col width="85px">
                            </colgroup>
                            <tbody>
                            <?php foreach ($entries as $entry): ?>
                                <?php echo $this->render('HistoryMedicationsEntry_event_view', ['entry' => $entry]); ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

<header class=" element-header">
    <h3 class="element-title">Systemic Medications</h3>
</header>
  <?php foreach ($visible_sections as $key=>$section_name): ?>
      <?php $entries = array_filter($element->$key, function($e){ return !in_array($e->route_id, array(MedicationRoute::ROUTE_EYE, MedicationRoute::ROUTE_INTRAVITREAL )); }); ?>
      <?php if (!empty($entries)): ?>
          <div class="label"><?php echo $section_name; ?>:</div>
          <div class="element-data">
              <div class="data-value">
                  <div class="tile-data-overflow">
                      <table>
                          <colgroup>
                              <col>
                              <col width="55px">
                              <col width="85px">
                          </colgroup>
                          <tbody>
                          <?php foreach ($entries as $entry): ?>
                              <?php echo $this->render('HistoryMedicationsEntry_event_view', ['entry' => $entry]); ?>
                          <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>
          </div>
      </div>
      <?php endif; ?>
  <?php endforeach; ?>