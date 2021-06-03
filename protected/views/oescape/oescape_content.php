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
<?php $this->renderPartial('//patient/_patient_alerts'); // load the patient alerts

// get Glaucoma or General Ophthalmology as an array
$subspecialty_arr = Subspecialty::model()->findAllByAttributes(array('short_name' => ['Glaucoma','General']));

// if on the Glaucoma or General Ophthalmology load the drill though for the IOP data
foreach ($subspecialty_arr as $sub) {
    if ($subspecialty->id === $sub->id) {
        $this->renderPartial('//oescape/oescape_drill_down_list');
    }
}
?>

<div class="oescape flex-layout flex-stretch"  id="oescape-layout">
    <?php echo $content; ?>
</div>
<script type="text/javascript">
    $('#oescape-layout').ready(function() {
        //Immediately set the size to the selected value
        setOEScapeSize();
        window.addEventListener('resize', () => {
            setOEScapeSize();
        });
    });
</script>
