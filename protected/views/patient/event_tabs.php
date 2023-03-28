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

<?php $this->beginWidget('CondenseHtmlWidget') ?>

<?php
if ($this->event_subtype) {
    echo $this->event_subtype->getEventIcon('medium');
} elseif ($this->event) {
    echo $this->event->getEventIcon('medium');
}
?>
<?php foreach ($this->event_tabs as $tab) {
    $class = $tab['class'] ?? null;
    $label = $tab['label'] ?? null;
    $active = isset($tab['active']) && $tab['active'] ? 'selected' : null;
    $href = $tab['href'] ?? '#';
    if (isset($tab['type']) && $tab['type']) { ?>
    <div class="button header-tab <?=$active?>">
        <<?=$tab['type']?> class="<?=$class?>"><?=$label?></<?=$tab['type']?>>
    </div>
    <?php } else {?>
    <a href="<?=$href?>" class="button header-tab <?=$class?> <?=$active?>">
        <?=$label?>
    </a>
    <?php } ?>
<?php }
if (in_array($this->action->id, ['create', 'update', 'step'])) {
    if ($this->template) { ?>
        <button id="js-template-prefill-popup-open" type="button" href="#">
        <i class="oe-i starline small pad-r"></i>
        <?= $this->template->name ?>
    </button>
    <?php } elseif ($this->event->template_id && isset($this->event->template)) { ?>
    <button id="js-template-prefill-popup-open" type="button" href="#">
        <i class="oe-i starline small pad-r"></i>
        <?= $this->event->template->name ?>
    </button>
    <?php }
    if ($this->show_index_search) { ?>
        <button class="button header-tab icon" name="exam-search" id="js-search-in-event">
            <i class="oe-i search"></i>
        </button>
    <?php }
}
$this->endWidget() ?>
