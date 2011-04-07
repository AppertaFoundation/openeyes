	<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/ed_drawing.js');?>
	<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/ed_vitreoretinal.js');?>
        <label for="<?php echo get_class($model)?>_<?php echo $side?>"><?php echo CHtml::encode($model->getAttributeLabel('image_string_' . $side)); ?></label><br />
        <?php echo CHtml::activeHiddenField($model, 'image_string_' . $side); ?>
        <?php echo CHtml::error($model,'image_string_' . $side); ?> <br />
        <div>
                <div>
                                <div>
                                       <span>
						<?php
							if ($writeable) {
						?>
                                                <!-- Doodle toolbar -->
                                                <button disabled="true" id="moveToFront<?php echo get_class($model)?>_<?php echo $side?>" title="Move to front" onclick="drawing<?php echo get_class($model)?>_<?php echo $side?>.moveToFront(); return false;" ><img src="/img/eyedraw/moveToFront.gif"/></button>
                                                <button disabled="true" id="moveToBack<?php echo get_class($model)?>_<?php echo $side?>" title="Move to back" onclick="drawing<?php echo get_class($model)?>_<?php echo $side?>.moveToBack(); return false;" ><img src="/img/eyedraw/moveToBack.gif" /></button>
                                                <button disabled="true" id="delete<?php echo get_class($model)?>_<?php echo $side?>" title="Delete" onclick="drawing<?php echo get_class($model)?>_<?php echo $side?>.deleteDoodle(); return false;" ><img src="/img/eyedraw/delete.gif" /></button>
                                                <button disabled="true" id="lock<?php echo get_class($model)?>_<?php echo $side?>" title="Lock" onclick="drawing<?php echo get_class($model)?>_<?php echo $side?>.lock(); return false;" ><img src="/img/eyedraw/lock.gif" /></button>
                                                <button disabled="true" id="unlock<?php echo get_class($model)?>_<?php echo $side?>" title="Unlock" onclick="drawing<?php echo get_class($model)?>_<?php echo $side?>.unlock(); return false;" ><img src="/img/eyedraw/unlock.gif" /></button>
                                                <br />

                                                <!-- Doodle selection toolbar -->
                                                <button id="RRD<?php echo get_class($model)?>_<?php echo $side?>" title="Retinal detachment" onclick="drawing<?php echo get_class($model)?>_<?php echo $side?>.addDoodle('RRD'); return false;" ><img src="/img/eyedraw/rrd.gif" /></button>
                                                <button id="uTear<?php echo get_class($model)?>_<?php echo $side?>" title="U tear" onclick="drawing<?php echo get_class($model)?>_<?php echo $side?>.addDoodle('UTear'); return false;" ><img src="/img/eyedraw/uTear.gif" /></button>
                                                <button id="roundHole<?php echo get_class($model)?>_<?php echo $side?>" title="Round hole" onclick="drawing<?php echo get_class($model)?>_<?php echo $side?>.addDoodle('RoundHole'); return false;" ><img src="/img/eyedraw/roundHole.gif" /></button>
                                                <br />
						<?php
							}
						?>
                                                <!-- add tabindex="1" to canvas after testing and remove highlighted border with css -->
                                                <canvas class="ed_canvas" id="canvas<?php echo get_class($model)?>_<?php echo $side?>" width="501" height="501"></canvas>
                                                <br />
						<?php
							if ($writeable) {
						?>
                                                <button title="Report" onclick="report<?php echo get_class($model)?>_<?php echo $side?>(); return false;">Report</button><br />
						<?php
							}
						?>
                                        </span>
                                </div>
                </div>
        </div>
