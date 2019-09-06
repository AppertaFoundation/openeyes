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
if (@$htmlOptions['id']) {
    $input_id = @$htmlOptions['id'];
} else {
    $input_id = CHtml::modelName($element) . '_' . $field . '_0';
}
?>
<?php if (!@$htmlOptions['nowrapper']) { ?>
<div class="flex-layout flex-left"<?php if (@$htmlOptions['hidden']) {
    ?> style="display: none;"<?php
                                  } ?>>
    <?php unset($htmlOptions['hidden']) ?>
  <div class="cols-<?php echo $layoutColumns['label']; ?> column">
    <label for="<?=\CHtml::modelName($element) . '_' . $field . '_0'; ?>">
        <?=\CHtml::encode($element->getAttributeLabel($field)) ?>:
    </label>
  </div>
  <div class="cols-<?php echo $layoutColumns['field']; ?> column end">
<?php } ?>
    <input class="<?= @$htmlOptions['class'] ?>"
           style="<?= @$htmlOptions['style']?>"
           id="<?= $input_id ?>"
            <?=isset($htmlOptions['form']) ? 'form='.$htmlOptions['form'] :''?>
           placeholder="yyyy-mm-dd"
           name="<?= $name ?>"
           value="<?= $value ?>"
           autocomplete="off"/>
    <script>
      var datefield = $(document.currentScript).prev('input');
      var pmu = pickmeup(datefield.get(0), {
        format: '<?= @$htmlOptions['dateFormat'] ?: 'd b Y' ?>',
        hide_on_select: true,
        default_date: false,
        max: '<?= @$options['maxDate'] ?>',
            <?php if (array_key_exists('minDate', $options)) { ?>
        min: <?= $options['minDate'] === 'today' ? 'new Date()' : 'new Date("' . $options['minDate'] . '")' ?>,
            <?php } ?>
      });
      if (pmu && datefield.val()) {
        pmu.set_date(new Date(datefield.val()));
      }
    </script>
        <?php if (!@$htmlOptions['nowrapper']) { ?>
  </div>
</div>
        <?php } ?>
