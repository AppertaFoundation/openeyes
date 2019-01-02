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

<section class="element view tile  view-per-operative-drugs">
  <header class="element-header">
    <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
  </header>
  <div class="element-data full-width">

    <div class="data-group">
      <div class="data-value <?php if (!$element->drugs) { ?> none<?php } ?>">
        <div class="tile-data-overflow">
            <?php if (!$element->drugs) { ?>
              None
            <?php } else { ?>
              <table class="large last-left">
                <tbody>
                <?php foreach ($element->drugs as $drug) { ?>
                  <tr>
                    <td><?php echo $drug->name ?></td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            <?php } ?>
        </div>
      </div>
    </div>

  </div>
</section>

<?php
if ($instructions = $element->event->getElementByClass(Element_OphTrOperationnote_Comments::class)): ?>
    <section class="element view tile">
        <header class="element-header">
            <h3 class="element-title"><?=\CHtml::encode($instructions->getAttributeLabel('postop_instructions')) ?></h3>
        </header>
        <div class="element-data full-width">
            <div class="data-value">
                <div class="tile-data-overflow">
                    <div class="data-value<?php if (!$instructions->postop_instructions) { ?> none<?php } ?>">
                        <?=\CHtml::encode($instructions->postop_instructions) ? Yii::app()->format->Ntext($instructions->postop_instructions) : 'None' ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php $element = Element_OphTrOperationnote_Comments::model()->findByAttributes(['event_id' => $element->event->id]); ?>
<section class="element view tile  view-per-operative-comments">
    <header class="element-header">
        <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
    </header>
    <div class="element-data full-width">
        <div class="data-group">
            <div class="data-value <?php if (!$element->comments) { ?> none<?php } ?>">
                <div class="tile-data-overflow">
                    <?php if (!$element->comments) { ?>
                        None
                    <?php } else {
                        echo $element->comments;
                    } ?>
                </div>
            </div>
        </div>
    </div>
</section>