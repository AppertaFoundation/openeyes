<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/


Yii::app()->clientScript->registerCoreScript('jquery');
$cs = Yii::app()->getClientScript();
$cs->registerCSSFile('/css/waitingList.css', 'all');

?>
<h3 class="title">Waiting List</h3>
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'waitingList-filter',
        'action'=>Yii::app()->createUrl('waitingList/search'),
        'enableAjaxValidation'=>false,
)); ?>
<div id="search-options">
        <div id="main-search">
        <table>
        <tr>
                <th>Specialty:</th>
                <th>Firm:</th>
                <th>Type:</th>
		<th>&nbsp;</th>
        </tr>
        <tr>
                <td><?php
        echo CHtml::dropDownList('specialty-id', '', Specialty::model()->getList(),
                array('empty'=>'All specialties', 'ajax'=>array(
                        'type'=>'POST',
                        'data'=>array('specialty_id'=>'js:this.value'),
                        'url'=>Yii::app()->createUrl('waitingList/filterFirms'),
                        'success'=>"js:function(data) {
                                if ($('#specialty-id').val() != '') {
                                        $('#firm-id').attr('disabled', false);
                                        $('#firm-id').html(data);
                                } else {
                                        $('#firm-id').attr('disabled', true);
                                        $('#firm-id').html(data);
                                }
                        }",
                ))); ?></td>
                <td><?php
        echo CHtml::dropDownList('firm-id', '', array(),
                array('empty'=>'All firms', 'disabled'=>(empty($firmId)))); ?></td>
                <td><?php
        echo CHtml::dropDownList('status', '', ElementOperation::getLetterOptions()) ?></td>
	<td><button type="submit" value="submit" class="shinybutton highlighted"><span>Search</span></button></td>
</tr>
</table>
<?php $this->endWidget(); ?>
        </div>
</div>
<div class="search-options">
</div>
<div class="main-search">
</div>
<div class="cleartall"></div>
<div id="searchResults"></div>
<div class="cleartall"></div>
<script type="text/javascript">
        $('#waitingList-filter button[type="submit"]').click(function() {
                $.ajax({
                        'url': '<?php echo Yii::app()->createUrl('waitingList/search'); ?>',
                        'type': 'POST',
                        'data': $('#waitingList-filter').serialize(),
                        'success': function(data) {
                                $('#searchResults').html(data);
                                return false;
                        }
                });
                return false;
        });
</script>
