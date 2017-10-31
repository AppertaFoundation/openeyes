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
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
}

$this->beginContent('//patient/event_container');
$this->moduleNameCssClass .= ' highlight-fields';

if ($this->is_auto) {
    ?>
<div id="surgeon" class="row data-row">
	<div class="large-2 column" style="margin-left: 10px;">
		<div class="data-label">Surgeon:</div>
	</div>
	<div class="large-9 column end">
		<div class="data-value"><b><?php
                if (isset(Element_OphInBiometry_IolRefValues::model()->findByAttributes(array('event_id' => $this->event->id))->surgeon_id)) {
                    echo OphInBiometry_Surgeon::model()->findByAttributes(
                        array('id' => Element_OphInBiometry_IolRefValues::model()->findByAttributes(array('event_id' => $this->event->id))->surgeon_id)
                    )->name;
                }
    ?></b></div>
	</div>
</div>
<?php

}
$this->renderOpenElements($this->action->id); ?>

<?php $this->endContent()?>
