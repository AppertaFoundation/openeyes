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
$layoutColumns=$form->layoutColumns;
$form->layoutColumns=array('label'=>3,'field'=>9);
if( $element->patientId > 0) {
	$latestData = $element->findBySql("
						SELECT eob.* FROM et_ophtroperationnote_biometry eob
										WHERE eob.patient_id=" . $element->patientId . "
										ORDER BY eob.last_modified_date
										DESC LIMIT 1; ");
}else{
	$latestData = NULL;
}
?>
<div class="element-fields">
	<div class="row ">
		<div class="fixed column">
			<?php if( ! $latestData ) { ?>
				<div class="alert-box">No biometry data presented.</div>
			<?php }else { ?>
				<div class="alert-box">Biometry data last modified:
                    <?php echo date("F j, Y, g:i a", strtotime($latestData->{'last_modified_date'})); ?></div>
				<?php
				$this->renderPartial('form_Element_OphTrOperationnote_Biometry_Data', array(
					'element' => $latestData,
					'form' => $form
				));
			}
			?>
		</div>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		// we always want to display the cataract element! somehow it is hidden by default...
		$('.Element_OphTrOperationnote_Cataract').show();   // this is a hack, need to find the Javascript hiding the element and why is it hidden
		$('.Element_OphTrOperationnote_Biometry').show();
	});
</script>
<?php $form->layoutColumns=$layoutColumns;?>
