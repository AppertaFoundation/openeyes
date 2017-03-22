<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<tr>
  <td>
      <?php echo CHtml::hiddenField('transactionID[]', $transaction->id) ?>
      <?php echo CHtml::textField('date[]', $transaction->date, array('id' => "date$i", 'disabled' => $disabled)) ?>
  </td>
  <td>
      <?php echo CHtml::dropDownList(
          'study_id[]',
          $transaction->study_id,
          CHtml::listData(OphInDnaextraction_DnaTests_Study::model()->findAll(), 'id', 'name'),
          array('empty' => '- Select -', 'disabled' => $disabled)
      ) ?>
  </td>
  <td>
      <?php echo CHtml::textField('volume[]', $transaction->volume, array('disabled' => $disabled)) ?>
  </td>
  <td>
      <?php echo CHtml::textField('comments[]', $transaction->comments, array('disabled' => $disabled)) ?>
  </td>  
  <td>
      <?php if (!$disabled) {
          echo CHtml::link('(remove)', '#', array('class' => 'removeTransaction'));
      } ?>
  </td>
</tr>
<script type="text/javascript">
  $('#date<?php echo $i?>').datepicker({
    maxDate: 'today',
    showAnim: 'fold',
    dateFormat: '<?php echo Helper::NHS_DATE_FORMAT_JS?>'
  });
</script>
