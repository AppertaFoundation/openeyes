<div class="large-10 column event edit container">
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
	<div class="box event content view examination">
		<h2 class="event-title">Examination</h2>

		<div class="row">
			<div class="large-12 column">

				<!-- Element with active sub-element/s -->
				<section class="element">
					<header class="element-header">
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
						<h3 class="element-title">History</h3>
					</header>
					<div class="element-fields">
						<div class="field-row">
							<select>
								<option>-- History --</option>
							</select>
							<select>
								<option>-- Severity --</option>
							</select>
							<select>
								<option>-- Onset --</option>
							</select>
							<select>
								<option>-- Eye --</option>
							</select>
							<select>
								<option>-- Duration --</option>
							</select>
						</div>
						<div class="field-row">
							<textarea rows="1"></textarea>
						</div>
					</div>
					<div class="sub-elements">
						<div class="sub-element">
							<header class="sub-element-header">
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
								<h3 class="sub-element-title">Comorbidities</h3>
							</header>
							<div class="sub-element-fields">
								<select>
									<option>-- Add --</option>
								</select>
							</div>
						</div>
					</div>
				</section>

				<!-- Element with inactive sub-element/s -->
				<section class="element">
					<header class="element-header">
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
						<h3 class="element-title">History</h3>
					</header>
					<div class="element-fields">
						<div class="field-row">
							<select>
								<option>-- History --</option>
							</select>
							<select>
								<option>-- Severity --</option>
							</select>
							<select>
								<option>-- Onset --</option>
							</select>
							<select>
								<option>-- Eye --</option>
							</select>
							<select>
								<option>-- Duration --</option>
							</select>
						</div>
						<div class="field-row">
							<textarea rows="1"></textarea>
						</div>
					</div>
					<div class="sub-elements inactive">
						<ul class="optional-elements-list">
							<li>
								<a href="#">Comorbidities</a>
							</li>
						</ul>
					</div>
				</section>

				<!-- Element with eye-draw -->
				<section class="element">
					<header class="element-header">
						<div class="element-actions">
							<a href="#" title="View Previous" class="view-previous">
								<img src="/img/_elements/btns/load.png" alt="View previous">
							</a>
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
						<h3 class="element-title">Refraction</h3>
					</header>
					<div class="element-fields element-eyes row">
						<div class="element-eye right-eye column">
							<a href="#" class="icon-remove-side">Remove side</a>
							<div class="eyedraw-data row">
								<div class="eyedraw-image column small">
									<img src="/img/new/tmp/eyedraw-small-edit.png" class="canvas" alt="Eyedraw" />
								</div>
								<div class="eyedraw-fields column small">
									<div class="row field-row">
										<div class="large-3 column">
											<label>Sphere:</label>
										</div>
										<div class="large-9 column">
											<select><option>-</option></select>
											<select><option>0</option></select>
											<select><option>.00</option></select>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label>Cylinder:</label>
										</div>
										<div class="large-9 column">
											<select><option>-</option></select>
											<select><option>0</option></select>
											<select><option>.00</option></select>
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label for="axis">Axis:</label>
										</div>
										<div class="large-5 column end">
											<input type="text" class="small" id="axis" />
										</div>
									</div>
									<div class="row field-row">
										<div class="large-3 column">
											<label for="type">Type:</label>
										</div>
										<div class="large-5 column end">
											<select id="type">
												<option>Auto-refraction</option>
											</select>
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

				<section class="element Element_OphCiExamination_IntraocularPressure">
					<header class="element-header">
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
						<h3 class="element-title">Intraocular Pressure</h3>
					</header>
					<div class="element-fields element-eyes row">
						<div class="element-eye right-eye column">
							<div class="field-row">
								<select>
									<option value="1" selected="selected">NR</option>
								</select>
								<span class="element-label">
									mmHg,
								</span>
								<select>
									<option value="1" selected="selected">Goldmann</option>
								</select>
							</div>
						</div>
						<div class="element-eye left-eye column">
							<div class="field-row">
								<select>
									<option value="1" selected="selected">NR</option>
								</select>
								<span class="element-label">
									mmHg,
								</span>
								<select>
									<option value="1" selected="selected">Goldmann</option>
								</select>
							</div>
						</div>
					</div>
				</section>

				<section class="element Element_OphCiExamination_Dilation">
					<header class="element-header">
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
						<h3 class="element-title">Dilation</h3>
					</header>
					<div class="element-fields element-eyes row">
						<div class="element-eye right-eye column">
							<a href="#" class="icon-remove-side">Remove side</a>
							<div class="panel element-field">
								<h4>Add a treatment</h4>
								<div class="field-row">
									<select>
										<option value="">--- Please select ---</option>
									</select>
									<button class="small secondary">
										Clear
									</button>
								</div>
							</div>
							<div class="panel element-field">
								<h4>Treatments</h4>
								<table class="plain">
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
												<input class="input-time small" type="text" value="09:49" />
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
												<input class="input-time small" type="text" value="10:24" />
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
												<input class="input-time small" type="text" value="10:24" />
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

						</div>
						<div class="element-eye left-eye column">
							<a href="#" class="icon-remove-side">Remove side</a>
							<div class="panel element-field">
								<h4>Add a treatment</h4>
								<div class="field-row">
									<select>
										<option value="">--- Please select ---</option>
									</select>
									<button class="small secondary">
										Clear
									</button>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section class="element Element_OphCiExamination_Diagnoses">
					<header class="element-header">
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
						<h3 class="element-title">Diagnoses</h3>
					</header>
					<div class="element-fields">
						<div class="panel element-field">
							<h4>Diagnoses</h4>
							<table class="plain">
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
											<label>
												<input type="radio" />
												Right
											</label>
											<label>
												<input type="radio" />
												Both
											</label>
											<label>
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
											<label>
												<input type="radio" />
												Right
											</label>
											<label>
												<input type="radio" />
												Both
											</label>
											<label>
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
						<div class="panel element-field">
							<h4>Add a diagnosis</h4>
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
								<div class="large-3 column">
									<select>
										<option>Select a commonly used diagnosis</option>
									</select>
								</div>
								<div class="large-6 column">
									<input type="text" placeholder="or type the first few characters of a diagnosis" />
								</div>
							</div>
						</div>
					</div>
				</section>

				<section class="element">

					<header class="element-header">
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove sub-element</span>
							</a>
						</div>
						<h3 class="element-title">Visual Acuity</h3>
					</header>

					<div class="element-fields element-eyes row">
						<div class="element-eye column right-eye">
							<a href="#" class="icon-remove-side">Remove side</a>
							<table class="blank">
								<thead>
									<tr>
										<th colspan="3">Snellen Metre</th>
									</tr>
								</thead>
								<tbody>
									<tr class="visualAcuityReading" data-key="0">
										<td>
											<input type="hidden" name="visualacuity_reading[0][side]" value="0">
											<select class="va-selector" name="visualacuity_reading[0][value]" id="visualacuity_reading_0_value">
												<option value="126">6/3</option>
												<option value="119">6/4</option>
												<option value="114">6/5</option>
												<option value="110">6/6</option>
												<option value="101">6/9</option>
												<option value="95">6/12</option>
												<option value="86">6/18</option>
												<option value="80">6/24</option>
												<option value="71">6/36</option>
												<option value="60">6/60</option>
												<option value="45">3/60</option>
												<option value="36">2/60</option>
												<option value="21">1/60</option>
												<option value="4">CF</option>
												<option value="3">HM</option>
												<option value="2">PL</option>
												<option value="1">NPL</option>
											</select>
											<img src="/assets/84410d8e/img/icon_info.png" style="height:20px" alt="info" />
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
									<tr class="visualAcuityReading" data-key="1">
										<td>
											<input type="hidden" name="visualacuity_reading[1][side]" value="0">
											<select class="va-selector" name="visualacuity_reading[1][value]" id="visualacuity_reading_1_value">
												<option value="126">6/3</option>
												<option value="119">6/4</option>
												<option value="114">6/5</option>
												<option value="110">6/6</option>
												<option value="101">6/9</option>
												<option value="95">6/12</option>
												<option value="86">6/18</option>
												<option value="80">6/24</option>
												<option value="71">6/36</option>
												<option value="60">6/60</option>
												<option value="45">3/60</option>
												<option value="36">2/60</option>
												<option value="21">1/60</option>
												<option value="4">CF</option>
												<option value="3">HM</option>
												<option value="2">PL</option>
												<option value="1">NPL</option>
											</select>
											<img src="/assets/84410d8e/img/icon_info.png" style="height:20px" alt="info" />
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
						<div class="element-eye column left-eye">
							<a href="#" class="icon-remove-side">Remove side</a>
							<div class="element-label">Not recorded</div>
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

				<section class="element">
					<header class="element-header">
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove element</span>
							</a>
						</div>
						<h3 class="element-title">
							Clinical Management
						</h3>
					</header>
					<div class="element-fields">
						<div class="element-field">
							<div class="field-row">
								<select>
									<option>-- Add --</option>
								</select>
							</div>
							<div class="field-row">
								<textarea rows="1"></textarea>
							</div>
						</div>
					</div>
					<div class="sub-elements">
						<div class="sub-element">
							<header class="sub-element-header">
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
								<h4 class="sub-element-title">
									Cataract Management
								</h4>
							</header>
							<div class="sub-element-fields">
								<div class="row field-row">
									<div class="large-4 column">
										<label>
											Eye:
										</label>
									</div>
									<div class="large-8 column">
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
									<div class="large-4 column">
										<label>
											Post operative refractive target in dioptres:
										</label>
									</div>
									<div class="large-8 column">
										<input type="range" min="-20" max="20" value="0.0" step="0.5">
									</div>
								</div>
								<div class="row field-row">
									<div class="large-4 column">
										<label>
											The post operative refractive target has been discussed with the patient:
										</label>
									</div>
									<div class="large-8 column">
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
									<div class="large-4 column">
										<label>
											Suitable for surgeon:
										</label>
									</div>
									<div class="large-8 column">
										<select>
											<option value="">- Please select -</option>
										</select>
										<label class="inline">
											<input type="checkbox" value="1" />	Supervised
										</label>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-4 column">
										<label>
											Previous refractive surgery:
										</label>
									</div>
									<div class="large-8 column">
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
									<div class="large-4 column">
										<label>
											Vitrectomised eye:
										</label>
									</div>
									<div class="large-8 column">
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
						</div>
					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<div class="element-actions">
							<a href="#" class="button button-icon small">
								<span class="icon-button-small-mini-cross"></span>
								<span class="hide-offscreen">Remove element</span>
							</a>
						</div>
						<h3 class="element-title">
							Investigation
						</h3>
					</header>
					<div class="element-fields">
						<div class="field-row">
							<select><option>-- Add --</option></select>
						</div>
					</div>
					<div class="sub-elements">
						<div class="sub-element">
							<header class="sub-element-header">
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
								<h4 class="sub-element-title">
									OCT
								</h4>
							</header>
							<div class="sub-element-fields">
							test
							</div>
						</div>
					</div>
				</section>

				<section class="optional-elements">
					<header class="optional-elements-header">
						<div class="optional-elements-actions">
							<a href="#">
								<span>Add all</span>
								<img src="/img/_elements/icons/event-optional/element-added.png" alt="Add all" />
							</a>
							<a href="#">
								<span>Remove all</span>
								<img src="/img/_elements/icons/event-optional/element-remove.png" alt="Remove all" />
							</a>
						</div>
						<h3 class="element-title">Optional Elements</h3>
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
			</div>
		</div>
	</div>
</div>