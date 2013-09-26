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
					<?php if (!@$htmlOptions['nowrapper']) {?>
						<div class="eventDetail"<?php if (@$htmlOptions['hidden']) {?> style="display: none;"<?php }?>>
							<?php unset($htmlOptions['hidden'])?>
							<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field))?>:</div>
							<div class="data">
								<?php }?>
								<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
									'name'=>$name,
									'id'=>get_class($element)."_".$field."_0",
									// additional javascript options for the date picker plugin
									'options'=>array_merge($options,array(
										'showAnim'=>'fold',
										'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
									)),
									'value' => (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$value) ? Helper::convertMySQL2NHS($value) : $value),
									'htmlOptions'=>$htmlOptions
								)); ?>
								<?php if (!@$htmlOptions['nowrapper']) {?>
							</div>
						</div>
					<?php }?>
