					<div class="eventDetail">
						<div class="label"><?php echo CHtml::encode($labeltext)?>:</div>
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
								<?php foreach ($fields as $field) {?>
									<?php echo CHtml::checkBox(get_class($element)."[$field]",$checked[$field],array('style' => 'margin-bottom: 20px;'))?> <?php echo CHtml::encode($element->getAttributeLabel($field))?><br/>
								<?php }?>
							</div>
						<?php }?>
					</div>
