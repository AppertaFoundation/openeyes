<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$va_data = null;
$near_va_data = null;
$examination_api = Yii::app()->moduleAPI->get('OphCiExamination');
if ($examination_api) {
    $va_data = $examination_api->getMostRecentVAData($this->patient);
    $near_va_data = $examination_api->getMostRecentNearVAData($this->patient);
}

$section_cls_append = [
    'update' => 'edit  edit-biometry',
    'view' => 'priority'
][$action] ?? '';

?>
<section class="element full <?= $section_cls_append ?> eye-divider ">
    <header class="element-header">
        <h3 class="element-title">Visual Acuity <?= '<br />' . ($va_data !== null ? date("d M Y", strtotime($va_data['event_date'])) : ''); ?></h3>
    </header>
    <div class="element-fields element-eyes data-group">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) {
            $formatted_readings = array_map(function ($reading) {
                return $reading['value'] . " " . $reading['method_name'] . " (" . $reading['unit'] . ")";
            }, $va_data["{$eye_side}_readings"] ?? []);
            ?>
        <div class="js-element-eye <?= $eye_side ?>-eye column">
            <?php if (count($formatted_readings)) { ?>
                <div class="data-group">
                    <div class="data-value"><?= implode(", ", $formatted_readings) ?></div>
                </div>
            <?php } else { ?>
                <div class="data-value not-recorded">Not recorded</div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</section>

<section class="element full  <?= $section_cls_append ?> eye-divider ">
    <header class="element-header">
        <h2 class="element-title">Near Visual Acuity <?= '<br />' . ($near_va_data !== null ? date("d M Y", strtotime($near_va_data['event_date'])) : ''); ?></h2>
    </header>
    <div class="element-fields element-eyes data-group">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) {
            $formatted_readings = array_map(function ($reading) {
                return $reading['value'] . " " . $reading['method_name'] . " (" . $reading['unit'] . ")";
            }, $near_va_data["{$eye_side}_readings"] ?? []);
            ?>
            <div class="js-element-eye <?= $eye_side ?>-eye column">
                <?php if (count($formatted_readings)) { ?>
                    <div class="data-group">
                        <div class="data-value"><?= implode(", ", $formatted_readings) ?></div>
                    </div>
                <?php } else { ?>
                    <div class="data-value not-recorded">Not recorded</div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>
<?php
// Refraction here
$refraction_data = null;
if ($examination_api) {
    $refraction_data = $examination_api->getMostRecentRefractionData($this->patient);
}

if ($refraction_data) {
    ?>
    <section class="element full eye-divider <?= $section_cls_append ?>">
        <header class="element-header">
            <h3 class="element-title">Refraction <?= '<br />' . \Helper::convertDate2NHS($refraction_data['event_date']); ?></h3>
        </header>
        <div class="element-fields element-eyes data-group">
            <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
                <div class="js-element-eye <?= $eye_side ?>-eye column">
                    <?php if ($refraction_data["{$eye_side}_comments"] || $refraction_data["{$eye_side}_priority_reading"]) {
                        ?>
                        <div class="refraction">
                            <div class="data-value">
                                <?= $refraction_data["{$eye_side}_priority_reading"] ? $refraction_data["{$eye_side}_priority_reading"]["refraction"] : "" ?><br />
                                <?= $refraction_data["{$eye_side}_priority_reading"] ? $refraction_data["{$eye_side}_priority_reading"]["spherical_equivalent"] : "" ?>
                                <?= $refraction_data["{$eye_side}_comments"]
                                    ? Yii::app()->format->Ntext($refraction_data["{$eye_side}_comments"])
                                    : ""?>
                            </div>
                        </div>
                        <?php
                    } else { ?>
                        <div class="data-value not-recorded">
                            Not recorded
                        </div>
                    <?php } ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
        <?php
} else { ?>
<section class="element full  <?= $section_cls_append ?> eye-divider ">
    <header class="element-header">
        <h3 class="element-title">Refraction</h3>
    </header>
    <div class="element-fields element-eyes data-group">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
            <div class="js-element-eye <?= $eye_side; ?>-eye column">
                <div class="data-value not-recorded">
                    Not recorded
                </div>
            </div>
        <?php endforeach; ?>
<?php } ?>
</section>