<div class="large-10 column event ophtrlaser edit">
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
		<h2 class="event-title">Laser</h2>

		<div class="row">
			<div class="large-12 column">

				<!-- Site -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Site</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Site:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Laser:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
						<div class="field-row">
							<div class="field-info">
								<em>Please select a site to see the list of available lasers.</em>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Surgeon:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
					</div>
				</section>

				<!-- Treatment -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Treatment</h3>
					</header>
					<div class="element-fields element-eyes row">
						<div class="element-eye right-eye column">
							<a href="#" class="icon-remove-side">Remove eye</a>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Procedures:</label>
								</div>
								<div class="large-6 column end">
									<div class="multi-select multi-select-list">
										<div class="multi-select-dropdown-container">
											<select>
												<option>-- Select --</option>
											</select>
										</div>
										<ul class="multi-select-selections">
											<li>
												Cycloablation
												<a href="#" class="remove-one">Remove</a>
											</li>
											<li>
												Laser iridoplasty
												<a href="#" class="remove-one">Remove</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="element-eye left-eye column">
							<a href="#" class="icon-remove-side">Remove eye</a>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Procedures:</label>
								</div>
								<div class="large-6 column end">
									<select>
										<option>-- Select --</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="sub-elements">
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Anterior Segment</h4>
								<div class="sub-element-actions">
									<a href="#" class="button button-icon small">
										<span class="icon-button-small-mini-cross"></span>
										<span class="hide-offscreen">Remove sub-element</span>
									</a>
								</div>
							</header>
							<div class="sub-element-fields element-eyes row">
								<div class="element-eye right-eye column">
									<a href="#" class="icon-remove-side">Remove eye</a>
									<div class="eyedraw-row row field-row anterior-segment">
										<div class="fixed column">
											<img src="<?php echo $assets_root_path?>assets/img/eyedraw/large.png" />
										</div>
									</div>
								</div>
								<div class="element-eye left-eye column">
									<a href="#" class="icon-remove-side">Remove eye</a>
									<div class="eyedraw-row row field-row anterior-segment">
										<div class="fixed column">
											<img src="<?php echo $assets_root_path?>assets/img/eyedraw/large.png" />
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<div class="sub-elements inactive">
						<ul class="sub-elements-list">
							<li>
								<a href="#">Posterior Pole</a>
							</li>
							<li>
								<a href="#">Fundus</a>
							</li>
						</ul>
					</div>
				</section>

				<!-- Comments -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Comments</h3>
					</header>
					<div class="element-fields">
						<div class="field-row row">
							<div class="large-2 column">
								<label for="">Comments:</label>
							</div>
							<div class="large-10 column">
								<textarea placeholder="Enter comments..."></textarea>
							</div>
						</div>
					</div>
				</section>

				<!-- Optional elements -->
				<section class="optional-elements">
					<header class="optional-elements-header">
						<h3 class="optional-elements-title">Optional Elements</h3>
						<div class="optional-elements-actions">
							<a href="#">
								<span>Add all</span>
								<img src="<?php echo $assets_root_path?>assets/img/_elements/icons/event-optional/element-added.png" alt="Add all" />
							</a>
							<a href="#">
								<span>Remove all</span>
								<img src="<?php echo $assets_root_path?>assets/img/_elements/icons/event-optional/element-remove.png" alt="Remove all" />
							</a>
						</div>
					</header>
					<ul class="optional-elements-list">
						<li>
							<a href="#">Comments</a>
						</li>
					</ul>
				</section>

			</div>
		</div>
	</div>
</div>
