<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
$expert = $element->getSetting('expert');
if ($expert) {
	$doodleToolBarArray = array('AngleNV', 'AntSynech', 'AngleRecession');
} else {
	$doodleToolBarArray = array('AngleNV', 'AntSynech');
}
$bindingArray = array();
$onReadyCommandArray = array(
	array('addDoodle', array('Gonioscopy'))
);
foreach (array('AngleGradeNorth' => 'sup','AngleGradeEast' => 'nas','AngleGradeSouth' => 'inf', 'AngleGradeWest' => 'tem') as $doodleClass => $position) {
	$bindingArray[$doodleClass]['grade'] = array('id' => 'Element_OphCiExamination_Gonioscopy_'.$side.'_gonio_'.$position.'_id', 'attribute' => 'data-value');
	if (!$expert) {
		$bindingArray[$doodleClass]['seen'] = array('id' => $side.'_gonio_'.$position.'_basic', 'attribute' => 'data-value');
	}
	$onReadyCommandArray[] = array('addDoodle', array($doodleClass));
}
$onReadyCommandArray[] = array('deselectDoodles', array());
$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
		'doodleToolBarArray' => $doodleToolBarArray,
		'onReadyCommandArray' => $onReadyCommandArray,
		'bindingArray' => $bindingArray,
		'idSuffix' => $side.'_'.$element->elementType->id,
		'side' => ($side == 'right') ? 'R' : 'L',
		'mode' => 'edit',
		'model' => $element,
		'attribute' => $side.'_eyedraw',
));
?>
<div class="eyedrawFields">
	<div<?php if (!$expert) { ?>
		style="display: none;" <?php } ?> class="shaffer_grade">
		<div class="label">Shaffer grade:</div>
		<div class="data gonioCross">
			<?php
			$html_options = array();
			foreach (OphCiExamination_Gonioscopy_Description::model()->findAll() as $option) {
				$html_options[(string) $option->id] = array('data-value'=> $option->name);
			}
			?>
			<div class="gonioSup">
				<?php echo CHtml::activeDropDownList($element, $side.'_gonio_sup_id', $element->getGonioscopyOptions(), array('class' => 'gonioGrade gonioExpert', 'data-position' => 'sup', 'options' => $html_options))?>
			</div>
			<div class="gonioTem">
				<?php echo CHtml::activeDropDownList($element, $side.'_gonio_tem_id', $element->getGonioscopyOptions(), array('class' => 'gonioGrade gonioExpert', 'data-position' => 'tem', 'options' => $html_options))?>
			</div>
			<div class="gonioNas">
				<?php echo CHtml::activeDropDownList($element, $side.'_gonio_nas_id', $element->getGonioscopyOptions(), array('class' => 'gonioGrade gonioExpert', 'data-position' => 'nas', 'options' => $html_options))?>
			</div>
			<div class="gonioInf">
				<?php echo CHtml::activeDropDownList($element, $side.'_gonio_inf_id', $element->getGonioscopyOptions(), array('class' => 'gonioGrade gonioExpert', 'data-position' => 'inf', 'options' => $html_options))?>
			</div>
		</div>
	</div>
	<?php if (!$expert) { ?>
	<div class="basic_grade">
		<div class="label">Angle Open?:</div>
		<?php
			$basic_options = array('0' => 'No', '1' => 'Yes');
			$html_options = array('1' => array('data-value'=> 'Yes'), '0' => array('data-value'=> 'No'));
		?>
		<div class="data gonioCross">
			<div class="gonioSup">
				<?php echo CHtml::dropDownList($side.'_gonio_sup_basic', ($element->{$side.'_gonio_sup'}) ? $element->{$side.'_gonio_sup'}->seen : true, $basic_options, array('class' => 'gonioGrade gonioBasic', 'data-position' => 'sup', 'options' => $html_options))?>
			</div>
			<div class="gonioTem">
				<?php echo CHtml::dropDownList($side.'_gonio_tem_basic', ($element->{$side.'_gonio_tem'}) ? $element->{$side.'_gonio_tem'}->seen : true, $basic_options, array('class' => 'gonioGrade gonioBasic', 'data-position' => 'tem', 'options' => $html_options))?>
			</div>
			<div class="gonioNas">
				<?php echo CHtml::dropDownList($side.'_gonio_nas_basic', ($element->{$side.'_gonio_nas'}) ? $element->{$side.'_gonio_nas'}->seen : true, $basic_options, array('class' => 'gonioGrade gonioBasic', 'data-position' => 'nas', 'options' => $html_options))?>
			</div>
			<div class="gonioInf">
				<?php echo CHtml::dropDownList($side.'_gonio_inf_basic', ($element->{$side.'_gonio_inf'}) ? $element->{$side.'_gonio_inf'}->seen : true, $basic_options, array('class' => 'gonioGrade gonioBasic', 'data-position' => 'inf', 'options' => $html_options))?>
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="van_herick">
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_van_herick_id'); ?>
			(
			<?php echo CHtml::link('images', '#', array('class' => 'foster_images_link')); ?>
			):
		</div>
		<div class="data">
			<?php echo CHtml::activeDropDownList($element, $side.'_van_herick_id', $element->getVanHerickOptions(), array('class' => 'clearWithEyedraw')); ?>
			<div data-side="<?php echo $side?>" class="foster_images_dialog"
				title="Foster Images">
				<img usemap="#<?php echo $side ?>_foster_images_map"
					src="<?php echo $this->assetPath ?>/img/gonioscopy.png">
				<map name="<?php echo $side ?>_foster_images_map">
					<area data-vh="5" shape="rect" coords="0,0,225,225" />
					<area data-vh="15" shape="rect" coords="0,225,225,450" />
					<area data-vh="25" shape="rect" coords="0,450,225,675" />
					<area data-vh="30" shape="rect" coords="225,0,450,225" />
					<area data-vh="75" shape="rect" coords="225,225,450,450" />
					<area data-vh="100" shape="rect" coords="225,450,450,675" />
				</map>
			</div>
		</div>
	</div>
	<div>
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_description'); ?>
			:
		</div>
		<div class="data">
			<?php echo CHtml::activeTextArea($element, $side.'_description', array('rows' => "2", 'cols' => "20", 'class' => 'autosize clearWithEyedraw')) ?>
		</div>
	</div>
	<button class="ed_report">Report</button>
	<button class="ed_clear">Clear</button>
</div>
