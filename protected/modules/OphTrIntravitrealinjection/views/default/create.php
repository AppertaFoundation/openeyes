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

<?php
$form_id = 'clinical-create';
$this->beginContent('//patient/event_container', array('no_face' => false, 'form_id' => $form_id));
$this->breadcrumbs = array($this->module->id);
$this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'save'), array('form' => $form_id));

$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 4,
        'field' => 8,
    ),
));
    ?>
        <?php $this->displayErrors($errors)?>

        <?php if ($this->side_to_inject !== null) {
    $cls_lkup = array(
                0 => 'none',
                Eye::LEFT => 'left',
                Eye::RIGHT => 'right',
                Eye::BOTH => 'both',
            );
    if ($this->side_to_inject == 0) {
        $msg = 'No injection should be performed today';
    } elseif ($this->side_to_inject == Eye::BOTH) {
        $msg = 'Both eyes to be injected';
    } else {
        $msg = 'Only '.strtolower(Eye::model()->findByPk($this->side_to_inject)->name).' eye to be injected';
    }

    $columns = 6;
    $offset = 0;
    if ($this->side_to_inject === Eye::LEFT) {
        $offset = 6;
    } elseif ($this->side_to_inject !== Eye::RIGHT) {
        $columns = 12;
    }
    ?>
      <div class="cols-<?php echo $columns; ?> column large-offset-<?php echo $offset; ?>">
                    <div class="alert-box alert injection-warning">
                        <?php echo $msg ?>
                    </div>
                </div>
        <?php
} ?>

        <?php $this->renderOpenElements($this->action->id, $form)?>
        <?php $this->renderOptionalElements($this->action->id, $form)?>
        <?php $this->displayErrors($errors, true)?>

    <?php $this->endWidget()?>
<?php $this->endContent();?>
