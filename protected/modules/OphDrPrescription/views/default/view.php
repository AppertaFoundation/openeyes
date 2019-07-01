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

$Element = Element_OphDrPrescription_Details::model()->find('event_id=?', array($this->event->id));

?>
<?php $this->beginContent('//patient/event_container', ['Element' => $Element, 'no_face'=>true]); ?>

    <?php
        // Event actions
        if ($this->checkPrintAccess()) {
            if(!$Element->draft || $this->checkEditAccess()){
                foreach ($Element->items as $item) {
                    // If at least one prescription item has 'Print to FP10' selected as the dispense condition, display the Print FP10 button.
                    if ($item->dispense_condition->name == 'Print to FP10') {
                        $this->event_actions[] = EventAction::button('Print FP10', "et_print_fp10");
                        break;
                    }
                }

            }
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

    <script type="text/javascript">
        <?php
        if (isset(Yii::app()->session['print_prescription'])) {
            unset(Yii::app()->session['print_prescription']);
            ?>
        $(document).ready(function() {
            do_print_prescription();
        });
        <?php } ?>
    </script>
<?php $this->renderPartial('//default/delete');?>
<?php $this->endContent();?>

