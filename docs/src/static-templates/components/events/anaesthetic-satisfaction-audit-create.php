<div class="large-10 column event ophouanaestheticsatisfactionaudit edit">
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
		<h2 class="event-title">Anaesthetic Satisfaction Audit</h2>

		<div class="row">
			<div class="large-12 column">

				<!-- Anaesthetist -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Anaesthetist</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Anaesthetist</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
					</div>
				</section>

				<!-- Satisfaction -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Satisfaction</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row slider">
							<div class="large-2 column">
								<label for="">Pain:</label>
							</div>
							<div class="large-10 column end">
								<div class="field-row">
									<span class="slider-value">0</span>
									<input type="range" min="0" max="10" step="1" style="width:340px;" />
								</div>
								<div class="field-row">
									<img class="field_key" id="pain_key" src="/protected/modules/OphOuAnaestheticsatisfactionaudit/assets/img/painscale_adult.png" />
								</div>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Nausea:</label>
							</div>
							<div class="large-3 column end">
								<input type="range" />
							</div>
						</div>
						<div class="row field-row">
							<div class="large-10 large-offset-2 column">
								<div class="field-info">
									<em>0 - none, 1 - mild, 2 - moderate, 3 - severe</em>
								</div>
							</div>
						</div>

						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Vomited:</label>
							</div>
							<div class="large-10 column">
								<input type="checkbox" />
							</div>
						</div>
					</div>
				</section>

				<!-- Vital Signs -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Vital Signs</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Respiratory Rate:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Oxygen Saturation:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Systolic Blood Pressure:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Body Temperature:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Heart Rate:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Conscious Level AVPU:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
					</div>
				</section>

				<!-- Notes -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Notes</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Comments:</label>
							</div>
							<div class="large-10 column end">
								<textarea placeholder="Enter comments..."></textarea>
							</div>
						</div>
						<fieldset class="row field-row">
							<legend class="large-2 column">
								Ready for discharge from recovery:
							</legend>
							<div class="large-3 column end">
								<label class="inline highlight">
									<input type="radio"> Yes
								</label>
								<label class="inline highlight">
									<input type="radio" /> No
								</label>
							</div>
						</fieldset>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>