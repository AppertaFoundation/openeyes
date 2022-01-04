<?php
/**
* (C) OpenEyes Foundation, 2020
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @link http://www.openeyes.org.uk
*
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (C) 2020, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/

?>
<ul class="js-session-features" style="display:<?=(!$session->available ? 'none' : 'block');?>">
    <li class="<?=(!$session->consultant) ? '' : 'js-checked'; ?>"
        style="display: <?=(!$session->consultant) ? 'none' : 'block'; ?>"
        id="consultant_icon_<?php echo $session->id ?>" class="consultant"
        title="Consultant Present">
        Consultant list
    </li>
    <li class="<?=(!$session->anaesthetist) ? '' : 'js-checked'; ?>"
        style="display: <?=(!$session->anaesthetist) ? 'none' : 'block'; ?>"
        id="anaesthetist_icon_<?php echo $session->id ?>" class="anaesthetist"
        title="Anaesthetist Present">
        Anaesthetist present
    </li>
    <li class="<?=(!$session->general_anaesthetic) ? '' : 'js-checked'; ?>"
        style="display: <?=(!$session->general_anaesthetic) ? 'none' : 'block'; ?>"
        id="general_anaesthetic_icon_<?php echo $session->id ?>"
        title="Anaesthetist Present">
        GA available
    </li>
    <li class="<?=(!$session->paediatric) ? '' : 'js-checked'; ?>"
        style="display: <?=(!$session->paediatric) ? 'none' : 'block'; ?>"
        id="paediatric_icon_<?php echo $session->id ?>" class="paediatric"
        title="Paediatric Session">Paediatric list
    </li>

    <!-- checkboxes -->
    <?php
    $data = [
        'consultant' => 'Consultant list',
        'anaesthetist' => 'Anaesthetist present',
        'general_anaesthetic' => 'GA available',
        'paediatric' => 'Paediatric list',
    ];
    foreach ($data as $attribute => $label) :?>
        <label class="highlight js-diaryEditMode" style="display:none">
        <?=\CHtml::activeCheckBox($session, $attribute, ['name' => "{$attribute}_{$session->id}"]) . $label;?>
        </label>
    <?php endforeach;?>
</ul>