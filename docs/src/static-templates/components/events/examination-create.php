<div class="large-10 column event ophciexamination edit">
	<header class="event-header">
		<ul class="inline-list tabs event-actions">
			<li class="selected">
				<a href="#">Create</a>
			</li>
		</ul>
		<div class="button-bar right">
			<a href="#" class="button small secondary">
				Save
			</a>
		</div>
	</header>
	<div class="event-content">
		<h2 class="event-title">Examination</h2>

		<!-- Validation errors -->
		<div class="alert-box alert with-icon validation-errors top">
			<a href="#" class="close">×</a>
			<p>Please fix the following input errors:</p>
			<ul>
				<li>History: Description cannot be blank.</li>
				<li>Posterior Pole: Left Description cannot be blank.</li>
				<li>Posterior Pole: Right Description cannot be blank.</li>
			</ul>
		</div>

		<div class="row">
			<div class="large-12 column">

				<!-- Element with active sub-element/s -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">History (with enabled sub-element)</h3>
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
					</header>
					<div class="element-fields">
						<div class="field-row text-macros">
							<select class="inline">
								<option>-- History --</option>
							</select>
							<select class="inline">
								<option>-- Severity --</option>
							</select>
							<select class="inline">
								<option>-- Onset --</option>
							</select>
							<select class="inline">
								<option>-- Eye --</option>
							</select>
							<select class="inline">
								<option>-- Duration --</option>
							</select>
						</div>
						<div class="field-row">
							<textarea rows="1"></textarea>
						</div>
					</div>
					<div class="sub-elements">

						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Commorbidities</h4>
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
							</header>
							<div class="sub-element-fields">
								<div class="field-row">
									<div class="multi-select">
										<div class="multi-select-dropdown-container">
											<select class="inline">
												<option>-- Add --</option>
											</select>
											<a href="#" class="remove-all">Remove all</a>
										</div>
										<ul class="multi-select-selections">
											<li>
												No comorbidities
											</li>
											<li>
												Hyperopia
												<a href="#" class="remove-one">Remove</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="field-row">
									<textarea placeholder="Enter comments here"></textarea>
								</div>
							</div>
						</section>

						<!-- Layout with labels: -->
						<!--
						<div class="sub-element-fields">
							<div class="field-row row">
								<div class="large-3 column">
									<label for="commorbidities-add">
										Add a Commorbidity:
									</label>
								</div>
								<div class="large-9 column">
									<select id="commorbidities-add">
										<option>-- Select --</option>
									</select>
								</div>
							</div>
							<div class="field-row row">
								<div class="large-3 column">
									<label for="commorbidities-comments">Comments:</label>
								</div>
								<div class="large-9 column">
									<textarea id="commorbidities-comments" placeholder="Enter comments here"></textarea>
								</div>
						</div>
					-->
				</div>
			</section>

			<!-- Element with inactive sub-element/s -->
			<section class="element">
				<header class="element-header">
					<h3 class="element-title">History (with disabled sub-element)</h3>
					<div class="element-actions">
						<a href="#" class="button button-icon small">
							<span class="icon-button-small-mini-cross"></span>
							<span class="hide-offscreen">Remove sub-element</span>
						</a>
					</div>
				</header>
				<div class="element-fields">
					<div class="field-row">
						<select class="inline">
							<option>-- History --</option>
						</select>
						<select class="inline">
							<option>-- Severity --</option>
						</select>
						<select class="inline">
							<option>-- Onset --</option>
						</select>
						<select class="inline">
							<option>-- Eye --</option>
						</select>
						<select class="inline">
							<option>-- Duration --</option>
						</select>
					</div>
					<div class="field-row">
						<textarea rows="1"></textarea>
					</div>
				</div>
				<div class="sub-elements inactive">
					<ul class="sub-elements-list">
						<li>
							<a href="#">Commorbidities</a>
						</li>
						<li>
							<a href="#">Another example sub-element</a>
						</li>
					</ul>
				</div>
			</section>

			<!-- Element with eye-draw -->
			<section class="element">
				<header class="element-header">
					<h3 class="element-title">Refraction</h3>
					<div class="element-actions">
						<a href="#" title="View Previous" class="view-previous">
							<img src="<?php echo $assets_root_path;?>assets/img/_elements/btns/load.png" alt="View previous">
						</a>
						<a href="#" class="button button-icon small">
							<span class="icon-button-small-mini-cross"></span>
							<span class="hide-offscreen">Remove sub-element</span>
						</a>
					</div>
				</header>
				<div class="element-fields element-eyes row">
					<div class="element-eye right-eye column">
						<a href="#" class="icon-remove-side">Remove side</a>


						<div class="eyedraw-row row refraction">
							<div class="fixed column">
								<img src="<?php echo $assets_root_path?>assets/img/eyedraw/small.png" class="canvas" alt="Eyedraw" />
							</div>
							<div class="fluid column">
								<div class="eyedraw-data eyedraw-row row">
									<div class="row field-row">
										<div class="large-3 column">
											<label>Sphere:</label>
										</div>
										<div class="large-9 column">
											<select class="inline"><option>-</option></select>
											<select class="inline"><option>0</option></select>
											<select class="inline"><option>.00</option></select>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Cylinder:</label>
										</div>
										<div class="large-9 column">
											<select class="inline"><option>-</option></select>
											<select class="inline"><option>0</option></select>
											<select class="inline"><option>.00</option></select>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label for="axis">Axis:</label>
										</div>
										<div class="large-6 column end">
											<input type="text" class="small" id="axis" />
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label for="type">Type:</label>
										</div>
										<div class="large-6 column end">
											<select id="type">
												<option>Auto-refraction</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="element-eye left-eye column">
						<div class="add-side">
							<a href="#">
								Add left side <span class="icon-add-side"></span>
							</a>
						</div>
					</div>
				</div>
			</section>

			<section class="element">

				<header class="element-header">
					<h3 class="element-title">Visual Acuity</h3>
					<div class="element-title-additional">
						<select class="inline" id="visualacuity_unit_change">
							<option value="1">ETDRS Letters</option>
							<option value="4">logMAR</option>
							<option value="2" selected="selected">Snellen Metre
							</option>
						</select>
						<div class="info"><small><em>Some info</em></small></div>
					</div>
					<div class="element-actions">
						<a href="#" class="button button-icon small">
							<span class="icon-button-small-mini-cross"></span>
							<span class="hide-offscreen">Remove sub-element</span>
						</a>
					</div>
				</header>

				<div class="element-fields element-eyes row">
					<div class="element-eye right-eye column">
						<a href="#" class="icon-remove-side">Remove side</a>
						<table class="blank">
							<thead>
								<tr>
									<th colspan="3">Snellen Metre</th>
								</tr>
							</thead>
							<tbody>
								<tr class="visualAcuityReading visual-acuity-reading" data-key="0">
									<td>
										<input type="hidden" value="0">
										<select class="va-selector inline" id="visualacuity_reading_0_value">
											<option value="126">6/3</option>
										</select>
										<img src="<?php echo $assets_root_path?>assets/modules/OphCiExamination/assets/img/icon_info.png" style="height:20px" alt="info" />
									</td>
									<td>
										<select class="method_id" id="visualacuity_reading_0_method_id">
											<option value="1">Unaided</option>
											<option value="2">Glasses</option>
											<option value="3">Contact lens</option>
											<option value="4">Pinhole</option>
											<option value="5">Auto-refraction</option>
											<option value="6">Formal refraction</option>
										</select>
									</td>
									<td class="readingActions">
										<a class="removeReading" href="#">Remove</a>
									</td>
								</tr>
								<tr class="visualAcuityReading visual-acuity-reading" data-key="1">
									<td>
										<input type="hidden" value="0">
										<select class="va-selector inline" id="visualacuity_reading_1_value">
											<option value="126">6/3</option>
										</select>
										<img src="<?php echo $assets_root_path?>assets/modules/OphCiExamination/assets/img/icon_info.png" style="height:20px" alt="info" />
									</td>
									<td>
										<select class="method_id" id="visualacuity_reading_1_method_id">
											<option value="1">Unaided</option>
											<option value="2">Glasses</option>
											<option value="3">Contact lens</option>
											<option value="4">Pinhole</option>
											<option value="5">Auto-refraction</option>
											<option value="6">Formal refraction</option>
										</select>
									</td>
									<td class="readingActions">
										<a class="removeReading" href="#">Remove</a>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="field-row">
							<button class="button small secondary">
								Add
							</button>
						</div>
						<div class="field-row">
							<textarea rows="1"></textarea>
						</div>
					</div>
					<div class="element-eye left-eye column">
						<a href="#" class="icon-remove-side">Remove side</a>
						<!-- <div class="field-row field-info">Not recorded</div> -->

						<div class="field-row row noReadings">
							<div class="large-4 column">
								<div class="field-info">Not recorded</div>
							</div>
							<div class="large-8 column end">
								<label class="inline">
									<input type="checkbox" />	Unable to assess
								</label>
								<label class="inline">
									<input type="checkbox" /> Eye missing
								</label>
							</div>
						</div>


						<div class="field-row">
							<button class="button small secondary">
								Add
							</button>
						</div>
						<div class="field-row">
							<textarea rows="1"></textarea>
						</div>
					</div>
				</div>
			</section>

			<section class="element Element_OphCiExamination_Gonioscopy">
				<header class="element-header">

					<h3 class="element-title">Gonioscopy</h3>

					<div class="element-actions">
						<a href="#" class="button button-icon small js-remove-element">
							<span class="icon-button-small-mini-cross"></span>
							<span class="hide-offscreen">Remove element</span>
						</a>
					</div>
				</header>

				<div class="element-fields element-eyes row">
					<div class="element-eye right-eye column side left" data-side="right">
						<div class="active-form">
							<a href="#" class="icon-remove-side remove-side">Remove side</a>
							<div class="eyedraw-row row gonioscopy">
								<div class="fixed column">
									<div class="EyeDrawWidget" id="eyedrawwidget_right_51">
										<ul class="ed_toolbar clearfix">
											<li class="ed_img_button action" id="moveToFrontright_51">
												<a href="#" data-function="moveToFront">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/moveToFront.gif">
												</a>
												<span>Move to front</span>
											</li>
											<li class="ed_img_button action" id="moveToBackright_51">
												<a href="#" data-function="moveToBack">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/moveToBack.gif">
												</a>
												<span>Move to back</span>
											</li>
											<li class="ed_img_button action" id="deleteSelectedDoodleright_51">
												<a href="#" data-function="deleteSelectedDoodle">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/deleteSelectedDoodle.gif">
												</a>
												<span>Delete</span>
											</li>
											<li class="ed_img_button action" id="resetEyedrawright_51">
												<a href="#" data-function="resetEyedraw">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/resetEyedraw.gif">
												</a>
												<span>Reset eyedraw</span>
											</li>
											<li class="ed_img_button action" id="lockright_51">
												<a href="#" data-function="lock">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/lock.gif">
												</a>
												<span>Lock</span>
											</li>
											<li class="ed_img_button action" id="unlockright_51">
												<a href="#" data-function="unlock">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/unlock.gif">
												</a>
												<span>Unlock</span>
											</li>
											<li class="ed_img_button action" id="Labelright_51">
												<a href="#" data-function="addDoodle" data-arg="Label">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/Label.gif">
												</a>
												<span>Label</span>
											</li>
										</ul>
										<ul class="ed_toolbar clearfix" id="ed_canvas_edit_right_51doodleToolbar0">
											<li class="ed_img_button action" id="AngleNVright_51">
												<a href="#" data-function="addDoodle" data-arg="AngleNV">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/AngleNV.gif">
												</a>
												<span>Angle new vessels</span>
											</li>
											<li class="ed_img_button action" id="AntSynechright_51">
												<a href="#" data-function="addDoodle" data-arg="AntSynech">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/AntSynech.gif">
												</a>
												<span>Anterior synechiae</span>
											</li>
										</ul>
										<canvas id="ed_canvas_edit_left_51" class="ed_canvas_edit" width="300" height="300" tabindex="1" data-drawing-name="ed_drawing_edit_left_51">
										</canvas>
									</div>
								</div>
								<div class="fluid column">
									<div class="eyedraw-fields">

										<div style="display: none;" class="shaffer-grade">
											<div class="field-label">Shaffer grade:</div>
											<div class="gonio-cross">
												<div class="gonio-sup">
													<select class="inline gonioGrade gonioExpert" data-position="sup" id="Element_OphCiExamination_Gonioscopy_right_gonio_sup_id">
														<option value="1" selected="selected" data-value="4">4</option>
													</select>
												</div>
												<div class="gonio-tem">
													<select class="inline gonioGrade gonioExpert" data-position="tem" id="Element_OphCiExamination_Gonioscopy_right_gonio_tem_id">
														<option value="1" selected="selected" data-value="4">4</option>
													</select>
												</div>
												<div class="gonio-nas">
													<select class="inline gonioGrade gonioExpert" data-position="nas" id="Element_OphCiExamination_Gonioscopy_right_gonio_nas_id">
														<option value="1" selected="selected" data-value="4">4</option>
													</select>
												</div>
												<div class="gonio-inf">
													<select class="inline gonioGrade gonioExpert" data-position="inf" id="Element_OphCiExamination_Gonioscopy_right_gonio_inf_id">
														<option value="1" selected="selected" data-value="4">4</option>
													</select>
												</div>
											</div>
										</div>

										<div class="basic-grade">
											<div class="field-label">Angle Open?:</div>
											<div class="gonio-cross">
												<div class="gonio-sup">
													<select class="inline gonioGrade gonioBasic" data-position="sup" id="right_gonio_sup_basic">
														<option value="0" data-value="No">No</option>
														<option value="1" selected="selected" data-value="Yes">Yes</option>
													</select>
												</div>
												<div class="gonio-tem">
													<select class="inline gonioGrade gonioBasic" data-position="tem" id="right_gonio_tem_basic">
														<option value="0" data-value="No">No</option>
														<option value="1" selected="selected" data-value="Yes">Yes</option>
													</select>
												</div>
												<div class="gonio-nas">
													<select class="inline gonioGrade gonioBasic" data-position="nas" id="right_gonio_nas_basic">
														<option value="0" data-value="No">No</option>
														<option value="1" selected="selected" data-value="Yes">Yes</option>
													</select>
												</div>
												<div class="gonio-inf">
													<select class="inline gonioGrade gonioBasic" data-position="inf" id="right_gonio_inf_basic">
														<option value="0" data-value="No">No</option>
														<option value="1" selected="selected" data-value="Yes">Yes</option>
													</select>
												</div>
											</div>
										</div>

										<div class="van_herick field-row">
											<label for="Element_OphCiExamination_Gonioscopy_right_van_herick_id">
												Van Herick			(
												<a class="foster_images_link" href="#">images</a>			):
											</label>
											<select class="inline clearWithEyedraw" id="Element_OphCiExamination_Gonioscopy_right_van_herick_id">
												<option value="0">NR</option>
												<option value="1">5%</option>
												<option value="2">15%</option>
												<option value="3">25%</option>
												<option value="4">30%</option>
												<option value="5">75%</option>
												<option value="6">100%</option>
											</select>
										</div>

										<div class="field-row">
											<label for="Element_OphCiExamination_Gonioscopy_right_description">
												Description:
											</label>
											<textarea rows="2" class="autosize clearWithEyedraw" id="Element_OphCiExamination_Gonioscopy_right_description" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 52px;"></textarea>
										</div>
										<div class="field-row">
											<button class="ed_report secondary small">Report</button>
											<button class="ed_clear secondary small">Clear</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="inactive-form">
							<div class="add-side">
								<a href="#">
									Add right side <span class="icon-add-side"></span>
								</a>
							</div>
						</div>
					</div>
					<div class="element-eye right-eye column side right" data-side="left">
						<div class="active-form">
							<a href="#" class="icon-remove-side remove-side">Remove side</a>
							<div class="eyedraw-row row gonioscopy">
								<div class="fixed column">
									<div class="EyeDrawWidget" id="eyedrawwidget_left_51">

										<ul class="ed_toolbar clearfix">
											<li class="ed_img_button action" id="moveToFrontleft_51">
												<a href="#" data-function="moveToFront">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/moveToFront.gif">
												</a>
												<span>Move to front</span>
											</li>
											<li class="ed_img_button action" id="moveToBackleft_51">
												<a href="#" data-function="moveToBack">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/moveToBack.gif">
												</a>
												<span>Move to back</span>
											</li>
											<li class="ed_img_button action" id="deleteSelectedDoodleleft_51">
												<a href="#" data-function="deleteSelectedDoodle">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/deleteSelectedDoodle.gif">
												</a>
												<span>Delete</span>
											</li>
											<li class="ed_img_button action" id="resetEyedrawleft_51">
												<a href="#" data-function="resetEyedraw">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/resetEyedraw.gif">
												</a>
												<span>Reset eyedraw</span>
											</li>
											<li class="ed_img_button action" id="lockleft_51">
												<a href="#" data-function="lock">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/lock.gif">
												</a>
												<span>Lock</span>
											</li>
											<li class="ed_img_button action" id="unlockleft_51">
												<a href="#" data-function="unlock">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/unlock.gif">
												</a>
												<span>Unlock</span>
											</li>
											<li class="ed_img_button action" id="Labelleft_51">
												<a href="#" data-function="addDoodle" data-arg="Label">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/Label.gif">
												</a>
												<span>Label</span>
											</li>
										</ul>
										<ul class="ed_toolbar clearfix" id="ed_canvas_edit_left_51doodleToolbar0">
											<li class="ed_img_button action" id="AngleNVleft_51">
												<a href="#" data-function="addDoodle" data-arg="AngleNV">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/AngleNV.gif">
												</a>
												<span>Angle new vessels</span>
											</li>
											<li class="ed_img_button action" id="AntSynechleft_51">
												<a href="#" data-function="addDoodle" data-arg="AntSynech">
													<img src="<?php echo $assets_root_path;?>assets/modules/eyedraw/img/AntSynech.gif">
												</a>
												<span>Anterior synechiae</span>
											</li>
										</ul>
										<canvas id="ed_canvas_edit_left_51" class="ed_canvas_edit" width="300" height="300" tabindex="1" data-drawing-name="ed_drawing_edit_left_51">
										</canvas>
									</div>
								</div>
								<div class="fluid column">
									<div class="eyedraw-fields">

										<div style="display: none;" class="shaffer-grade">
											<div class="field-label">Shaffer grade:</div>
											<div class="gonio-cross">
												<div class="gonio-sup">
													<select class="inline gonioGrade gonioExpert" data-position="sup" id="Element_OphCiExamination_Gonioscopy_left_gonio_sup_id">
														<option value="1" selected="selected" data-value="4">4</option>
													</select>
												</div>
												<div class="gonio-tem">
													<select class="inline gonioGrade gonioExpert" data-position="tem" id="Element_OphCiExamination_Gonioscopy_left_gonio_tem_id">
														<option value="1" selected="selected" data-value="4">4</option>
													</select>
												</div>
												<div class="gonio-nas">
													<select class="inline gonioGrade gonioExpert" data-position="nas" id="Element_OphCiExamination_Gonioscopy_left_gonio_nas_id">
														<option value="1" selected="selected" data-value="4">4</option>
													</select>
												</div>
												<div class="gonio-inf">
													<select class="inline gonioGrade gonioExpert" data-position="inf" id="Element_OphCiExamination_Gonioscopy_left_gonio_inf_id">
														<option value="1" selected="selected" data-value="4">4</option>
													</select>
												</div>
											</div>
										</div>

										<div class="basic-grade">
											<div class="field-label">Angle Open?:</div>
											<div class="gonio-cross">
												<div class="gonio-sup">
													<select class="inline gonioGrade gonioBasic" data-position="sup" id="left_gonio_sup_basic">
														<option value="0" data-value="No">No</option>
														<option value="1" selected="selected" data-value="Yes">Yes</option>
													</select>
												</div>
												<div class="gonio-tem">
													<select class="inline gonioGrade gonioBasic" data-position="tem" id="left_gonio_tem_basic">
														<option value="0" data-value="No">No</option>
														<option value="1" selected="selected" data-value="Yes">Yes</option>
													</select>
												</div>
												<div class="gonio-nas">
													<select class="inline gonioGrade gonioBasic" data-position="nas" id="left_gonio_nas_basic">
														<option value="0" data-value="No">No</option>
														<option value="1" selected="selected" data-value="Yes">Yes</option>
													</select>
												</div>
												<div class="gonio-inf">
													<select class="inline gonioGrade gonioBasic" data-position="inf" id="left_gonio_inf_basic">
														<option value="0" data-value="No">No</option>
														<option value="1" selected="selected" data-value="Yes">Yes</option>
													</select>
												</div>
											</div>
										</div>

										<div class="van_herick field-row">
											<label for="Element_OphCiExamination_Gonioscopy_left_van_herick_id">
												Van Herick			(
												<a class="foster_images_link" href="#">images</a>			):
											</label>
											<select class="inline clearWithEyedraw" id="Element_OphCiExamination_Gonioscopy_left_van_herick_id">
												<option value="0">NR</option>
												<option value="1">5%</option>
												<option value="2">15%</option>
												<option value="3">25%</option>
												<option value="4">30%</option>
												<option value="5">75%</option>
												<option value="6">100%</option>
											</select>
										</div>

										<div class="field-row">
											<label for="Element_OphCiExamination_Gonioscopy_left_description">
												Description:
											</label>
											<textarea rows="2" class="autosize clearWithEyedraw" id="Element_OphCiExamination_Gonioscopy_left_description" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 52px;"></textarea>
										</div>

										<div class="field-row">
											<button class="ed_report secondary small">Report</button>
											<button class="ed_clear secondary small">Clear</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="inactive-form">
							<div class="add-side">
								<a href="#">
									Add left side <span class="icon-add-side"></span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section class="element Element_OphCiExamination_IntraocularPressure">
				<header class="element-header">
					<h3 class="element-title">Intraocular Pressure</h3>
					<div class="element-actions">
						<a href="#" class="button button-icon small">
							<span class="icon-button-small-mini-cross"></span>
							<span class="hide-offscreen">Remove sub-element</span>
						</a>
					</div>
				</header>
				<div class="element-fields element-eyes row">
					<div class="element-eye right-eye column">
						<div class="field-row">
							<select class="inline">
								<option value="1" selected="selected">NR</option>
							</select>
							<span class="field-label">
								mmHg,
							</span>
							<select class="inline">
								<option value="1" selected="selected">Goldmann</option>
							</select>
						</div>
					</div>
					<div class="element-eye left-eye column">
						<div class="field-row">
							<select class="inline">
								<option value="1" selected="selected">NR</option>
							</select>
							<span class="field-label">
								mmHg,
							</span>
							<select class="inline">
								<option value="1" selected="selected">Goldmann</option>
							</select>
						</div>
					</div>
				</div>
			</section>

			<section class="element Element_OphCiExamination_Dilation">
				<header class="element-header">
					<h3 class="element-title">Dilation</h3>
					<div class="element-actions">
						<a href="#" class="button button-icon small">
							<span class="icon-button-small-mini-cross"></span>
							<span class="hide-offscreen">Remove sub-element</span>
						</a>
					</div>
				</header>
				<div class="element-fields element-eyes row">
					<div class="element-eye right-eye column">
						<a href="#" class="icon-remove-side">Remove side</a>
						<div class="field-row">
							<select class="inline">
								<option value="">--- Please select ---</option>
							</select>
							<button class="small secondary">
								Clear
							</button>
						</div>
						<table class="plain grid">
							<thead>
								<tr>
									<th>Time</th>
									<th>Drug</th>
									<th>Drops</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody class="plain">
								<tr>
									<td>
										<input class="input-time" type="text" value="09:49" />
									</td>
									<td>
										Cyclopentolate 0.5%
									</td>
									<td>
										<select>
											<option value="1" selected="selected">1</option>
										</select>
									</td>
									<td>
										<a href="#" class="removeTreatment">Remove</a>
									</td>
								</tr>
								<tr>
									<td>
										<input class="input-time" type="text" value="10:24" />
									</td>
									<td>
										<span class="drug-name">Phenylephrine 2.5%</span>
									</td>
									<td>
										<select>
											<option value="1">1</option>
										</select>
									</td>
									<td>
										<a href="#" class="removeTreatment">Remove</a>
									</td>
								</tr>
								<tr>
									<td>
										<input class="input-time" type="text" value="10:24" />
									</td>
									<td>
										<span class="drug-name">Tropicamide 0.5%</span>
									</td>
									<td>
										<select>
											<option value="1">1</option>
										</select>
									</td>
									<td>
										<a href="#" class="removeTreatment">Remove</a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="element-eye left-eye column">
						<a href="#" class="icon-remove-side">Remove side</a>
						<div class="field-row">
							<select class="inline">
								<option value="">--- Please select ---</option>
							</select>
							<button class="small secondary">
								Clear
							</button>
						</div>
					</div>
				</div>
			</section>

			<section class="element Element_OphCiExamination_DRGrading">
				<header class="element-header">
					<h3 class="element-title">DR Grading</h3>
					<div class="element-title-additional">
						<div class="info">
							<a href="#" class="drgrading_images_link"><img src="<?php echo $assets_root_path;?>assets/modules/OphCiExamination/assets/img/photo_sm.png" /></a>
							<a href="#" id="drgrading_dirty">re-sync</a>
						</div>
					</div>
					<div class="element-actions">
						<a href="#" class="button button-icon small js-remove-element">
							<span class="icon-button-small-mini-cross"></span>
							<span class="hide-offscreen">Remove element</span>
						</a>
					</div>
				</header>
				<div class="sub-element-fields">
					<fieldset class="field-row row">
						<legend class="large-2 column">
							Diabetes type:
						</legend>
						<div class="large-10 column">
							<label class="inline highlight">
								<input type="radio" />
								Diabetes mellitus type 1
							</label>
							<label class="inline highlight">
								<input type="radio" />
								Diabetes mellitus type 2
							</label>
						</div>
					</fieldset>
				</div>
				<div class="sub-element-fields element-eyes row">
					<div class="element-eye right-eye column left side" data-side="right">
						<div class="active-form">
							<div class="row field-row">
								<div class="large-4 column">
									<label>
										Clinical Grading for retinopathy:
									</label>
								</div>
								<div class="large-8 column">
									<div class="wrapper field-highlight inline none">
										<select id="Element_OphCiExamination_DRGrading_right_clinicalret_id">
											<option value="1" class="none">None</option>
											<option value="2" class="mild">Mild nonproliferative retinopathy</option>
										</select>
									</div>
									<span class="grade-info-icon" data-info-type="clinicalret">
										<img src="<?php echo $assets_root_path?>assets/modules/OphCiExamination/assets/img/icon_info.png" style="height:20px">
									</span>
									<div class="quicklook grade-info" style="display: none;">
										<div id="Element_OphCiExamination_DRGrading_right_all_clinicalret_desc" class="grade-info-all ui-dialog-content ui-widget-content" data-select-id="Element_OphCiExamination_DRGrading_right_clinicalret_id" style="width: auto; min-height: 83px; height: auto;" scrolltop="0" scrollleft="0">
											<dl>
												<dt class="pill none">
													<a href="#" data-id="1">None</a>
												</dt>
												<dd class="none">
													No retinopathy
												</dd>
												<dt class="pill mild">
													<a href="#" data-id="2">Mild nonproliferative retinopathy</a>
												</dt>
												<dd class="mild">
													At least one microaneurysm
												</dd>
												<dt class="pill moderate">
													<a href="#" data-id="3">Moderate nonproliferative retinopathy</a>
												</dt>
												<dd class="moderate">
													Hemorrhages and/or microaneurysms ≥ standard photograph 2A*; and/or:<ul><li>soft exudates</li><li>venous beading</li><li>intraretinal microvascular abnormalities definitely present ( IRMA )</li></ul>
												</dd>
												<dt class="pill severe">
													<a href="#" data-id="4">Severe nonproliferative retinopathy</a>
												</dt>
												<dd class="severe">
													<ul><li>Soft exudates, venous beading, and intraretinal microvascular abnormalities all definitely present in at least two of fields four through seven</li><li>or two of the preceding three lesions present in at least two of fields four through seven and hemorrhages and microaneurysms present in these four fields, equaling or exceeding standard photo 2A in at least one of them</li><li>or intraretinal microvascular abnormalities present in each of fields four through seven and equaling or exceeding standard photograph 8A in at least two of them</li></ul>
												</dd>
												<dt class="pill early">
													<a href="#" data-id="5">Early proliferative retinopathy</a>
												</dt>
												<dd class="early">
													Proliferative retinopathy without Diabetic Retinopathy Study high-risk characteristic:<ul><li>New vessels</li></ul>
												</dd>
												<dt class="pill high-risk">
													<a href="#" data-id="6">High-risk proliferative retinopathy</a>
												</dt>
												<dd class="high-risk">
													Proliferative retinopathy with Diabetic Retinopathy Study high-risk characteristics:<ul><li>New vessels on or within one disc diameter of the optic disc (NVD) ≥ standard photograph 10A* (about one-quarter to one-third disc area), with or without vitreous or preretinal hemorrhage</li><li>vitreous and/or preretinal hemorrhage accompanied by new vessels, either NVD &lt; standard photograph 10A or new vessels elsewhere (NVE) ≥ one-quarter disc area</li></ul>
												</dd>
											</dl>
										</div>
									</div>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label>
										NSC retinopathy:
									</label>
								</div>
								<div class="large-8 column">
									<div class="wrapper field-highlight inline none">
										<select id="Element_OphCiExamination_DRGrading_right_nscretinopathy_id">
											<option value="1" class="none" data-code="NO">R0</option>
										</select>
									</div>
									<span class="grade-info-icon" data-info-type="retinopathy"><img src="<?php echo $assets_root_path?>assets/modules/OphCiExamination/assets/img/icon_info.png" style="height:20px"></span>
								</div>
							</div>


							<fieldset id="Element_OphCiExamination_DRGrading_right_nscretinopathy_photocoagulation" class="row field-row">
								<legend class="large-4 column">Retinopathy photocoagulation:</legend>
								<input type="hidden" value="">
								<div class="large-8 column end">
									<label class="inline highlight">
										<input value="1" id="Element_OphCiExamination_DRGrading_right_nscretinopathy_photocoagulation_1" type="radio">
										Yes
									</label>
									<label class="inline highlight">
										<input value="0" id="Element_OphCiExamination_DRGrading_right_nscretinopathy_photocoagulation_0" type="radio" checked="checked">
										No
									</label>
								</div>
							</fieldset>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="Element_OphCiExamination_DRGrading_right_clinicalmac_id">
										Clinical Grading for maculopathy:
									</label>
								</div>
								<div class="large-8 column">
									<div class="wrapper field-highlight inline none">
										<select id="Element_OphCiExamination_DRGrading_right_clinicalmac_id">
											<option value="1" class="none" data-code="NM">No macular oedema</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="Element_OphCiExamination_DRGrading_right_nscmaculopathy_id">
										NSC maculopathy:
									</label>
								</div>
								<div class="large-8 column">
									<div class="wrapper field-highlight inline none">
										<select id="Element_OphCiExamination_DRGrading_right_nscmaculopathy_id">
											<option value="1" class="none" data-code="NO">M0</option>
										</select>
									</div>
									<span class="grade-info-icon" data-info-type="maculopathy"><img src="<?php echo $assets_root_path?>assets/modules/OphCiExamination/assets/img/icon_info.png" style="height:20px"></span>
								</div>
							</div>
							<div class="inactive-form">
								<div class="add-side">
									Add left posterior segment
								</div>
							</div>
						</div>
					</div>
				</section>

				<section class="element Element_OphCiExamination_Diagnoses">
					<header class="element-header">
						<h3 class="element-title">Diagnoses</h3>
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-3 column">
								<label>Eye:</label>
							</div>
							<div class="large-9 column">
								<label class="inline highlight">
									<input type="radio" />
									Right
								</label>
								<label class="inline highlight">
									<input type="radio" />
									Both
								</label>
								<label class="inline highlight">
									<input type="radio" />
									Left
								</label>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-3 column">
								<label>Diagnosis:</label>
							</div>
							<div class="large-5 column end">
								<div class="field-row">
									<select>
										<option>Select a commonly used diagnosis</option>
									</select>
								</div>
								<input type="text" placeholder="or type the first few characters of a diagnosis" />
							</div>
						</div>

						<table class="plain grid">
							<thead>
								<tr>
									<th>Diagnosis</th>
									<th>Eye</th>
									<th>Principal</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										Blepharospasm
									</td>
									<td class="eye">
										<label class="inline">
											<input type="radio"/>
											Right
										</label>
										<label class="inline">
											<input type="radio" />
											Both
										</label>
										<label class="inline">
											<input type="radio" />
											Left
										</label>
									</td>
									<td>
										<input type="radio" />
									</td>
									<td>
										<a href="#">Remove</a>
									</td>
								</tr>
								<tr>
									<td>
										Cyst of eyelid
									</td>
									<td class="eye">
										<label class="inline">
											<input type="radio" />
											Right
										</label>
										<label class="inline">
											<input type="radio" />
											Both
										</label>
										<label class="inline">
											<input type="radio" />
											Left
										</label>
									</td>
									<td>
										<input type="radio" />
									</td>
									<td>
										<a href="#">Remove</a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Clinical Management</h3>
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove element</span>
							</a>
						</div>
					</header>
					<div class="element-fields">
						<div class="field-row">
							<select class="inline">
								<option>-- Add --</option>
							</select>
						</div>
						<div class="field-row">
							<textarea rows="1"></textarea>
						</div>
					</div>
					<div class="sub-elements">

						<!-- Cataract management sub-element -->
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Cataract Management</h4>
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
							</header>
							<div class="sub-element-fields">
								<div class="row field-row">
									<div class="large-3 column">
										<label>
											Eye:
										</label>
									</div>
									<div class="large-9 column">
										<label class="inline highlight">
											<input type="radio" />
											First eye
										</label>
										<label class="inline highlight">
											<input type="radio" />
											Second eye
										</label>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-3 column">
										<label>
											Post operative refractive target in dioptres:
										</label>
									</div>
									<div class="large-9 column">
										<input type="range" min="-20" max="20" value="0.0" step="0.5">
									</div>
								</div>
								<div class="row field-row">
									<div class="large-3 column">
										<label>
											The post operative refractive target has been discussed with the patient:
										</label>
									</div>
									<div class="large-9 column">
										<label class="inline highlight">
											<input type="radio" />
											Yes
										</label>
										<label class="inline highlight">
											<input type="radio" />
											No
										</label>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-3 column">
										<label>
											Suitable for surgeon:
										</label>
									</div>
									<div class="large-9 column">
										<select class="inline">
											<option value="">- Please select -</option>
										</select>
										<label class="inline">
											<input type="checkbox" value="1" />	Supervised
										</label>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-3 column">
										<label>
											Previous refractive surgery:
										</label>
									</div>
									<div class="large-9 column">
										<label class="inline highlight">
											<input type="radio" />
											Yes
										</label>
										<label class="inline highlight">
											<input type="radio" />
											No
										</label>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-3 column">
										<label>
											Vitrectomised eye:
										</label>
									</div>
									<div class="large-9 column">
										<label class="inline highlight">
											<input type="radio" />
											Yes
										</label>
										<label class="inline highlight">
											<input type="radio" />
											No
										</label>
									</div>
								</div>
							</div>
						</section>

						<!-- Laser management sub-element -->
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Laser Management</h4>
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
							</header>
							<div class="sub-element-fields">
								<div class="row field-row">
									<div class="large-3 column">
										<label>
											Laser:
										</label>
									</div>
									<div class="large-5 column end">
										<select>
											<option>Booked for a future data</option>
										</select>
									</div>
								</div>
							</div>
							<div class="element-eyes sub-element-fields">
								<div class="element-eye right-eye column">
									<a href="#" class="icon-remove-side">Remove side</a>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Laser type:</label>
										</div>
										<div class="large-7 column end">
											<select><option>-- Select --</option></select>
											<br />
											<span class="field-info hint">Ensure a Laser event is added for this patient when procedure is completed</span>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Comments:</label>
										</div>
										<div class="large-9 column">
											<textarea placeholder="Enter comments..."></textarea>
										</div>
									</div>
								</div>
								<div class="element-eye left-eye column">
									<a href="#" class="icon-remove-side">Remove side</a>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Laser type:</label>
										</div>
										<div class="large-7 column end">
											<select><option>-- Select --</option></select>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Comments:</label>
										</div>
										<div class="large-9 column">
											<textarea placeholder="Enter comments..."></textarea>
										</div>
									</div>
								</div>
							</div>
						</section>

						<!-- Injection management sub-element -->
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Injection Management</h4>
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
							</header>
							<div class="sub-element-fields">
								<fieldset class="row field-row">
									<legend class="large-3 column">
										Treatment:
									</legend>
									<div class="large-9 column">
										<label>
											<input type="checkbox" /> No treatment
										</label>
									</div>
								</fieldset>
							</div>
							<div class="sub-element-fields element-eyes row">
								<div class="element-eye right-eye column">
									<a href="#" class="icon-remove-side">Remove side</a>
									<div class="row field-row diagnosis-selection">
										<div class="large-3 column">
											<label>Diagnosis:</label>
										</div>
										<div class="large-9 column">
											<div class="row collapse">
												<div class="large-10 column">
													<div class="dropdown-row">
														<select>
															<option value="">- Please select -</option>
														</select>
													</div>
													<div class="autocomplete-row">
														<input placeholder="search for diagnosis" type="text" value="" />
													</div>
												</div>
												<div class="large-2 column">
													<div class="postfix">
														<button class="small button-icon small">
															<span class="icon-button-small-search"></span>
															<span class="hide-offscreen">Search</span>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="row field-row">
										<div class="large-3 column">
											<label>Risks:</label>
										</div>
										<div class="large-7 column end">
											<div class="multi-select multi-select-list">
												<div class="multi-select-dropdown-container">
													<select>
														<option>-- Select --</option>
														<option>Pre-existing glaucoma</option>
														<option>Previous glaucoma surgery</option>
														<option>Allergy to povidone iodine</option>
														<option>Previous interocular surgery</option>
														<option>CVA</option>
														<option>MI</option>
													</select>
												</div>
												<ul class="multi-select-selections">
													<li>
														Previous glaucoma surgery
														<a href="#" class="remove-one">Remove</a>
													</li>
													<li>
														Hyperopia
														<a href="#" class="remove-one">Remove</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Comments:</label>
										</div>
										<div class="large-9 column">
											<textarea placeholder="Enter comments..."></textarea>
										</div>
									</div>
								</div>
								<div class="element-eye left-eye column">
									<a href="#" class="icon-remove-side">Remove side</a>
									<div class="row field-row diagnosis-selection">
										<div class="large-3 column">
											<label>Diagnosis:</label>
										</div>
										<div class="large-9 column">
											<div class="row collapse">
												<div class="large-10 column">
													<div class="dropdown-row">
														<select>
															<option value="">- Please select -</option>
														</select>
													</div>
													<div class="autocomplete-row">
														<input placeholder="search for diagnosis" type="text" value="" />
													</div>
												</div>
												<div class="large-2 column">
													<div class="postfix">
														<button class="small button-icon small">
															<span class="icon-button-small-search"></span>
															<span class="hide-offscreen">Search</span>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Risks:</label>
										</div>
										<div class="large-7 column end">
											<select>
												<option>-- Select --</option>
												<option>Pre-existing glaucoma</option>
												<option>Previous glaucoma surgery</option>
												<option>Allergy to povidone iodine</option>
												<option>Previous interocular surgery</option>
												<option>CVA</option>
												<option>MI</option>
											</select>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Comments:</label>
										</div>
										<div class="large-9 column">
											<textarea placeholder="Enter comments..."></textarea>
										</div>
									</div>
								</div>
							</div>
						</section>

					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Risks</h3>
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove element</span>
							</a>
						</div>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label>Comments:</label>
							</div>
							<div class="large-8 column end">
								<textarea placeholder="Enter comments..."></textarea>
							</div>
						</div>
					</div>
					<div class="sub-elements">
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Glaucoma Risk Stratification</h4>
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
							</header>
							<div class="sub-element-fields">
								<div class="field-row row collapse">
									<div class="large-2 column">
										<div class="field-highlight moderate risk">
											<select class="full-width">
												<option>-- Select --</option>
												<option selected>Moderate</option>
											</select>
										</div>
									</div>
									<div class="large-10 column">
										<div class="postfix align">
											<a href="#" class="field-info">Definitions</a>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Investigation</h3>
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove element</span>
							</a>
						</div>
					</header>
					<div class="element-fields">
						<div class="field-row">
							<select class="inline">
								<option>-- Add --</option>
							</select>
						</div>
						<div class="field-row">
							<textarea rows="1"></textarea>
						</div>
					</div>
					<div class="sub-elements">
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">OCT</h4>
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
							</header>
							<div class="element-eyes sub-element-fields">
								<div class="element-eye right-eye column">
									<a href="#" class="icon-remove-side">Remove side</a>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Right method:</label>
										</div>
										<div class="large-3 column end">
											<select><option>Topcon</option></select>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Maximum CRT:</label>
										</div>
										<div class="large-9 column">
											<div class="row collapse">
												<div class="large-3 column">
													<input type="text" />
												</div>
												<div class="large-9 column">
													<div class="postfix field-info align">
														µm
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Central SFT:</label>
										</div>
										<div class="large-9 column">
											<div class="row collapse">
												<div class="large-3 column">
													<input type="text" />
												</div>
												<div class="large-9 column">
													<div class="postfix field-info align">
														µm
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="element-eye left-eye column">
									<a href="#" class="icon-remove-side">Remove side</a>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Left method:</label>
										</div>
										<div class="large-3 column end">
											<select>
												<option>Topcon</option>
											</select>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Maximum CRT:</label>
										</div>
										<div class="large-9 column">
											<div class="row collapse">
												<div class="large-3 column">
													<input type="text" />
												</div>
												<div class="large-9 column">
													<div class="postfix field-info align">
														µm
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Central SFT:</label>
										</div>
										<div class="large-9 column">
											<div class="row collapse">
												<div class="large-3 column">
													<input type="text" />
												</div>
												<div class="large-9 column">
													<div class="postfix field-info align">
														µm
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</section>

				<section class="optional-elements">
					<header class="optional-elements-header">
						<h3 class="optional-elements-title">Optional Elements</h3>
						<div class="optional-elements-actions">
							<a href="#">
								<span>Add all</span>
								<img src="<?php echo $assets_root_path;?>assets/img/_elements/icons/event-optional/element-added.png" alt="Add all" />
							</a>
							<a href="#">
								<span>Remove all</span>
								<img src="<?php echo $assets_root_path;?>assets/img/_elements/icons/event-optional/element-remove.png" alt="Remove all" />
							</a>
						</div>
					</header>
					<ul class="optional-elements-list">
						<li>
							<a href="#">History</a>
						</li>
						<li>
							<a href="#">Visual Acuity</a>
						</li>
						<li>
							<a href="#">
								Adnexal Comorbidity
							</a>
						</li>
						<li>
							<a href="#">
								Dilation
							</a>
						</li>
						<li class="clicked">
							<a href="#">
								Clinical Management (clicked state)
							</a>
						</li>
					</ul>
				</section>

				<!-- Validation errors -->
				<div class="alert-box alert with-icon validation-errors bottom">
					<a href="#" class="close">×</a>
					<p>Please fix the following input errors:</p>
					<ul>
						<li>History: Description cannot be blank.</li>
						<li>Posterior Pole: Left Description cannot be blank.</li>
						<li>Posterior Pole: Right Description cannot be blank.</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>