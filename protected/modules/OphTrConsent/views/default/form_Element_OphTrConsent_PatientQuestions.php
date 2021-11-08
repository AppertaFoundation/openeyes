<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="element-fields full-width flex-layout">
    <div class="cols-10">
        <table class="cols-full">
        <colgroup>
            <col class="cols-3">
        </colgroup>
        <tbody>
        <tr>
            <td>
                <?= CHtml::encode($element->getAttributeLabel('questions')) ?>
            </td>
            <td>
                <?php echo $form->textArea(
                    $element,
                    'questions',
                    array('rows' => 4, 'cols' => 80, 'nowrapper' => true)
                ) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= CHtml::encode($element->getAttributeLabel('refused_procedures')) ?>
            </td>
            <td>
                <?php echo $form->textArea(
                    $element,
                    'refused_procedures',
                    array('rows' => 4, 'cols' => 80, 'nowrapper' => true)
                ) ?>
            </td>
        </tr>
        </tbody>
    </table>
    </div>
</div>

<script>
    tinymce.init({
        mode: "textareas",
        menubar: false,
        statusbar: true,
        plugins: ' advlist lists',
        toolbar: 'undo redo | bold italic |  alignleft aligncenter alignright | numlist bullist ',
        selector: "textarea#Element_OphTrConsent_PatientQuestions_questions"
    });
    tinymce.init({
        mode: "textareas",
        menubar: false,
        statusbar: true,
        plugins: ' advlist lists',
        toolbar: 'undo redo | bold italic |  alignleft aligncenter alignright | numlist bullist ',
        selector: "textarea#Element_OphTrConsent_PatientQuestions_refused_procedures",
    });
</script>
