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

<div class="admin box">
    <h2>Display Context For <?=$display_context->worklist_definition->name?></h2>
    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors))?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'display-context-form',
        'enableAjaxValidation' => false,
        'focus' => '#site_id',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>
    <?php echo $form->dropDownList($display_context, 'institution_id', Institution::model()->getTenantedList(false), array('empty' => '- Any -')) ?>
    <?php echo $form->dropDownList($display_context, 'site_id', Site::model()->getListForCurrentInstitution(), array('empty' => '- Any -')) ?>
    <?php echo $form->dropDownList($display_context, 'subspecialty_id', Subspecialty::model()->getList(), array('empty' => '- Any -')) ?>
    <?php echo $form->dropDownList($display_context, 'firm_id', Firm::model()->getListWithSpecialties(), array('empty' => '- Any -')) ?>

    <?php echo $form->formActions(array('cancel-uri' => '/Admin/worklist/definitionDisplayContexts/' . $display_context->worklist_definition->id))?>
    <?php $this->endWidget()?>
</div>
