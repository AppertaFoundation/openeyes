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
    $model_name = CHtml::modelName($element);
    $element_errors = $element->getErrors();
?>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
    <div class="flex-t">
        <div class="cols-6">
            <div class="row">
            Where the patient has authorised an attorney to make decisions about the procedure in question under a Lasting Power of Attorney or a Court Appointed Deputy has been authorised to make decisions about the procedure in question, the attorney or deputy will have the final responsibility for determining whether a procedure is in the patient's best interests
            </div>
        </div>
        
        <div class="cols-5">
            <div class="row">
                <?= CHtml::encode($element->getAttributeLabel("comments"))?>
            </div>
            <div class="row">
                <?= $form->textArea(
                    $element,
                    "comments",
                    array('nowrapper' => true),
                    false,
                    array(
                        'class' => 'cols-full',
                        'rows' => '1',
                        'placeholder' => "(Optional) comments about assessing patient’s best interests"
                    )
                ); ?>
            </div>
        </div>  
    </div>
    
    <hr class="divider" />
    
    <!-- Power of Attorney contacts -->
    <table class="cols-full last-left" id="<?= $model_name ?>_entry_table">
        <colgroup>
            <col class="cols-3">
        </colgroup">
        <tbody>
            <?php
                $row_count = 0;
            foreach ($this->contact_assignments as $contact_assignment) { ?>
                    <?=$this->render(
                    'ConsentContactsEntry_event_edit',
                    array(
                        'element' => $element,
                        'form' => $form,
                        'entry' => $contact_assignment,
                        'show_comments' => true,
                        'row_count' => $row_count,
                        'field_prefix' => $model_name . '[entries][' . ($row_count) . ']',
                        'model_name' => $model_name,
                        'removable' => true,
                        'is_template' => true
                    )
                    );
                    $row_count++; ?>
            <?php } ?>
            
        
        </tbody>
    </table>        
    
    <div class="flex-r row">
        <div class="add-data-actions flex-item-bottom" id="contacts-popup">
            <button class="green hint js-add-select-search" id="add-contacts-btn" type="button">Add Power of Attorney Contact</button>      
        </div>
    </div>  
</div>

<script type="text/template" class="entry-template hidden" id="<?= CHtml::modelName($element) . '_entry_template' ?>">
    <?php

    $empty_entry = new PatientAttorneyDeputyContact();
    echo $this->render(
        'ConsentContactsEntry_event_edit',
        array(
            'entry' => $empty_entry,
            'model_name' => $model_name,
            'form' => $form,
            'show_comments' => true,
            'removable' => true,
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'row_count' => '{{row_count}}',
            'is_template' => true,
            'values' => array(
                'id' => '{{id}}',
                'label' => '{{label}}',
                'full_name' => '{{full_name}}'
            ),
        )
    );
    ?>
</script>

<script type="text/javascript">
    $(document).ready(function () {
        let contactController;
        contactController = new OpenEyes.OphTrConsent.AttorneyDeputyContactsController({
            modelName: '<?=$model_name?>',
            contactFilterId: 'contact-type-filter'
        });


        <?php $contact_labels = ContactLabel::model()->findAll(
            [
            'select' => 't.name,t.id, t.max_number_per_patient',
            'group' => 't.name',
            'distinct' => true,
            ]
        );?>

        new OpenEyes.UI.AdderDialog({
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($contact_label) {
                    return [
                        'label' => $contact_label->name,
                        'id' => $contact_label->id,
                        'patient_limit' => $contact_label->max_number_per_patient
                    ];
                },
                $contact_labels)
            ) ?>, {'header': 'Contact Type', 'id': 'contact-type-filter'})],
            openButton: $('#add-contacts-btn'),
            onReturn: function (adderDialog, selectedItems) {
                if (!contactController.isContactInTable(selectedItems)) {
                    contactController.addEntry(selectedItems);
                }
            },
            searchOptions: {
                searchSource: "/OphTrConsent/contact/autocomplete"
            },
            enableCustomSearchEntries: true,
            searchAsTypedPrefix: 'Add a new contact:',
            filter: true,
            filterDataId: "contact-type-filter",
        });
    });
</script>
<?php
/*
 * We need to decide which JS file need to be loaded regarding to the controller
 * Unfortunately jsVars[] won't work from here because processJsVars function already called
 */
$modulePath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OphTrConsent.assets'), true);

Yii::app()->clientScript->registerScriptFile($modulePath . '/js/Contacts.js', CClientScript::POS_END);

?>