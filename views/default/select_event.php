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
	$this->beginContent('//patient/event_container');
	$assetAliasPath = 'application.modules.OphCoCvi.assets';
    $this->moduleNameCssClass .= ' edit';
?>
	<div class="row">
		<div class="large-12 column">

			<section class="element">
                <header class="element-header">
						<h3 class="element-title">Create CVI</h3>
					</header>

					<div class="element-fields">

						<div class="field-row">
							<div class="field-info">
                                There are two or more CVIs for the patient. Please indicate whether you want another CVI to be created
							</div>
						</div>
                        <div class="field-row">
							<div class="field-info">
                                Current cvi are as follows:
                                <?php $i = 0;foreach($cvi_url as $cvi_event) {echo ($i!=0) ? ', ' : '';?>
                                <a href='<?=$cvi_event?>' ><?=$cvi_event?></a>
                                <?php $i++; } ?>
							</div>
						</div>
                        <div class="field-row">
							<div class="field-info">
                                <a href='<?= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&createnewcvi=1" ?>' >
                                    <button type="submit">
                                        Proceed to Create new CVI
                                    </button>
                                </a>
							</div>
						</div>
                    </div>
               </section>
		</div>
	</div>

<?php $this->endContent() ;?>
