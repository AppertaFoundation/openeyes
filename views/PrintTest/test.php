<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div style="margin:0 auto; width:60%; background: #FFF; padding:10px;">
<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
	'id'=>'create-form',
	'enableAjaxValidation'=>false,
        'action' => array( './PrintTest/test' ),
	'layoutColumns' => array(
		'label' => 2,
		'field' => 10
	),
        'htmlOptions'=>array(
            'name'=>'test-print',
        ),

));
?>

<div class="form-group">
    <label for="name">Name:</label>
    <input type="text" class="form-control" name="text_name" id="name">
</div>
<div class="form-group">
    <label for="day">Date:</label>
    <input type="text" class="form-control" style="width:50px; display:inline-block;" id="day" name="text_day" placeholder="day">
    <input type="text" class="form-control" style="width:50px; display:inline-block;" id="month" name="text_month" placeholder="month">
    <input type="text" class="form-control" style="width:50px; display:inline-block;" id="year" name="text_year" placeholder="year">
</div>
<div class="form-group">
    <p>
        <div class="radio">
            <label><input type="radio" name="radio_optradio" value="1">I am the patient</label>
        </div>
        <div class="radio">
            <label><input type="radio" name="radio_optradio" value="2">the patient’s representative and my name is (PLEASE PRINT):</label>
        </div>
    </p>
</div>
<div class="form-group">
    <span>I consider:</span>
    <div class="radio">
        <label><input type="radio" value="1" name="radio_optcons">That this person is sight impaired (partially sighted)</label>
    </div>
    <div class="radio">
        <label><input type="radio" value="2" name="radio_optcons">That this person is severely sight impaired (blind)</label>
    </div>
</div>
<div class="form-group">
    <p>
        <label for="day_of_e">Date of examination:</label>
        <input type="text" class="form-control" style="width:50px; display:inline-block;" id="day_of_e" name="text_day_of_e" placeholder="day">
        <input type="text" class="form-control" style="width:50px; display:inline-block;" id="month_of_e" name="text_month_of_e" placeholder="month">
        <input type="text" class="form-control" style="width:50px; display:inline-block;" id="year_of_e" name="text_year_of_e" placeholder="year">
    </p>
</div>
<div class="form-group">
    <label for="consultants_name">Consultant's Name:</label>
    <input type="text" class="form-control" name="text_consultants_name" id="consultants_name">
</div>
<div class="form-group">
    <label for="hospital_address">Hospital address:</label>
    <textarea class="form-control" rows="4" name="textarea_hospital_address" id="hospital_address"></textarea>
</div>
<div class="form-group">
    <input type="hidden" name="test_print" />
    <button type="submit" class="btn btn-default">Submit</button>
     <?php echo $pdfObj ?>
</div>
<?php $this->endWidget() ?>
    
</div>
