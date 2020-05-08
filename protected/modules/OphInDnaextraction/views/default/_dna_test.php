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

<?php $is_remove_allowed = isset($is_remove_allowed) ? $is_remove_allowed : true; ?>

<tr class="transaction-row" data-index="<?php echo $i; ?>">
  <td>
        <?=\CHtml::activeHiddenField($transaction, "[$i]id") ?>

        <?php
        $value = Yii::app()->request->getQuery('OphInDnaextraction_DnaTests_Transaction');
        $dateTime = new DateTime($transaction->date ? $transaction->date : $value);
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
          'name'  => "OphInDnaextraction_DnaTests_Transaction[$i][date]",
          'value' => isset($value[$i]['date']) ? $value[$i]['date'] : $dateTime->format('j M Y'),
          'id'    => "OphInDnaextraction_DnaTests_Transaction_{$i}_date",
          'options' => array(
              'showAnim'      => 'fold',
              'changeMonth'   => true,
              'changeYear'    => true,
              'altFormat'     => 'yy-mm-dd',
              'altField'      => "OphInDnaextraction_DnaTests_Transaction_{$i}_date_alt",
              'dateFormat'    => Helper::NHS_DATE_FORMAT_JS,
              'maxDate'       => 'today'
          ),
          'htmlOptions' =>array(
                'class' => "dna-hasDatepicker",
                'disabled' => $disabled ? 'disabled' : ''
          )
        ));
        ?>
  </td>
  <td>
        <?=\CHtml::activeDropDownList(
            $transaction,
            "[$i]study_id",
            CHtml::listData(OphInDnaextraction_DnaTests_Study::model()->findAll(), 'id', 'name'),
            array('empty' => '- Select -', 'disabled' => $disabled, 'class' => "study",)
        ) ?>
  </td>
  <td>
        <?=\CHtml::activeTextField($transaction, "[$i]volume", array('class' => "volume", 'disabled' => $disabled)) ?>
  </td>
  <td>
        <?=\CHtml::activeTextField($transaction, "[$i]comments", array('class' => "comments", 'disabled' => $disabled)) ?>
  </td>  
  <td class="<?php echo $is_remove_allowed ? '': 'hidden'?> ">
        <?php if (!$disabled) {
            echo CHtml::link('(remove)', 'javascript:void(0)', array('class' => 'removeTransaction'));
        } ?>
  </td>
</tr>
