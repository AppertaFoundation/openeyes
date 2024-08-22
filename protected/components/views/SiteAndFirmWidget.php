<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => ($this->mode === 'static' ? 'static-' : '') . 'site-and-firm-form',
    'htmlOptions' => array('class' => 'js-' . $this->mode . '-site-and-firm-form'),
    'action' => Yii::app()->createUrl('/site/changesiteandfirm'),
));
?>
<?=\CHtml::hiddenField('returnUrl', $this->returnUrl) ?>

<?php if ($this->subspecialty): ?>
    <?=\CHtml::hiddenField('subspecialty_id', $this->subspecialty->id) ?>
  <p>
    To add an event to this episode you must switch to a <?php echo $this->subspecialty->name ?> firm.
  </p>
<?php endif; ?>

<?php if ($this->support_services): ?>
    <?=\CHtml::hiddenField('support_services', 1) ?>
  <p>
    To add an event to this episode you must switch to a support services firm.
  </p>
<?php endif; ?>

<?php if ($this->patient): ?>
    <?=\CHtml::hiddenField('patient_id', $this->patient->id) ?>
<?php endif; ?>

<?php
if ($errors = $form->errorSummary($model)) {
    echo '<div>' . $errors . '</div>';
}
?>
<table <?php if ($this->mode === 'static'): ?>class="standard"<?php endif; ?>>
  <colgroup>
    <col class="cols-3">
  </colgroup>
  <tbody>
  <tr>
    <td>
        Institution
        <i class="oe-i info small js-has-tooltip" data-tooltip-content="Please logout to change Institution"></i>
    </td>
    <td> 
        <?= \CHtml::dropDownList('',
          Yii::app()->session['selected_institution_id'],
          \CHtml::listData(Institution::model()->findAll(), 'id', 'name'),
          [
              'class' => 'cols-full',
              'disabled' => 'disabled',
          ]);
        ?>
    </td>
  </tr>
  <tr>
    <td>
        <?= $model->getAttributeLabel('site_id') ?>
        <?php if ($disable_site) { ?>
            <i class="oe-i info small js-has-tooltip" data-tooltip-content="Please logout to change Site"></i>
            <?=$form->hiddenField($model, 'site_id', ['value' => Yii::app()->session['selected_site_id']]) ?>
        <?php } ?>
    </td>
    <td>
        <?php echo $form->dropDownList($model, 'site_id', $sites, [
            'class' => 'cols-full',
            'disabled' => $disable_site,
            'data-test' => 'change-site-site-context-popup',
        ]); ?>
    </td>
  </tr>
  <tr>
    <td>
        <?= $model->getAttributeLabel('firm_id') ?>
    </td>
    <td>
        <?php echo $form->dropDownList($model, 'firm_id', $firms, array('class' => 'cols-full', 'data-test' => 'change-firm-site-context-popup')); ?>
    </td>
  </tr>

  <?php if ($this->mode === "popup"): ?>
    <tr>
      <td colspan="2" class="align-right">
          <?=\CHtml::submitButton('Confirm change', array('class' => 'green hint', 'data-test' => 'confirm-change-site-context-popup',)); ?>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>

<?php
if (Yii::app()->components['user']->loginRequiredAjaxResponse) {
    Yii::app()->clientScript->registerScript('ajaxLoginRequired', '
            jQuery("body").ajaxComplete(
                function(event, request, options) {
                    if (request.responseText == "' . Yii::app()->components['user']->loginRequiredAjaxResponse . '") {
                        window.location.href = "' . Yii::app()->createUrl('/site/login') . '"
                    }
                }
            );
        ');
}
?>

<?php $this->endWidget(); ?>

<script>
  $(document).ready(function () {
    $('.js-static-site-and-firm-form').on('change', 'select', function () {
      $(this).closest('form').submit();
    });
  });
</script>
