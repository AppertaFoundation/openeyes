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
<div class="element-fields full-width flex-layout flex-top col-gap">
    <div class="cols-7 data-group">
        <table class= "cols-full">
            <colgroup>
                <col class="cols-5">
                <col class="cols-7">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        <div class="data-label"><?php echo $element->getAttributeLabel('id') ?>:</div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php echo $element->id; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo $element->getAttributeLabel('type_id') ?>:</div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php echo $element->type ? $element->type->name : 'None' ?>
                            <?php if ($element->type->id == 4) : ?>
                                (<?php echo $element->other_sample_type; ?>)
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo $element->getAttributeLabel('blood_date') ?>:</div>
                    </td>
                    <td>
                        <div class="data-value"><?php echo $element->NHSDate('blood_date') ?></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo $element->getAttributeLabel('volume') ?>:</div>
                    </td>
                    <td>
                        <div class="data-value"><?=\CHtml::encode($element->volume) ?></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo $element->getAttributeLabel('destination') ?>:</div>
                    </td>
                    <td>
                        <div class="data-value"><?=\CHtml::encode($element->destination) ?></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo $element->getAttributeLabel('consented_by') ?>:</div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php
                            $user = User::model()->findByPk($element->consented_by);
                            echo $user['first_name'] . ' ' . $user['last_name'];
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo $element->getAttributeLabel('studies') ?>:</div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php
                            $studies = [];
                            foreach ($element->studies as $s) {
                                $studies[] = $s->name;
                            }
                            echo implode(", ", $studies);
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo $element->getAttributeLabel('comments') ?>:</div>
                    </td>
                    <td>
                        <div class="data-value"><?=\CHtml::encode($element->comments) ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php if ($this->event->children) { ?>
  <section class="sub-element">
    <header class="sub-element-header">
      <h3 class="sub-element-title">Tests</h3>
    </header>
  </section>
    <?php foreach ($this->event->children as $event) {
        foreach (ElementType::model()->findAll(array(
            'condition' => 'event_type_id = ?',
            'params' => array($event->event_type_id),
            'order' => 'display_order asc',
        )) as $element_type) {
            $model = $element_type->class_name;
            $object = $model::model()->find('event_id=?', array($event->id));

            $this->renderFile(
                "protected/modules/{$event->eventType->class_name}/views/default/view_{$element_type->class_name}.php",
                array('element' => $object)
            );
        }
    } ?>
<?php } ?>
