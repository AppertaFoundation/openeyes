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
					<div class="eventDetail">
						<?php if (!@$options['nolabel']) {?>
							<div class="label"><?php if ($labeltext) echo CHtml::encode($labeltext).':'?></div>
						<?php }?>
						<?php if (!empty($columns)) {
							foreach ($columns as $i => $data) {?>
								<div class="datacol<?php echo $i+1?>">
									<?php foreach ($data as $n => $field) {?>
										<?php echo CHtml::checkBox(get_class($element)."[$field]",$checked[$field],array('style' => 'margin-bottom: 20px;'))?> <?php echo CHtml::encode($element->getAttributeLabel($field))?>
										<?php if (($n+1) < count($data)) {?><br/><?php }?>
									<?php }?>
								</div>
							<?php }?>
						<?php } else {?>
							<div class="data">
								<?php if (isset($options['header'])) {?>
									<div class="checkBoxArrayHeader">
										<?php echo $options['header']?>
									</div>
								<?php }?>
								<?php foreach ($fields as $field) {?>
									<?php echo CHtml::checkBox(get_class($element)."[$field]",$checked[$field],array('style' => 'margin-bottom: 20px;'))?> <?php echo CHtml::encode($element->getAttributeLabel($field))?><br/>
								<?php }?>
							</div>
						<?php }?>
					</div>
