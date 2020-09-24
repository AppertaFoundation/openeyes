<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var Element_OphTrOperationchecklists_Note $element
 */
?>
<?php $name_stub = CHtml::modelName($element) . '[notes]';
// get all the case notes for this element
if ($element->id) {
    $notes = OphTrOperationchecklists_Notes::model()->findAll('element_id = :element_id', array(':element_id' => $element->id));
}
?>
<div class="element-fields">
    <?= CHtml::textField(
        $name_stub . '[notes]',
        $element->notes->notes ?? '',
        array(
            'class' => 'cols-12',
            'nowrapper' => true,
            'placeholder' => 'Comments'
        )
    ); ?>
</div>

<?php if (isset($notes) && is_array($notes)) {
    if (count($notes) > 0) {
        ?>
    <div class="element-data full-width">
        <div>
            <table class="cols-full case-notes">
                <colgroup>
                    <col class="cols-12">
                </colgroup>
                <tbody>
                    <th>
                        Case Notes
                    </th>
                    <?php foreach ($notes as $note) {
                        $text = $note->notes;
                        $toolTip = "Created: " . $note->created_date . " by " . $note->createdUser->getFullNameAndTitle();
                        ?>
                        <tr>
                            <td>
                                <span class="cols-5"><?= $text; ?></span>
                            </td>
                            <td>
                                <i class="oe-i info small js-has-tooltip right" data-tooltip-content="<?= $toolTip; ?>"></i>
                            </td>
                            <td>
                                <i class="oe-i remove-circle small pad" data-case-note-id="<?= $note->id ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php }
} ?>

<script>
    $(document).ready(function() {
        let $currentDialog;
        let row;
        // remove the case note
        $('.case-notes').on('click', 'tr td .remove-circle', function (e) {
            e.preventDefault();
            row = $(this).closest('tr');
            // create popup dialog to ask user if he is sure he wants to remove the case note
            $currentDialog = new OpenEyes.UI.Dialog({
                content: '<button class="button hint green" data-case-note-id="'+ $(this).data('case-note-id')+'">YES</button>&nbsp;&nbsp;' +
                    '<button class="button hint red">NO</button>',
                title: "Are you sure you want to remove this note?",
                popupClass: 'oe-popup case-notes'
            });
            $currentDialog.open();
        });

        // handle popup dialog buttons
        $(document).on('click', '.oe-popup.case-notes button', function () {
            if ($(this).text() === "YES") {
                $.ajax({
                    'url': baseUrl + '/OphTrOperationChecklists/default/deleteNote',
                    'type': 'POST',
                    'data': {"case-note-id" : $(this).data('case-note-id'), YII_CSRF_TOKEN: YII_CSRF_TOKEN},
                    'success': function (resp) {
                        if (resp !== '1') {
                            alert("Something went wrong trying to remove the case note. Please try again or contact support for assistance.");
                        } else {
                            row.remove();
                        }
                    },
                    'error': function (msg) {
                        alert("Something went wrong trying to remove the case note. Please try again or contact support for assistance. Return message: " + msg);
                    }
                });
            }
            $currentDialog.close();
        });
    });
</script>
