<?php
/**
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

$Element = Element_OphDrPrescription_Details::model()->find('event_id=?', array($this->event->id));

?>
<?php $this->beginContent('//patient/event_container', ['Element' => $Element, 'no_face'=>true]); ?>

    <?php
        // Event actions
        $elementEditable = $Element->isEditableByMedication();
        if(($Element->draft ) && (!$elementEditable )){
            $this->event_actions[] = EventAction::button(
                'Save as final',
                'save',
                array('level' => 'secondary'),
                array(
                    'id' => 'et_save_final',
                    'class' => 'button small',
                    'type' => 'button',
                    'data-element' => $Element->id
                )
            );
        }

        if ($this->checkPrintAccess()) {
        	if(!$Element->draft || $this->checkEditAccess()){
        		$this->event_actions[] = EventAction::printButton();
        	}
        }
    ?>

    <?php $this->renderPartial('//base/_messages'); ?>
    <?php if ($this->event->delete_pending) {?>
        <div class="alert-box alert with-icon">
            This event is pending deletion and has been locked.
        </div>
    <?php } elseif ($Element->draft) {?>
        <div class="alert-box alert with-icon">
            This prescription is a draft and can still be edited
        </div>
    <?php }?>

    <?php $this->renderOpenElements($this->action->id); ?>
    <?php $this->renderOptionalElements($this->action->id); ?>

<?php
    if (isset(Yii::app()->session['print_prescription'])) :?>
        <?php unset(Yii::app()->session['print_prescription']); ?>
        <script>
            $(document).ready(function() {
                do_print_prescription();
            });
        </script>
<?php endif; ?>

<?php $this->renderPartial('//default/delete');?>
<?php $this->endContent();?>
