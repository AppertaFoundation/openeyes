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
));
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
$primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $institution_id, $site_id);
$secondary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $institution_id, $site_id);
?>
<!-- Splitting the UI below based on context because as of OE-8991, the search bar gets shown differently based on whether its on the homepage or search results page-->
<?php
if ($context == "sidebar") { ?>
    <div id="oe-search-patient">
        <h4>Search by <?php echo  $primary_identifier_prompt . ', ' . $secondary_identifier_prompt?>, Firstname Surname or Surname, Firstname.</h4>
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
        <div class="find-by">Search by <?php echo $primary_identifier_prompt . ', ' . $secondary_identifier_prompt?>, Firstname Surname or Surname, Firstname.</div>
      <i class="spinner" style="display: none;" title="Loading..."></i>
    </div>
</div>
    <?php
}
$this->endWidget(); ?>
