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
										<input type="hidden" name="visualacuity_reading[0][side]" value="0">
										<select class="va-selector inline" name="visualacuity_reading[0][value]" id="visualacuity_reading_0_value">
											<option value="126">6/3</option>
										</select>
										<img src="<?php echo $assets_root_path?>assets/modules/OphCiExamination/assets/img/icon_info.png" style="height:20px" alt="info" />
									</td>
									<td>
										<select class="method_id" name="visualacuity_reading[0][method_id]" id="visualacuity_reading_0_method_id">
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
										<input type="hidden" name="visualacuity_reading[1][side]" value="0">
										<select class="va-selector inline" name="visualacuity_reading[1][value]" id="visualacuity_reading_1_value">
											<option value="126">6/3</option>
										</select>
										<img src="<?php echo $assets_root_path?>assets/modules/OphCiExamination/assets/img/icon_info.png" style="height:20px" alt="info" />
									</td>
									<td>
										<select class="method_id" name="visualacuity_reading[1][method_id]" id="visualacuity_reading_1_method_id">
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