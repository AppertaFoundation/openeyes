<?php
function eyedraw($size = 'large', $id = 0) {
return <<<HTML
	<div class="eye-draw-widget" id="eyedrawwidget_right{$id}">
		<ul class="ed_toolbar clearfix">
			<li class="ed_img_button action" id="moveToFrontright{$id}">
				<a href="#" data-function="moveToFront">
					<img src="/module_assets/eyedraw/img/moveToFront.gif" />
				</a>
				<span>Move to front</span>
			</li>
			<li class="ed_img_button action" id="moveToBackright{$id}">
				<a href="#" data-function="moveToBack">
					<img src="/module_assets/eyedraw/img/moveToBack.gif" />
				</a>
				<span>Move to back</span>
			</li>
			<li class="ed_img_button action" id="deleteSelectedDoodleright{$id}">
				<a href="#" data-function="deleteSelectedDoodle">
					<img src="/module_assets/eyedraw/img/deleteSelectedDoodle.gif" />
				</a>
				<span>Delete</span>
			</li>
			<li class="ed_img_button action" id="resetEyedrawright{$id}">
				<a href="#" data-function="resetEyedraw">
					<img src="/module_assets/eyedraw/img/resetEyedraw.gif" />
				</a>
				<span>Reset eyedraw</span>
			</li>
			<li class="ed_img_button action" id="lockright{$id}">
				<a href="#" data-function="lock">
					<img src="/module_assets/eyedraw/img/lock.gif" />
				</a>
				<span>Lock</span>
			</li>
			<li class="ed_img_button action" id="unlockright{$id}">
				<a href="#" data-function="unlock">
					<img src="/module_assets/eyedraw/img/unlock.gif" />
				</a>
				<span>Unlock</span>
			</li>
			<li class="ed_img_button action" id="Labelright{$id}">
				<a href="#" data-function="addDoodle" data-arg="Label">
					<img src="/module_assets/eyedraw/img/Label.gif" />
				</a>
				<span>Label</span>
			</li>
		</ul>
		<ul class="ed_toolbar clearfix" id="ed_canvas_edit_right{$id}doodleToolbar0">
			<li class="ed_img_button action" id="InjectionSiteright{$id}">
				<a href="#" data-function="addDoodle" data-arg="InjectionSite">
					<img src="/module_assets/eyedraw/img/InjectionSite.gif" />
				</a>
				<span>Injection site</span>
			</li>
		</ul>
		<canvas id="ed_canvas_edit_right{$id}" class="ed_canvas_edit" width="300" height="300" tabindex="1" data-drawing-name="ed_drawing_edit_right{$id}">
		</canvas>
	</div>
	<script type="text/javascript">
		eyeDrawInit({'drawingName':'ed_drawing_edit_right{$id}','canvasId':'ed_canvas_edit_right{$id}','eye':0,'scale':0.5,'idSuffix':'right{$id}','isEditable':true,'focus':false,'graphicsPath':'/module_assets/eyedraw/img/','inputId':'Element_OphTrIntravitrealinjection_AnteriorSegment_right_eyedraw','onReadyCommandArray':[['addDoodle',['AntSeg']],['addDoodle',['InjectionSite']],['deselectDoodles',[]]],'onDoodlesLoadedCommandArray':[],'bindingArray':[],'deleteValueArray':[],'syncArray':[],'listenerArray':[function(){}],'offsetX':0,'offsetY':0,'toImage':false})
	</script>
HTML;
}
?>