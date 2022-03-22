<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$pick_behavior = new SetupPathwayStepPickerBehavior();
$path_step_type_ids = json_encode($pick_behavior->getPathwayStepTypesRequirePicker());
$picker_setup = $pick_behavior->setupPicker();
?>
<div class="clinic-pathway-btn" id="clinic-pathway-btn">
    <?= ucfirst($pathway->getStatusString()) ?>
    <div class="wait-duration">
        <?= $pathway->getTotalDurationHTML() ?>
    </div>
</div>

<script type="text/javascript">
    $('#clinic-pathway-btn').click(function (){
        if ($(this).hasClass('active')) {
            removePathwayContainer();
        } else {
            addPathwayContainer();
            $('.pathway-in-event').on('click', '.js-close-pathway', function () {
                removePathwayContainer();
            });
            $.ajax({
                'type': 'POST',
                'url': '/patient/showCurrentPathway',
                'data': "pathway_id=" + <?= $pathway->id ?> + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                success: function (resp) {
                    $('.js-pathway-spinner').remove();
                    $('.pathway-in-event').append(resp);
                    <?php
                        // Register worklist.js file only if the clinic pathway has been successfully added, and if it hasn't already been loaded
                    if (!array_key_exists(Yii::getPathOfAlias('application.assets.js.worklist') . '/worklist.js', Yii::app()->clientScript->scriptMap)) {
                        $worklist_js = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets.js.worklist') . '/worklist.js', true);
                        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.PathStep.js'), ClientScript::POS_END);
                        Yii::app()->clientScript->registerScriptFile($worklist_js, ClientScript::POS_END);
                    }
                        $psd_step_type_id = Yii::app()->db->createCommand()
                            ->select('id')
                            ->from('pathway_step_type')
                            ->where('short_name = \'drug admin\'')
                            ->queryScalar();
                    ?>
                    let picker = new OpenEyes.UI.PathwayStepPicker({
                        pathways: <?= $pathway->getPathwaysJSON() ?>,
                        ...<?=$path_step_type_ids?>,
                        ...<?=$picker_setup?>,
                    });
                    picker.init();
                },
                error: function () {
                    $('.js-pathway-spinner').remove();
                    $('.pathway-in-event').append("There was an unexpected error in retrieving clinical worklist for this patient. Please try again or contact support for assistance.");
                }
            });
        }
    });

    function removePathwayContainer()
    {
        $('#clinic-pathway-btn').removeClass('active');
        $('.main-event').removeClass('allow-for-pathway');
        $('.pathway-in-event').remove();
    }

    function addPathwayContainer()
    {
        $('#clinic-pathway-btn').addClass('active');

        let pathwayDiv = document.createElement('div');
        pathwayDiv.classList.add('pathway-in-event');

        let spinnerIcon = document.createElement('i');
        spinnerIcon.classList.add('spinner');
        spinnerIcon.classList.add('as-icon');
        spinnerIcon.classList.add('js-pathway-spinner');
        pathwayDiv.append(spinnerIcon);

        let closeDiv = document.createElement('div');
        closeDiv.classList.add('close-icon-btn');
        let closeIcon = document.createElement('i');
        closeIcon.classList.add('oe-i');
        closeIcon.classList.add('remove-circle');
        closeIcon.classList.add('small-icon');
        closeIcon.classList.add('js-close-pathway');
        closeDiv.append(closeIcon);
        pathwayDiv.append(closeDiv);

        let $mainEventSelector = $('.main-event');
        $mainEventSelector.addClass('allow-for-pathway');
        $mainEventSelector.append(pathwayDiv);
    }
</script>
