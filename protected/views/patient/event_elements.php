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

<div class="js-active-elements" id="element-content">
    <?php $this->renderOpenElements($this->action->id, $form) ?>
</div>

<?php if ($this->show_element_sidebar === false && count($this->getOptionalElements())
    && isset($this->render_optional_elements)
    && $this->render_optional_elements
) { ?>
  <section class="optional-elements">
    <header class="optional-elements-header">
      <h3 class="optional-elements-title">Optional Elements</h3>
        <?php if (isset($disableOptionalElementActions) && !$disableOptionalElementActions) { ?>
          <div class="optional-elements-actions">
            <a href="#" class="add-all">
              <span>Add all</span>
              <img
                  src="<?php echo Yii::app()->assetManager->createUrl('img/_elements/icons/event-optional/element-added.png'); ?>"
                  alt="Add all"/>
            </a>
            <a href="#" class="remove-all">
              <span>Remove all</span>
              <img
                  src="<?php echo Yii::app()->assetManager->createUrl('img/_elements/icons/event-optional/element-remove.png'); ?>"
                  alt="Remove all"/>
            </a>
          </div>
        <?php } ?>
    </header>
    <ul class="optional-elements-list">
        <?php $this->renderOptionalElements($this->action->id, $form) ?>
    </ul>
  </section>
<?php } ?>

