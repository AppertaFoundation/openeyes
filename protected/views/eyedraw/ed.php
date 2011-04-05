	<?Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/ed_drawing.js');?>
	<?Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/ed_vitreoretinal.js');?>
        <label for="<?=get_class($model)?>_<?= $side?>"><?php echo CHtml::encode($model->getAttributeLabel('image_string_' . $side)); ?></label><br />
        <?php echo CHtml::activeHiddenField($model, 'image_string_' . $side); ?>
        <?php echo CHtml::error($model,'image_string_' . $side); ?> <br />
        <div>
                <div>
                                <div>
                                       <span>
						<?if ($writeable) {?>
                                                <!-- Doodle toolbar -->
                                                <button disabled="true" id="moveToFront<?=get_class($model)?>_<?= $side?>" title="Move to front" onclick="drawing<?=get_class($model)?>_<?= $side?>.moveToFront(); return false;" ><img src="/img/eyedraw/moveToFront.gif"/></button>
                                                <button disabled="true" id="moveToBack<?=get_class($model)?>_<?= $side?>" title="Move to back" onclick="drawing<?=get_class($model)?>_<?= $side?>.moveToBack(); return false;" ><img src="/img/eyedraw/moveToBack.gif" /></button>
                                                <button disabled="true" id="delete<?=get_class($model)?>_<?= $side?>" title="Delete" onclick="drawing<?=get_class($model)?>_<?= $side?>.deleteDoodle(); return false;" ><img src="/img/eyedraw/delete.gif" /></button>
                                                <button disabled="true" id="lock<?=get_class($model)?>_<?= $side?>" title="Lock" onclick="drawing<?=get_class($model)?>_<?= $side?>.lock(); return false;" ><img src="/img/eyedraw/lock.gif" /></button>
                                                <button disabled="true" id="unlock<?=get_class($model)?>_<?= $side?>" title="Unlock" onclick="drawing<?=get_class($model)?>_<?= $side?>.unlock(); return false;" ><img src="/img/eyedraw/unlock.gif" /></button>
                                                <br />

                                                <!-- Doodle selection toolbar -->
                                                <button id="RRD<?=get_class($model)?>_<?= $side?>" title="Retinal detachment" onclick="drawing<?=get_class($model)?>_<?= $side?>.addDoodle('RRD'); return false;" ><img src="/img/eyedraw/rrd.gif" /></button>
                                                <button id="uTear<?=get_class($model)?>_<?= $side?>" title="U tear" onclick="drawing<?=get_class($model)?>_<?= $side?>.addDoodle('UTear'); return false;" ><img src="/img/eyedraw/uTear.gif" /></button>
                                                <button id="roundHole<?=get_class($model)?>_<?= $side?>" title="Round hole" onclick="drawing<?=get_class($model)?>_<?= $side?>.addDoodle('RoundHole'); return false;" ><img src="/img/eyedraw/roundHole.gif" /></button>
                                                <br />
						<?}?>
                                                <!-- add tabindex="1" to canvas after testing and remove highlighted border with css -->
                                                <canvas class="ed_canvas" id="canvas<?=get_class($model)?>_<?= $side?>" width="501" height="501"></canvas>
                                                <br />
						<?if ($writeable) {?>
                                                <button title="Report" onclick="report<?=get_class($model)?>_<?= $side?>(); return false;">Report</button><br />
						<?}?>
                                        </span>
                                </div>
                </div>
        </div>
