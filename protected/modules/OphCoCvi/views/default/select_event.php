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
                                This patient already has a CVI. Are you sure you want to create a new one?
							</div>
						</div>
                        <div class="field-row">
							<div class="field-info">
                                Current CVI are as follows:
                                <?php if($current_cvis) {
									$manager = $this->getManager();
								?>
									<ul>
									<?php foreach($current_cvis as $cvi_event) { ?>
										<li><a href='<?= $manager->getEventViewUri($cvi_event) ?>'>
												<?= Helper::convertMySQL2NHS($cvi_event->event_date) . ': ' . $manager->getDisplayStatusForEvent($cvi_event) ?></a>
										</li>
									<?php } ?>
									</ul>
								<?php } ?>
							</div>
						</div>
                        <div class="field-row">
							<div class="field-info">
                                <a href="<?= CHtml::encode($can_create) ? \Yii::app()->request->requestUri."&createnewcvi=1" : '#'; ?>" >
                                    <button class="primary small<?= $can_create ? '' : ' disabled' ?>">
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
