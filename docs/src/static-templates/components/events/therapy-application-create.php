<div class="large-10 column event ophcotherapyapplication edit">
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
		<h2 class="event-title">Therapy application</h2>

		<div class="row">
			<div class="large-12 column">

				<!-- Diagnosis -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Diagnosis</h3>
					</header>
					<div class="element-fields element-eyes row">
						<div class="element-eye right-eye column">
							<a href="#" class="icon-remove-side">Remove side</a>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">
										Diagnosis:
									</label>
								</div>
								<div class="large-8 column end">
									<div class="row diagnosis-selection">
										<div class="large-12 column end">
											<div class="row collapse">
												<div class="large-10 column">
													<div class="dropdown-row">
														<select>
															<option>- Please select -</option>
														</select>
													</div>
													<div class="autocomplete-row hide">
														<input placeholder="search for diagnosis" type="text" />
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
								</div>
							</div>

							<div class="row field-row">
								<div class="large-4 column">
									<label for="">
										Secondary To:
									</label>
								</div>
								<div class="large-8 column">
									<div class="row diagnosis-selection">
										<div class="large-12 column end">
											<div class="row collapse">
												<div class="large-10 column">
													<div class="dropdown-row">
														<select>
															<option>- Please Select -</option>
														</select>
													</div>
													<div class="autocomplete-row hide">
														<input placeholder="search for diagnosis" type="text" />
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

				<!-- Patient Suitability (without a treatment selection) -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Patient Suitability</h3>
					</header>
					<div class="element-fields element-eyes row">
						<div class="element-eye left-eye column">
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Treatment:</label>
								</div>
								<div class="large-4 column end">
									<select>
										<option>-- Select --</option>
									</select>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Angiogram Baseline Date:</label>
								</div>
								<div class="large-3 column end">
									<input type="text" />
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<div class="field-label">
										NICE Compliance
									</div>
								</div>
								<div class="large-8 column">
									<div class="field-value">
										Please select a treatment to determine compliance.
									</div>
								</div>
							</div>
						</div>
						<div class="element-eye right-eye column">
							<div class="eye-message">Select a diagnosis</div>
						</div>
					</div>
				</section>

				<!-- Patient Suitability (with a treatment selection) -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Patient Suitability</h3>
					</header>
					<div class="element-fields element-eyes row">
						<div class="element-eye left-eye column">
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Treatment:</label>
								</div>
								<div class="large-4 column end">
									<select>
										<option>Eylea</option>
									</select>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Angiogram Baseline Date:</label>
								</div>
								<div class="large-3 column end">
									<input type="text" />
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<div class="field-label">
										NICE Compliance
									</div>
								</div>
								<div class="large-8 column">
									<div class="compliance-container">
										<div class="outcome non-compliant">
											Non-Compliant
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="element-eye right-eye column">
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Treatment:</label>
								</div>
								<div class="large-4 column end">
									<select>
										<option>Eylea</option>
									</select>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Angiogram Baseline Date:</label>
								</div>
								<div class="large-4 column end">
									<input type="text" />
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<div class="field-label">
										NICE Compliance
									</div>
								</div>
								<div class="large-5 column end">
									<div class="compliance-container">
										<div class="field-row">
											<label for="">Patient has CNV?</label>
											<select>
												<option>- Select -</option>
											</select>
										</div>
										<div class="field-row">
											<label for="">CNV Secondary to AMD?</label>
											<select>
												<option>- Select -</option>
											</select>
										</div>
										<div class="outcome unknown">
											Unknown
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Relative ContraIndications -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Relative ContraIndications</h3>
					</header>
					<div class="element-fields">
						<fieldset class="row field-row">
							<legend class="large-2 column">
								Cerebrovascular accident:
							</legend>
							<div class="large-10 column">
								<label class="inline highlight">
									<input type="radio" /> Yes
								</label>
								<label class="inline highlight">
									<input type="radio" /> No
								</label>
							</div>
						</fieldset>
						<fieldset class="row field-row">
							<legend class="large-2 column">
								Ischaemic Attack:
							</legend>
							<div class="large-10 column">
								<label class="inline highlight">
									<input type="radio" /> Yes
								</label>
								<label class="inline highlight">
									<input type="radio" /> No
								</label>
							</div>
						</fieldset>
						<fieldset class="row field-row">
							<legend class="large-2 column">
								Myocardial Infarction:
							</legend>
							<div class="large-10 column">
								<label class="inline highlight">
									<input type="radio" /> Yes
								</label>
								<label class="inline highlight">
									<input type="radio" /> No
								</label>
							</div>
						</fieldset>
					</div>
				</section>

				<!-- MR Service Information -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">MR Service Information</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Consultant:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
					</div>
				</section>

				<!-- Exceptional Circumstances -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Exceptional Circumstances</h3>
					</header>
					<div class="element-fields element-eyes row">
						<div class="element-eye left-eye column">
							<fieldset class="row field-row">
								<legend class="large-4 column">
									Standard Intervention Exists
								</legend>
								<div class="large-8 column">
									<label class="inline highlight">
										<input type="radio" /> Yes
									</label>
									<label class="inline highlight">
										<input type="radio" /> No
									</label>
								</div>
							</fieldset>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Standard intervention</label>
								</div>
								<div class="large-6 column end">
									<select>
										<option>-- Select --</option>
									</select>
								</div>
							</div>
							<fieldset class="row field-row">
								<legend class="large-4 column">
									The standard intervention has been previously used
								</legend>
								<div class="large-8 column">
									<label class="inline highlight">
										<input type="radio" /> Yes
									</label>
									<label class="inline highlight">
										<input type="radio" /> No
									</label>
								</div>
							</fieldset>
							<fieldset class="row field-row">
								<legend class="large-4 column">
									The (non-standard) intervention applying for funding to be used is
								</legend>
								<div class="large-8 column">
									<label>
										<input type="radio" /> In addition to the standard (Additional)
									</label>
									<label>
										<input type="radio" /> Instead of the standard (Deviation)
									</label>
								</div>
							</fieldset>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">How is the patient significantly different to others with the same condition?</label>
								</div>
								<div class="large-8 column">
									<textarea></textarea>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">How is the patient likely to gain more benefit than otherwise?</label>
								</div>
								<div class="large-8 column">
									<textarea></textarea>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Previous Interventions:</label>
								</div>
								<div class="large-8 column">

									<div class="panel previous-interventions">
										<a href="#" class="icon-remove-side">Remove</a>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Start date</label>
											</div>
											<div class="large-5 column end">
												<input type="text" />
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">End date</label>
											</div>
											<div class="large-5 column end">
												<input type="text" />
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Treatment</label>
											</div>
											<div class="large-6 column">
												<select class="full-width">
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Pre treatment VA</label>
											</div>
											<div class="large-6 column">
												<select class="full-width">
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Post treatment VA</label>
											</div>
											<div class="large-6 column">
												<select class="full-width">
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Reason for stopping</label>
											</div>
											<div class="large-6 column">
												<select class="full-width">
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="field-row">
											<label for="">Comments</label>
											<textarea placeholder="Please provide pre and post treatment CMT"></textarea>
										</div>
									</div>

									<button class="secondary small">
										Add
									</button>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Relevant Interventions:</label>
								</div>
								<div class="large-8 column">

									<div class="panel previous-interventions">
										<a href="#" class="icon-remove-side">Remove</a>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Start date</label>
											</div>
											<div class="large-5 column end">
												<input type="text" />
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">End date</label>
											</div>
											<div class="large-5 column end">
												<input type="text" />
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Treatment</label>
											</div>
											<div class="large-6 column">
												<select class="full-width">
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Pre treatment VA</label>
											</div>
											<div class="large-6 column">
												<select class="full-width">
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Post treatment VA</label>
											</div>
											<div class="large-6 column">
												<select class="full-width">
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Reason for stopping</label>
											</div>
											<div class="large-6 column">
												<select class="full-width">
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="field-row">
											<label for="">Comments</label>
											<textarea placeholder="Please provide pre and post treatment CMT"></textarea>
										</div>
									</div>

									<button class="secondary small">
										Add
									</button>
								</div>
							</div>
							<fieldset class="row field-row">
								<legend class="large-4 column">
									Patient Factors
								</legend>
								<div class="large-8 column">
									<label class="inline highlight">
										<input type="radio" /> Yes
									</label>
									<label class="inline highlight">
										<input type="radio" /> No
									</label>
								</div>
							</fieldset>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Patient Expectations</label>
								</div>
								<div class="large-8 column">
									<textarea></textarea>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">Anticipated Start Date</label>
								</div>
								<div class="large-6 column end">
									<select>
										<option>-- Select --</option>
									</select>
								</div>
							</div>
							<div class="row field-row">
								<div class="large-4 column">
									<label for="">File attachments</label>
								</div>
								<div class="large-6 column end">
									<select>
										<option>-- Select --</option>
									</select>
								</div>
							</div>
						</div>
						<div class="element-eye right-eye column">
							<div class="eye-message">Select a diagnosis</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>