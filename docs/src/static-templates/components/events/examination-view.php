<div class="large-10 column event view ophciexamination">
	<header class="event-header">
		<ul class="inline-list tabs event-actions">
			<li class="selected">
				<a href="#">View</a>
			</li>
			<li>
				<a href="#">Edit</a>
			</li>
		</ul>
		<div class="button-bar right">
			<a href="#" class="delete event-action button button-icon small">
				<span class="icon-button-small-trash-can"></span>
				<span class="hide-offscreen">Delete</span>
			</a>
			<a href="#" class="button small">
				Print
			</a>
		</div>
	</header>
	<div class="event-content">
		<h2 class="event-title">Examination</h2>

		<div class="row">
			<div class="large-12 column">

				<!-- Element with 'global' data containing a sub-element -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">History</h3>
					</header>
					<div class="element-data">
						<div class="data-row">
							<div class="data-value">
								History test 123
							</div>
						</div>
					</div>
					<div class="sub-elements">
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Sub element title</h4>
							</header>
							<div class="sub-element-data">
								<div class="data-row">
									<div class="data-value">
										Angina, Ethnicity
										<br />
										Test 123
									</div>
								</div>
							</div>
						</section>
					</div>
				</section>

				<!-- Element with eye-draw (small size) data for both eyes -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Refraction</h3>
					</header>
					<div class="element-data element-eyes row">
						<div class="element-eye right-eye column">
							<div class="data-row">
								<div class="row refraction">
									<div class="column fixed">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/small.png" class="canvas" alt="Eyedraw" />
									</div>
									<div class="column fluid">
										<div class="data-value">
											0.00/0.00 @ 95° Auto-refraction<br>
											Spherical equivalent: 0.00
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="element-eye left-eye column">
							<div class="data-row">
								<div class="row refraction">
									<div class="column fixed">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/small.png" class="canvas" alt="Eyedraw" />
									</div>
									<div class="column fluid">
										<div class="data-value">
											0.00/0.00 @ 95° Auto-refraction<br>
											Spherical equivalent: 0.00
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>

					<section class="element">
						<header class="element-header">
							<h3 class="element-title">Visual Acuity</h3>
						</header>
						<div class="element-data element-eyes row">
							<div class="element-eye right-eye column">
								<div class="data-row">
									<div class="data-value">Not recorded</div>
									<div class="data-value">Left eye Visual Acuity</div>
								</div>
							</div>
							<div class="element-eye left-eye column">
								<div class="data-row">
									<div class="data-value">Not recorded</div>
									<div class="data-value">Right eye Visual Acuity</div>
								</div>
							</div>
						</div>
					</section>

					<!-- Element with eye-draw (large-size) data for both eyes -->
					<section class="element">
						<header class="element-header">
							<h3 class="element-title">Gonioscopy</h3>
						</header>
						<div class="element-data element-eyes row">
							<div class="element-eye right-eye column">
								<div class="row gonioscopy">
									<div class="column fixed">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/medium.png" class="canvas" alt="Eyedraw" />
									</div>
									<div class="column fluid">
										<div class="shaffer-grade">
											<div class="data-label">Shaffer Grade:</div>
											<div class="gonio-cross">
												<div class="gonio-sup">
													<span class="data-value">
														4
													</span>
												</div>
												<div class="gonio-tem">
													<span class="data-value">
														4
													</span>
												</div>
												<div class="gonio-nas">
													<span class="data-value">
														4
													</span>
												</div>
												<div class="gonio-inf">
													<span class="data-value">
														4
													</span>
												</div>
											</div>
										</div>
										<div class="data-row">
											<span class="data-label">
												Van Herick:
											</span>
											<span class="data-value">
												NR
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="element-eye left-eye column">
								<div class="row gonioscopy">
									<div class="column fixed">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/medium.png" class="canvas" alt="Eyedraw" />
									</div>
									<div class="column fluid">
										<div class="shaffer-grade">
											<div class="data-label">Shaffer Grade:</div>
											<div class="gonio-cross">
												<div class="gonio-sup">
													<span class="data-value">
														4
													</span>
												</div>
												<div class="gonio-tem">
													<span class="data-value">
														4
													</span>
												</div>
												<div class="gonio-nas">
													<span class="data-value">
														4
													</span>
												</div>
												<div class="gonio-inf">
													<span class="data-value">
														4
													</span>
												</div>
											</div>
										</div>
										<div class="data-row">
											<span class="data-label">
												Van Herick:
											</span>
											<span class="data-value">
												NR
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>

				<!-- Element with eye-draw (large-size) data for both eyes, with
				sub-elements -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Anterior Segment</h3>
					</header>
					<div class="element-data element-eyes row">
						<div class="element-eye right-eye column">
							<div class="data-row">
								<div class="row anterior-segment">
									<div class="column fixed">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/medium.png" class="canvas" alt="Eyedraw" />
									</div>
									<div class="column fluid">
										<div class="data-row description">
											<div class="data-value">
												Moderate nuclear cataract, moderate cortical cataract
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">Pupil Size:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Large</div>
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">Nuclear:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Moderate</div>
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">Cortical:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Moderate</div>
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">PXF:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Yes</div>
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">Phakodonesis:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Yes</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="element-eye left-eye column">
							<div class="data-row">
								<div class="row anterior-segment">
									<div class="column fixed">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/medium.png" class="canvas" alt="Eyedraw" />
									</div>
									<div class="column fluid">
										<div class="data-row description">
											<div class="data-value">
												Moderate nuclear cataract, moderate cortical cataract
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">Pupil Size:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Large</div>
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">Nuclear:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Moderate</div>
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">Cortical:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Moderate</div>
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">PXF:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Yes</div>
											</div>
										</div>
										<div class="row data-row">
											<div class="large-4 column">
												<div class="data-label">Phakodonesis:</div>
											</div>
											<div class="large-8 column">
												<div class="data-value">Yes</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sub-elements">
						<section class="sub-element"> <!-- sub-element container -->
							<header class="sub-element-header">
								<h4 class="sub-element-title">CCT</h4>
							</header>
							<div class="sub-element-data sub-element-eyes row">
								<div class="element-eye right-eye column">
									<div class="data-row">
										<div class="data-value">
											234 µm (Ultrasound pachymetry)
										</div>
									</div>
								</div>
								<div class="element-eye left-eye column">
									<div class="data-row">
										<div class="data-value">
											234 µm (Ultrasound pachymetry)
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</section>

				<!-- Custom element -->
				<section class="element Element_OphCiExamination_IntraocularPressure">
					<header class="element-header">
						<h3 class="element-title">Intraocular Pressure</h3>
					</header>
					<div class="element-data element-eyes row">
						<div class="element-eye right-eye column">
							<div class="data-row">
								<div class="data-value">
									Right eye
								</div>
							</div>
						</div>
						<div class="element-eye left-eye column">
							<div class="data-row">
								<div class="data-value">
									Left eye
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Element with tabular data -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Dilation</h3>
					</header>
					<div class="element-data element-eyes row">
						<div class="element-eye right-eye column">
							<div class="data-row">
								<table class="element-table">
									<thead>
										<tr>
											<th>Time</th>
											<th>Drug</th>
											<th>Drops</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>09:49</td>
											<td>Cyclopentolate 0.5%</td>
											<td>1 drop</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="element-eye left-eye column">
							<div class="data-row">
								<table class="element-table">
									<thead>
										<tr>
											<th>Time</th>
											<th>Drug</th>
											<th>Drops</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>09:49</td>
											<td>Cyclopentolate 0.5%</td>
											<td>1 drop</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</section>

				<!-- Element with eyedraw (large-size) and sub-element with custom columns -->
				<section class="element">
					<header class="element-header">
						<h4 class="element-title">Posterior Pole</h4>
					</header>
					<div class="element-data element-eyes row">
						<div class="element-eye right-eye column">
							<div class="data-row">
								<div class="row posterior-pole">
									<div class="column fixed">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/medium.png" class="canvas" alt="Eyedraw" />
									</div>
									<div class="column fluid">
										<div class="row">
											<div class="data-value">
												No abnormality
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="element-eye left-eye column">
							<div class="data-row">
								<div class="row posterior-pole">
									<div class="column fixed">
										<img src="<?php echo $assets_root_path?>assets/img/eyedraw/medium.png" class="canvas" alt="Eyedraw" />
									</div>
									<div class="column fluid">
										<div class="row">
											<div class="data-value">
												No abnormality
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sub-elements">
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">
									DR Grading
								</h4>
							</header>
							<div class="sub-element-data sub-element-eyes row">
								<div class="element-eye right-eye column">
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												Clinical Grading for retinopathy:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												Mild nonproliferative retinopathy
											</div>
										</div>
									</div>
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												NSC retinopathy:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												R2
											</div>
										</div>
									</div>
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												Retinopathy photocoagulation:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												No
											</div>
										</div>
									</div>
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												NSC maculopathy:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												M1A
											</div>
										</div>
									</div>
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												Maculopathy photocoagulation:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												Yes
											</div>
										</div>
									</div>
								</div>
								<div class="element-eye left-eye column">
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												Clinical Grading for retinopathy:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												Mild nonproliferative retinopathy
											</div>
										</div>
									</div>
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												NSC retinopathy:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												R2
											</div>
										</div>
									</div>
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												Retinopathy photocoagulation:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												No
											</div>
										</div>
									</div>
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												NSC maculopathy:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												M1A
											</div>
										</div>
									</div>
									<div class="row data-row">
										<div class="large-4 column">
											<div class="data-label">
												Maculopathy photocoagulation:
											</div>
										</div>
										<div class="large-8 column">
											<div class="data-value">
												Yes
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</section>

				<!-- Element with 'global' data, containing a sub-element with tabular data -->
				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Clinical Management</h3>
					</header>
					<div class="element-data">
						<div class="data-row">
							<div class="data-label">Clinical Management</div>
						</div>
					</div>
					<div class="sub-elements">
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Cataract Management</h4>
							</header>
							<div class="sub-element-data">
								<div class="data-row">
									<table class="element-table">
										<tbody>
											<tr>
												<th scope="row">Eye</th>
												<td>First eye</td>
											</tr>
											<tr>
												<th scope="row">At City Road</th>
												<td>Yes</td>
											</tr>
											<tr>
												<th scope="row">At Satellite</th>
												<td>No</td>
											</tr>
											<tr>
												<th scope="row">Straightforward case</th>
												<td>No</td>
											</tr>
											<tr>
												<th scope="row">Post operative refractive target in dioptres</th>
												<td>0.0</td>
											</tr>
											<tr>
												<th scope="row">The post operative refractive target has been discussed with the patient</th>
												<td>Yes</td>
											</tr>
											<tr>
												<th scope="row">Suitable for surgeon</th>
												<td>Senior Surgeon</td>
											</tr>
											<tr>
												<th scope="row">Supervised</th>
												<td>No</td>
											</tr>
											<tr>
												<th scope="row">Previous refractive surgery</th>
												<td>Yes</td>
											</tr>
											<tr>
												<th scope="row">Vitrectomised eye</th>
												<td>No</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</section>
					</div>
				</section>

				<section class="element">
					<header class="element-header">
						<h3 class="element-title">Risks</h3>
					</header>
					<div class="element-data">
						<div class="data-row">
							<div class="data-value">
								Risks
							</div>
						</div>
					</div>
					<div class="sub-elements">
						<section class="sub-element">
							<header class="sub-element-header">
								<h4 class="sub-element-title">Glaucoma Risk Stratification</h4>
							</header>
							<div class="sub-element-data">
								<span class="pill high">
									High
								</span>
								<span class="pill moderate">
									Moderate
								</span>
								<span class="pill low">
									Low
								</span>
							</div>
						</section>
					</div>
				</section>

				<div class="metadata">
					<span class="info">
						Examination created by <span class="user">Enoch Root</span> on 1 Jan 2000 at 00:00
					</span>
					<span class="info">
						Examination created by <span class="user">Enoch Root</span> on 1 Jan 2000 at 00:00
					</span>
				</div>
			</div>
		</div>
	</div>
</div>