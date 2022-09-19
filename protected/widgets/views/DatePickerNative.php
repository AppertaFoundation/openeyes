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

<?php if (!$this->getHtmlOption('nowrapper')) { ?>
  <div class="flex-layout flex-left" <?php if ($this->getHtmlOption('hidden')) {
        ?> style="display: none;" <?php
                                     } ?>>
    <?php /* TODO: IS THIS TO WORRY ABOUT unset($htmlOptions['hidden'])*/ ?>
    <div class="cols-<?= $layoutColumns['label']; ?> column">
      <label for="<?= $this->getInputId(); ?>">
        <?= \CHtml::encode($this->getLabel()) ?>:
      </label>
    </div>
    <div class="cols-<?= $layoutColumns['field']; ?> column end">
<?php } ?>

      <input type="date"
        id="<?= $this->getInputId() ?>"
        value="<?= $this->getValue() ?>"
        name="<?= $this->getInputName() ?>"
        <?php if ($this->getMaxDate()) {
            ?> max="<?= $this->getMaxDate() ?>" <?php
        } ?>
        <?php if ($this->getMinDate()) {
            ?> min="<?= $this->getMinDate() ?>" <?php
        } ?>
        <?php if ($this->getHtmlOption('class')) {
            ?> class="<?= $this->getHtmlOption('class') ?>" <?php
        } ?>
        <?php if ($this->getHtmlOption('style')) {
            ?> style="<?= $this->getHtmlOption('style') ?>" <?php
        } ?>
        <?php if ($this->getHtmlOption('form')) {
            ?> form="<?= $this->getHtmlOption('form') ?>" <?php
        } ?>
        autocomplete="off" />

      <?php if (!$this->getHtmlOption('nowrapper')) { ?>
    </div>
  </div>
      <?php } ?>