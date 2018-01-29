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
<section class=" element
	<?php
    $eye_divider_list = ['Visual Acuity','Anterior Segment','Intraocular Pressure',
        'Refraction','Gonioscopy','Adnexal','Pupillary Abnormalities', 'CCT','Keratoconus Monitoring',
        'Posterior Pole'];
  if ($element->elementType->name=='Medications'||$element->elementType->name=='Risks'){
    echo 'full';
  }elseif (in_array($element->elementType->name, $eye_divider_list)){
    echo 'full priority eye-divider view-visual-acuity';
  }
  elseif (@$child) {  echo 'tile'; } else { echo 'full priority';} ?>
	view-<?php echo $element->elementType->name?>">
	<?php if (!preg_match('/\[\-(.*)\-\]/', $element->elementType->name)) { ?>
		<header class=" element-header">
			<h3 class="element-title"><?php echo $element->elementType->name ?></h3>
		</header>
	<?php } ?>
	<?php echo $content;?>
</section>

<?php $child_elements = $this->getChildElements($element->getElementType());
      $new_elements = array();
      if (sizeof($child_elements)!=0) {
        foreach ($child_elements as $child_element) {
        if ($child_element->elementType->name == 'Medications') {
            $med_element = $child_element;
        }
        elseif ($child_element->elementType->name == 'Risks') {
            $risk_element = $child_element;
        }
        elseif ($child_element->elementType->name == 'Visual Acuity'){
            $visual_acuity_element = $child_element;
        }
        else{
          array_push($new_elements,$child_element);
        }
    }
          if (isset($visual_acuity_element)) {
              $this->renderSingleChildOpenElements($visual_acuity_element, 'view', @$form, @$data);
          }
        for ($i=0; $i<sizeof($new_elements); $i++) {
          if ($i %3== 0) { ?>
            <div class="flex-layout flex-left flex-stretch">
          <?php }
          $this->renderSingleChildOpenElements($new_elements[$i], 'view', @$form, @$data);
          if ($i %3 == 2 || $i == sizeof($new_elements)-1) {
              ?></div>
          <?php }
        }
        if (isset($med_element)) {
          $this->renderSingleChildOpenElements($med_element, 'view', @$form, @$data);
        }
        if (isset($risk_element)) {
          $this->renderSingleChildOpenElements($risk_element, 'view', @$form, @$data);
        }

}
  ?>