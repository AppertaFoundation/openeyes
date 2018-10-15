<?php
/**
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
?>

<?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'adminform',
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 5,
    ),
));
?>

<div class="cols-5">
    <table class="standard cols-full">
        <title>Add incision length default</title>
        <colgroup>
            <col class="cols-1">
            <col class="cols-3">
        </colgroup>
        <tbody>
        <tr>
            <td>Context</td>
            <td>
                <?=\CHtml::activeDropDownList(
                    $default,
                    'firm_id',
                    Firm::model()->getListWithSpecialties(),
                    ['class' => 'cols-full', 'empty' => 'Select ' . Firm::contextLabel()]
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Value</td>
            <td>
                <?=\CHtml::activeTextField(
                    $default,
                    'value',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<?php echo $form->formActions(array('cancel-uri' => '/OphTrOperationnote/admin/viewIncisionLengthDefaults')) ?>
<?php $this->endWidget() ?>
</div>
