<?php /**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$lprocs = OphTrLaser_LaserProcedure::model()->with(array('procedure'))->findAll(array('order' => 'procedure.term asc'));
$procs = array();
foreach ($lprocs as $lproc) {
    $procs[] = $lproc->procedure;
}

$layoutColumns = array(
    'label' => 4,
    'field' => 6,
);
?>

<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')) ?>

<div class="element-fields element-eyes">
    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
    <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> side"
         data-side="<?= $eye_side ?>"
    >
        <div class="active-form data-group flex-layout"
             style="<?= $element->hasEye($eye_side)? '': 'display: none;'?>"
        >
            <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
            <div class="cols-9">
                <table class="cols-full blank procedures">
                    <colgroup>
                        <col>
                        <col class="cols-2">
                    </colgroup>
                    <tbody>
                    <?php foreach ($element->{$eye_side . '_procedures'} as $procedure) {
                        // Adjust currently element readings to match unit steps
                        $this->renderPartial('form_Element_OphTrLaser_Laser_Procedure', array(
                            'id' => $procedure->id,
                            'term' => $procedure->term,
                            'eye_side' => $eye_side,
                        ));
                    } ?>
                    </tbody>
                </table>

            </div>
            <div class="add-data-actions flex-item-bottom" id="<?= $eye_side ?>-add-procedure">
                <button class="button hint green" id="add-procedure-btn-<?= $eye_side?>" type="button">
                    <i class="oe-i plus pro-theme"></i>
                </button>
                <!-- oe-add-select-search -->
            </div>
            <!--flex bottom-->
        </div>
        <!-- active form-->
        <div class="inactive-form"  style="<?= $element->hasEye($eye_side)? 'display: none;': ''?> ">
            <div class="add-side">
                <a href="#">
                    Add <?= $eye_side ?> side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>
        <script type="text/javascript">
            $(function () {
                let $table = $('.<?= $eye_side ?>-eye .procedures');
                new OpenEyes.UI.AdderDialog({
                    openButton: $('#add-procedure-btn-<?= $eye_side?>'),
                    itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                        array_map(function ($procedure) {
                            return ['label' => $procedure->term, 'id' => $procedure->id];
                        },
                        $procs)
                                                                   ) ?> ,{'multiSelect': true}),
                    ],
                    onOpen: function (adderDialog) {
                        adderDialog.popup.find('li').each(function() {
                            let procedure_id = $(this).data('id');
                            let alreadyUsed = $table.find('input[type="hidden"][name="treatment_<?=$eye_side?>_procedures[]"][value="' + procedure_id + '"]').length > 0;
                            $(this).toggle(!alreadyUsed);
                        });
                    },
                    onReturn: function (adderDialog, selectedItems) {
                        if (selectedItems.length) {
                            selectedItems.forEach(function (selectedItem) {
                                let selected_data = [];
                                selected_data.id = selectedItem.id;
                                selected_data.term = selectedItem.label;
                                selected_data.eye_side = '<?=$eye_side?>';
                                let form = Mustache.render($('#laser_procedure_template').html(), selected_data);
                                $table.find('tbody').append(form);
                            });
                            return true;
                        } else {
                            return false;
                        }
                    },
                });
            });
        </script>
    <?php endforeach; ?>

    <script type="text/template" id="laser_procedure_template" style="display:none">
    <?php
    $this->renderPartial('form_Element_OphTrLaser_Laser_Procedure', array(
            'id' => '{{id}}',
            'term' => '{{term}}',
            'eye_side' => '{{eye_side}}',
        ));
    ?>
    </script>