<div class="large-10 column event ophtroperationnote edit">
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
		<h2 class="event-title">Operation note</h2>

		<div class="row">
			<div class="large-12 column">

				<!-- Procedure list -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Procedure list</h3>
					</header>
					<div class="element-fields">
						<fieldset class="row field-row">
							<legend class="large-2 column">
								Eye:
							</legend>
							<div class="large-10 column">
								<label class="inline highlight">
									<input type="radio" /> Right
								</label>
								<label class="inline highlight">
									<input type="radio" /> Both
								</label>
								<label class="inline highlight">
									<input type="radio" /> Left
								</label>
							</div>
						</fieldset>
						<div class="row field-row">
							<div class="large-2 column">
								<label>
									Procedures:
								</label>
							</div>
							<div class="large-4 column">
								<fieldset>
									<legend>Add a procedure:</legend>
									<div class="field-row">
										<select>
											<option>Select a subsection</option>
										</select>
									</div>
									<div class="field-row">
										<input type="text" placeholder="or enter procedure here..." />
									</div>
								</fieldset>
							</div>
							<div class="large-6 column">
								<div class="panel procedures">
									<table class="plain">
										<thead>
											<tr>
												<td>Procedure</td>
												<td>Actions</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Punctoplasty</td>
												<td><a href="#">Remove</a></td>
											</tr>
											<tr>
												<td>Blepharoplasty of lower lid</td>
												<td><a href="#">Remove</a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- On-demand element for added procedure -->
				<section class="element on-demand">
					<header class="element-header">
						<h3 class="element-title">Punctoplasty</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Comments:</label>
							</div>
							<div class="large-6 column end">
								<textarea placeholder="Enter comments..."></textarea>
							</div>
						</div>
					</div>
				</section>

				<!-- On-demand element for added procedure -->
				<section class="element on-demand validation-error">
					<header class="element-header">
						<h3 class="element-title">Blepharoplasty of lower lid</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Comments:</label>
							</div>
							<div class="large-6 column end">
								<textarea placeholder="Enter comments..."></textarea>
							</div>
						</div>
					</div>
				</section>

				<section class="element on-demand">
					<header class="element-header">
						<h3 class="element-title">Cataract</h3>
					</header>
					<div class="element-fields">
						<div class="row">
							<div class="large-8 column">
								<div class="row">
									<div class="large-6 column">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/large.png" />
									</div>
									<div class="large-6 column">
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Incision site:</label>
											</div>
											<div class="large-6 column">
												<select>
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Length:</label>
											</div>
											<div class="large-6 column">
												<input type="text" />
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Meridian:</label>
											</div>
											<div class="large-6 column">
												<input type="text" />
											</div>
										</div>
										<div class="row field-row">
											<div class="large-6 column">
												<label for="">Incision type:</label>
											</div>
											<div class="large-6 column">
												<select>
													<option>-- Select --</option>
												</select>
											</div>
										</div>
										<div class="row field-row">
											<div class="large-12 column">
												<label for="">Details:</label>
												<textarea placeholder="Enter details..."></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Consistent horizontal form -->
							<div class="large-4 column">
								<div class="row field-row">
									<div class="large-12 column">
										<label for="">IOL type:</label>
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-12 column">
										<label for="">IOL power:</label>
										<input type="text" />
									</div>
								</div>
								<div class="row field-row">
									<div class="large-12 column">
										<label for="">Predicted refraction:</label>
										<input type="text" />
									</div>
								</div>
								<div class="row field-row">
									<div class="large-12 column">
										<label for="">IOL position:</label>
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-12 column">
										<label for="">Devices:</label>
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-12 column">
										<label for="">Complications:</label>
										<div class="multi-select multi-select-list">
											<div class="field-row">
												<select>
													<option>-- Select --</option>
												</select>
											</div>
											<ul class="multi-select-selections field-row">
												<li>
													Decentered IOL
													<a href="#" class="remove-one">Remove</a>
												</li>
												<li>
													IOL exchange
													<a href="#" class="remove-one">Remove</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-12 column">
										<label for="">Complication notes:</label>
										<textarea placeholder="Enter complication notes..."></textarea>
									</div>
								</div>
							</div>

							<!-- Consistent horizontal form -->
							<!-- <div class="large-4 column">
								<div class="row field-row">
									<div class="large-6 column">
										<label for="">IOL type:</label>
									</div>
									<div class="large-6 column">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-6 column">
										<label for="">IOL power:</label>
									</div>
									<div class="large-6 column">
										<input type="text" />
									</div>
								</div>
								<div class="row field-row">
									<div class="large-6 column">
										<label for="">Predicted refraction:</label>
									</div>
									<div class="large-6 column">
										<input type="text" />
									</div>
								</div>
								<div class="row field-row">
									<div class="large-6 column">
										<label for="">IOL position:</label>
									</div>
									<div class="large-6 column">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-6 column">
										<label for="">Devices:</label>
									</div>
									<div class="large-6 column">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-6 column">
										<label for="">Complications:</label>
									</div>
									<div class="large-6 column">
										<div class="multi-select multi-select-list">
											<div class="field-row">
												<select>
													<option>-- Select --</option>
												</select>
											</div>
											<ul class="multi-select-selections field-row">
												<li>
													Decentered IOL
													<a href="#" class="remove-one">Remove</a>
												</li>
												<li>
													IOL exchange
													<a href="#" class="remove-one">Remove</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-12 column">
										<label for="">Complication notes:</label>
										<textarea placeholder="Enter complication notes..."></textarea>
									</div>
								</div>
							</div>-->
						</div>
					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Anaesthetic</h3>
					</header>
					<div class="element-fields">
						<fieldset class="row field-row">
							<legend class="large-2 column">
								Type:
							</legend>
							<div class="large-10 column">
								<label class="inline highlight">
									<input type="radio" /> Topical
								</label>
								<label class="inline highlight">
									<input type="radio" /> LA
								</label>
								<label class="inline highlight">
									<input type="radio" /> LAC
								</label>
								<label class="inline highlight">
									<input type="radio" /> LAS
								</label>
								<label class="inline highlight">
									<input type="radio" /> GA
								</label>
							</div>
						</fieldset>
						<fieldset class="row field-row">
							<legend class="large-2 column">
								Given by:
							</legend>
							<div class="large-10 column">
								<label class="inline highlight">
									<input type="radio" /> Anaesthetist
								</label>
								<label class="inline highlight">
									<input type="radio" /> Surgeon
								</label>
								<label class="inline highlight">
									<input type="radio" /> Nurse
								</label>
								<label class="inline highlight">
									<input type="radio" /> Anaesthetic technician
								</label>
								<label class="inline highlight">
									<input type="radio" /> Other
								</label>
							</div>
						</fieldset>
						<fieldset class="row field-row">
							<legend class="large-2 column">
								Delivery:
							</legend>
							<div class="large-10 column">
								<label class="inline highlight">
									<input type="radio" /> Retrobulbar
								</label>
								<label class="inline highlight">
									<input type="radio" /> Peribulbar
								</label>
								<label class="inline highlight">
									<input type="radio" /> Subtenons
								</label>
								<label class="inline highlight">
									<input type="radio" /> Subconjunctival
								</label>
								<label class="inline highlight">
									<input type="radio" /> Topical
								</label>
								<label class="inline highlight">
									<input type="radio" /> Topical and intracameral
								</label>
								<label class="inline highlight">
									<input type="radio" /> Other
								</label>
							</div>
						</fieldset>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Agents:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Complications:</label>
							</div>
							<div class="large-3 column end">
								<div class="multi-select multi-select-list">
									<div class="multi-select-dropdown-container">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
									<ul class="multi-select-selections">
										<li>
											Retro bulbar / peribulbar haemorrage
											<a href="#" class="remove-one">Remove</a>
										</li>
										<li>
											Patient pain - Moderate
											<a href="#" class="remove-one">Remove</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Comments:</label>
							</div>
							<div class="large-6 column end">
								<textarea placeholder="Enter comments..."></textarea>
							</div>
						</div>
					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Surgeon</h3>
					</header>
					<div class="element-fields">
						<div id="div_Element_OphTrOperationnote_Surgeon" class="row field-row">
							<div class="large-2 column">
								<label for="Element_OphTrOperationnote_Surgeon_surgeon_id">
									Surgeon:
								</label>
							</div>
							<div class="large-4 column end">
								<div class="row">
									<div class="large-9 column end">
										<select name="Element_OphTrOperationnote_Surgeon[surgeon_id]" id="Element_OphTrOperationnote_Surgeon_surgeon_id">
											<option value="">- Please select -</option>
										</select>
									</div>
								</div>
							</div>
							<div class="large-2 column">
								<label for="Element_OphTrOperationnote_Surgeon_assistant_id">
									Assistant:
								</label>
							</div>
							<div class="large-4 column end">
								<div class="row">
									<div class="large-9 column end">
										<select name="Element_OphTrOperationnote_Surgeon[assistant_id]" id="Element_OphTrOperationnote_Surgeon_assistant_id">
											<option value="">- None -</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div id="div_Element_OphTrOperationnote_Surgeon_supervising_surgeon_id" class="row field-row">

							<div class="large-2 column">
								<label for="Element_OphTrOperationnote_Surgeon_supervising_surgeon_id">Supervising surgeon:</label>
							</div>

							<div class="large-3 column end">

								<select name="Element_OphTrOperationnote_Surgeon[supervising_surgeon_id]" id="Element_OphTrOperationnote_Surgeon_supervising_surgeon_id">
									<option value="">- None -</option>
								</select>
							</div>
						</div>
					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Per-operative drugs</h3>
					</header>
					<div class="element-fields">
						<div class="row field-row">
							<div class="large-2 column">
								<label for="">Drugs:</label>
							</div>
							<div class="large-3 column end">
								<select>
									<option>-- Select --</option>
								</select>
							</div>
						</div>
					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Comments</h3>
					</header>
					<div class="element-fields">
						<div class="row">
							<div class="large-2 column">
								<label for="">Operation comments:</label>
							</div>
							<div class="large-4 column">
								<textarea placeholder="Enter comments..."></textarea>
							</div>
							<div class="large-6 column">
								<div class="row field-row">
									<div class="large-4 column">
										<label for="">Post-op instructions:</label>
									</div>
									<div class="large-8 column">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>
								<div class="row field-row">
									<div class="large-4 column">
										<label for="">Post-op instructions:</label>
									</div>
									<div class="large-8 column">
										<textarea placeholder="Enter instructions..."></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>