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
	<div><?php echo $element->eye ? $element->eye->name : 'Eye no specified'?></div>
	<?php if ($element->city_road) {?>
		<div>At City Road</div>
	<?php }?>
	<?php if ($element->satellite) {?>
		<div>At satellite</div>
	<?php }?>
	<?php if ($element->fast_track) {?>
		<div>Suitable for fast-track</div>
	<?php }?>
	<div>
		Target post-op refractive correction is <?php echo $element->target_postop_refraction?> Dioptres
	</div>
	<?php if ($element->correction_discussed) {?>
		<div>Post-op refractive correction has been discussed with the patient</div>
	<?php } else {?>
		<div>Post-op refractive correction has not been discussed with the patient</div>
	<?php }?>
	<div>
		Suitable for <?php echo $element->suitable_for_surgeon->name?> surgeon (<?php echo $element->supervised ? 'supervised' : 'unsupervised'?>)
	</div>
	<div>
		<?php echo $element->vitrectomised_eye ? 'Vitrectomised eye' : 'Non-vitrectomised eye'?>
	</div>
