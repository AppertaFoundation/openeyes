<div class="large-10 column event edit container">
	<header class="event-header">
		<ul class="inline-list tabs event-actions">
			<li class="selected">
				<a href="#">Create</a>
			</li>
		</ul>
		<div class="button-bar right">
			<a href="#" class="button small">
				Print
			</a>
		</div>
	</header>
	<div class="box event content view examination">
		<h2 class="event-title">Examination</h2>

		<div class="row">
			<div class="large-12 column">

				<div class="element">
					<h3 class="element-title">History</h3>
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
				</div>

				<!-- Element with eye-draw -->
				<div class="element">
					<h3 class="element-title">Refraction</h3>
					<div class="element-fields element-eyes row">
						<div class="element-eye right-eye column">
							<a href="#" class="icon-remove-side">Remove side</a>
							<div class="eyedraw-data row">
								<div class="eyedraw-image column small">
									<img src="/img/new/tmp/eyedraw-small-edit.png" class="canvas">
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
				</div>

				<div class="element">
					<h3 class="element-title">Visual Acuity</h3>
					<div class="element-fields element-eyes row">
						<div class="element-eye column right-eye">
							<a href="#" class="icon-remove-side">Remove side</a>
							<table class="blank">
								<thead>
									<tr>
										<th>Snellen Metre</th>
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
											<span class="va-info-icon"><img src="/assets/84410d8e/img/icon_info.png" style="height:20px"></span>
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
											<span class="va-info-icon"><img src="/assets/84410d8e/img/icon_info.png" style="height:20px"></span>
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
				</div>

				<div class="optional-elements">
					<header class="optional-elements-header">
						<div class="optional-elements-actions">
							<a href="#">
								<span>Add all</span>
								<img src="/img/_elements/icons/event-optional/element-added.png" />
							</a>
							<a href="#">
								<span>Remove all</span>
								<img src="/img/_elements/icons/event-optional/element-remove.png" />
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
						<li>
							<a href="#">
								Clinical Management
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>