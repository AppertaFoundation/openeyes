<?php
/**
 * @var $callback string
 */
$this->beginWidget('CActiveForm', array(
    'id' => 'search-form',
    'focus' => '#query',
    'action' => $callback,
    'htmlOptions' => array(
        'class' => 'form oe-find-patient search',
    ),
)); ?>
<!-- Splitting the UI below based on context because as of OE-8991, the search bar gets shown differently based on whether its on the homepage or search results page-->
<?php
$search_by_message = (\SettingMetadata::model()->getSetting('hos_num_label')) . ', ' . (\SettingMetadata::model()->getSetting('nhs_num_label'));

if (\SettingMetadata::model()->checkSetting('dob_mandatory_in_search', 'on')) {
    $search_by_message .= ', Firstname Surname DOB or Surname, Firstname DOB.';
} else {
    $search_by_message .= ', Firstname Surname or Firstname Surname DOB or Surname, Firstname or Surname, Firstname DOB.';
}

if ($context == "sidebar") { ?>
    <div id="oe-search-patient">
        <h4>Search by <?= $search_by_message ?></h4>
        <div class="search-patient row">
            <?=CHtml::textField('query', isset($search_term) ? $search_term : '', [
                'autocomplete' => 'off',
                'class' => 'search cols-full',
                'placeholder' => 'Search',
            ]); ?>
            <button type="submit" id="js-find-patient" class="blue hint row cols-full">Find Patient</button>
            <i class="spinner" style="display: none;" title="Loading..."></i>
        </div>
    </div>
    <?php
} else { ?>
<div class="oe-search-patient" id="oe-search-patient">
    <div class="search-patient">
        <?=CHtml::textField('query', isset($search_term) ? $search_term : '', [
                'autocomplete' => 'off',
                'class' => 'search',
                'placeholder' => 'Search',
          ]); ?>
        <button type="submit" id="js-find-patient" class="blue hint">Find Patient</button>
        <div class="find-by">Search by <?= $search_by_message ?></div>
      <i class="spinner" style="display: none;" title="Loading..."></i>
    </div>
</div>
    <?php
}
$this->endWidget(); ?>
