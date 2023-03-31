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
$search_by_message = $primary_identifier_prompt . ', ' . $secondary_identifier_prompt;

// Sample strings are shown as "Pattern" => "Example"
$example_patterns = [];
$dob_mandatory = \SettingMetadata::model()->checkSetting('dob_mandatory_in_search', 'on');
if ($dob_mandatory) {
    $search_by_message .= ', Firstname Surname dd/MM/yyyy or Surname, Firstname dd/MM/yyyy.';
    $example_patterns = [
        'Given Family + DOB' => 'David Smith 31/12/1975',
        'Family, Given + DOB' => 'Smith, David 31/12/1975',
        'Initial Family + DOB' => 'D Smith 1975',
        'Family + DOB' => 'Smith 1975',
    ];
} else {
    $search_by_message .= ', Firstname Surname or Firstname Surname DOB or Surname, Firstname or Surname, Firstname DOB.';
    $example_patterns = [
        'Given Family' => 'David Smith',
        'Family, Given' => 'Smith, David',
        'Family only' => 'Smith',
        'Initial Family only' => 'D Smith',
        'Family + DOB' => 'Smith 1975',
    ];
}

$example_patterns = array_merge($example_patterns, PatientIdentifierHelper::getSearchExamplePatternBasedOnIdentifierType($primary_identifier_prompt));
$example_patterns = array_merge($example_patterns, PatientIdentifierHelper::getSearchExamplePatternBasedOnIdentifierType($secondary_identifier_prompt));

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
        <div class="find-by">
            <a href="#search-help" onclick="displaySearchPatterns()">Search by ID, or Name<?= $dob_mandatory ? ' and Date of Birth' : '' ?> (click for options)</a>
        </div>
      <i class="spinner" style="display: none;" title="Loading..."></i>
    </div>
</div>
    <?php
}
$this->endWidget(); ?>

<div class="oe-popup-wrap js-search-popup" style="display: none;">
    <div class="oe-popup">
        <div class="remove-i-btn"></div>
        <div class="title">Available search patterns</div>
        <div class="oe-popup-content false">
            <p>Search is not case sensitive, there is no need to use uppercase</p>
            <table class="large-text">
                <colgroup>
                    <col class="cols-4">
                </colgroup>
                <tbody>
                </tbody><thead>
                <tr>
                    <th>Search Pattern</th>
                    <th>Example</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($example_patterns as $pattern => $example) { ?>
                    <tr>
                        <th><?= $pattern ?></th>
                        <td><?= $example ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function displaySearchPatterns() {
        $(".js-search-popup").show();
    }

    $(".remove-i-btn").on('click', function () {
        $(this).closest('.js-search-popup').hide();
    });
</script>
