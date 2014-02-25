<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header-logged-in.php'; ?>

		<div class="container content">
			<h1 class="badge">Patient summary</h1>

			<div class="messages patient">
				<div class="row">
					<div class="large-12 column">
						<div class="alert-box patient with-icon">
							<strong>Patient has allergies</strong>
							- Acetazolamide<br />
						</div>
					</div>
				</div>
			</div>

			<div class="row">

				<div class="large-6 column">

					<!-- Personal Details -->
					<section class="box patient-info">
						<h3 class="box-title">Personal Details:</h3>
						<a href="#" class="toggle-trigger toggle-hide">
							<span class="icon-showhide">
								Show/hide this section
							</span>
						</a>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">First name(s):</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">Ellen</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Last name:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">Kendall</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Address:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">
									68 Pink Lane<br/>
									Larbert<br/>
									Essex<br/>
									IQ68 5SD
								</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Date of Birth:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">
									23 Jan 1937
								</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Age:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">
									76
								</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Gender:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">
									Female
								</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Ethnic Group:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">
									Unknown
								</div>
							</div>
						</div>
					</section>

					<!-- Contact Details -->
					<section class="box patient-info">
						<h3 class="box-title">Contact details:</h3>
						<a href="#" class="toggle-trigger toggle-show">
							<span class="icon-showhide">
								Show/hide this section
							</span>
						</a>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Telephone:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">01000 6104099</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Email:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">Ellen.Kendall@hotmail.com</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Next of Kin:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">Unknown</div>
							</div>
						</div>
					</section>

					<!-- General Practitioner -->
					<section class="box patient-info">
						<h3 class="box-title">General Practitioner:</h3>
						<a href="#" class="toggle-trigger toggle-hide">
							<span class="icon-showhide">
								Show/hide this section
							</span>
						</a>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Name:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">Dr A Who</div>
							</div>
						</div>
						<div class="row data-row highlight">
							<div class="large-4 column">
								<div class="data-label">GP Address:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">Unknown</div>
							</div>
						</div>
						<div class="row data-row highlight">
							<div class="large-4 column">
								<div class="data-label">GP Telephone:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">0555 555 5555</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Practice Address:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">83 Wintour Lane, Northop, Heald Green, Lothian, GH14 2DH</div>
							</div>
						</div>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label">Practice Telephone:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value">0111 111 1111</div>
							</div>
						</div>
					</section>

					<!-- Associated contacts -->
					<section class="box patient-info">
						<h3 class="box-title">Associated contacts:</h3>
						<div class="data-row">
							<table class="plain patient-data patient-contacts">
								<thead>
									<tr>
										<th>Name</th>
										<th>Location</th>
										<th>Type</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Mr Bill Aylward FRCS FRCOphth MD</td>
										<td>Ealing Hospital</td>
										<td>Consultant Ophthalmologist</td>
										<td><a href="#">Edit</a> <a href="#">Remove</a></td>
									</tr>
									<tr>
										<td>Mr Bill Aylward FRCS FRCOphth MD</td>
										<td>Visioncare Eye Medical Centre</td>
										<td>Consultant Ophthalmologist</td>
										<td><a href="#">Edit</a> <a href="#">Remove</a></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="row data-row">
							<div class="large-2 column align">
								<label for="" class="align">Add contact:</label>
							</div>
							<div class="large-3 column">
								<input type="text" placeholder="search for contacts..." />
							</div>
							<div class="large-3 column end">
								<select>
									<option>Staff</option>
								</select>
							</div>
						</div>
					</section>

				</div>
				<div class="large-6 column">

					<section class="box patient-info episodes">
						<header class="box-header">
							<h3 class="box-title">All Episodes</h3>
							<div class="box-info">
								<strong>open 3</strong> |
								closed 0
							</div>
						</header>
						<table class="patient-episodes grid">
							<thead>
								<tr>
									<th>Start Date</th>
									<th>End Date</th>
									<th>Firm</th>
									<th>Subspecialty</th>
									<th>Eye</th>
									<th>Diagnosis</th>
								</tr>
							</thead>
							<tbody>
								<tr class="speciality">
									<td colspan="6">
										Ophthalmology
									</td>
								</tr>
								<tr class="clickable">
									<td>5 Dec 2011</td>
									<td></td>
									<td>Aylward Bill</td>
									<td>Vitreoretinal</td>
									<td>Left</td>
									<td>Traction detachment of retina</td>
								</tr>
								<tr class="clickable">
									<td>5 Dec 2011</td>
									<td></td>
									<td>Aylward Bill</td>
									<td>Vitreoretinal</td>
									<td>Left</td>
									<td>Traction detachment of retina</td>
								</tr>
								<tr class="clickable">
									<td>5 Dec 2011</td>
									<td></td>
									<td>Aylward Bill</td>
									<td>Vitreoretinal</td>
									<td>Left</td>
									<td>Traction detachment of retina</td>
								</tr>
							</tbody>
						</table>
					</section>

					<!-- Patient associated data box with closed form -->
					<section class="box patient-info associated-data">
						<header class="box-header">
							<h3 class="box-title">
								<span class="icon-patient-clinician-hd_flag"></span>
								Other ophthalmic diagnoses
							</h3>
							<a href="#" class="toggle-trigger toggle-hide">
								<span class="icon-showhide">
									Show/hide this section
								</span>
							</a>
						</header>
						<table class="plain patient-data">
							<thead>
								<tr>
									<th>Date</th>
									<th>Diagnosis</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2 Feb 2006</td>
									<td>Right Acquired anophthalmos</td>
									<td><a href="#">Remove</a></td>
								</tr>
								<tr>
									<td>2013</td>
									<td>Right Ectropion</td>
									<td><a href="#">Remove</a></td>
								</tr>
							</tbody>
						</table>

						<div class="box-actions">
							<button class="secondary small">
								Add Ophthalmic Diagnosis
							</button>
						</div>

						<form class="form add-data hide">
							<!-- -->
						</form>
					</section>

					<!-- Patient associated data box with open form -->
					<section class="box patient-info associated-data">
						<header class="box-header">
							<h3 class="box-title">
								<span class="icon-patient-clinician-hd_flag"></span>
								Other ophthalmic diagnoses
							</h3>
							<a href="#" class="toggle-trigger toggle-hide">
								<span class="icon-showhide">
									Show/hide this section
								</span>
							</a>
						</header>
						<table class="plain patient-data">
							<thead>
								<tr>
									<th>Date</th>
									<th>Diagnosis</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2 Feb 2006</td>
									<td>Right Acquired anophthalmos</td>
									<td><a href="#">Remove</a></td>
								</tr>
								<tr>
									<td>2013</td>
									<td>Right Ectropion</td>
									<td><a href="#">Remove</a></td>
								</tr>
							</tbody>
						</table>

						<div class="box-actions">
							<button class="secondary small" disabled>
								Add Ophthalmic Diagnosis
							</button>
						</div>

						<form class="form add-data">

							<fieldset class="field-row">

								<legend><strong>Add ophthalmic diagnosis</strong></legend>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Diagnosis:</label>
									</div>
									<div class="large-7 column end">
										<div class="field-row">
											<strong>Selected diagnosis</strong>
										</div>
										<div class="field-row">
											<select>
												<option>-- Select --</option>
											</select>
										</div>
										<div class="field-row">
											<input type="text" placeholder="Or type the first few characters of a diagnosis" />
										</div>
									</div>
								</div>

								<fieldset class="row field-row">
									<legend class="large-3 column">
										Eye:
									</legend>
									<div class="large-7 column end">
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
									</div>
								</fieldset>

								<fieldset class="row field-row">
									<legend class="large-3 column">
										Date:
									</legend>
									<div class="large-2 column">
										<select>
											<option>Day</option>
										</select>
									</div>
									<div class="large-2 column">
										<select>
											<option>Month</option>
										</select>
									</div>
									<div class="large-2 column end">
										<select>
											<option>2013</option>
										</select>
									</div>
								</fieldset>

								<div class="buttons">
									<button type="submit" class="secondary small">
										Save
									</button>
									<button class="warning small">
										Cancel
									</button>
								</div>
							</fieldset>
						</form>
					</section>

					<!-- Previous operations -->
					<section class="box patient-info associated-data">
						<header class="box-header">
							<h3 class="box-title">
								<span class="icon-patient-clinician-hd_flag"></span>
								Previous operations
							</h3>
							<a href="#" class="toggle-trigger toggle-hide">
								<span class="icon-showhide">
									Show/hide this section
								</span>
							</a>
						</header>
						<table class="plain patient-data">
							<thead>
								<tr>
									<th>Date</th>
									<th>Diagnosis</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2 Feb 2006</td>
									<td>Right Acquired anophthalmos</td>
									<td><a href="#">Remove</a></td>
								</tr>
								<tr>
									<td>2013</td>
									<td>Right Ectropion</td>
									<td><a href="#">Remove</a></td>
								</tr>
							</tbody>
						</table>

						<div class="box-actions">
							<button class="secondary small" disabled>
								Add Ophthalmic Diagnosis
							</button>
						</div>

						<form class="form add-data">

							<fieldset class="field-row">

								<legend><strong>Add Previous operation</strong></legend>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Common operations:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Operation:</label>
									</div>
									<div class="large-7 column end">
										<input type="text" />
									</div>
								</div>

								<fieldset class="row field-row">
									<legend class="large-3 column">
										Side:
									</legend>
									<div class="large-7 column end">
										<label class="inline">
											<input type="radio" />
											None
										</label>
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
									</div>
								</fieldset>

								<fieldset class="row field-row">
									<legend class="large-3 column">
										Date:
									</legend>
									<div class="large-2 column">
										<select>
											<option>Day</option>
										</select>
									</div>
									<div class="large-2 column">
										<select>
											<option>Month</option>
										</select>
									</div>
									<div class="large-2 column end">
										<select>
											<option>2013</option>
										</select>
									</div>
								</fieldset>

								<div class="buttons">
									<button type="submit" class="secondary small">
										Save
									</button>
									<button class="warning small">
										Cancel
									</button>
								</div>
							</fieldset>
						</form>
					</section>

					<!-- Medication -->
					<section class="box patient-info associated-data">
						<header class="box-header">
							<h3 class="box-title">
								<span class="icon-patient-clinician-hd_flag"></span>
								Medication
							</h3>
							<a href="#" class="toggle-trigger toggle-hide">
								<span class="icon-showhide">
									Show/hide this section
								</span>
							</a>
						</header>
						<table class="plain patient-data">
							<thead>
								<tr>
									<th>Date</th>
									<th>Diagnosis</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2 Feb 2006</td>
									<td>Right Acquired anophthalmos</td>
									<td><a href="#">Remove</a></td>
								</tr>
								<tr>
									<td>2013</td>
									<td>Right Ectropion</td>
									<td><a href="#">Remove</a></td>
								</tr>
							</tbody>
						</table>

						<div class="box-actions">
							<button class="secondary small" disabled>
								Add medication
							</button>
						</div>

						<form class="form add-data">

							<fieldset class="field-row">

								<legend><strong>Add medication</strong></legend>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Medication:</label>
									</div>
									<div class="large-7 column end">
										<div class="field-row">
											<select>
												<option>-- Select --</option>
											</select>
										</div>
										<div class="field-row">
											<input type="text" placeholder="or search formulary" />
										</div>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Route:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Option:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Frequency:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Date from:</label>
									</div>
									<div class="large-3 column end">
										<input type="text" />
									</div>
								</div>

								<div class="buttons">
									<button type="submit" class="secondary small">
										Save
									</button>
									<button class="warning small">
										Cancel
									</button>
								</div>
							</fieldset>
						</form>
					</section>

					<!-- CVI Status -->
					<section class="box patient-info associated-data">
						<header class="box-header">
							<h3 class="box-title">
								<span class="icon-patient-clinician-hd_flag"></span>
								CVI Status
							</h3>
							<a href="#" class="toggle-trigger toggle-hide">
								<span class="icon-showhide">
									Show/hide this section
								</span>
							</a>
						</header>
						<table class="plain patient-data">
							<thead>
								<tr>
									<th>Date</th>
									<th>Diagnosis</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2 Feb 2006</td>
									<td>Right Acquired anophthalmos</td>
									<td><a href="#">Remove</a></td>
								</tr>
								<tr>
									<td>2013</td>
									<td>Right Ectropion</td>
									<td><a href="#">Remove</a></td>
								</tr>
							</tbody>
						</table>

						<div class="box-actions">
							<button class="secondary small" disabled>
								Edit
							</button>
						</div>

						<form class="form add-data">

							<fieldset class="field-row">

								<legend><strong>Edit CVI Status</strong></legend>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Status:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<fieldset class="row field-row">
									<legend class="large-3 column">
										Date:
									</legend>
									<div class="large-2 column">
										<select>
											<option>Day</option>
										</select>
									</div>
									<div class="large-2 column">
										<select>
											<option>Month</option>
										</select>
									</div>
									<div class="large-2 column end">
										<select>
											<option>2013</option>
										</select>
									</div>
								</fieldset>

								<div class="buttons">
									<button type="submit" class="secondary small">
										Save
									</button>
									<button class="warning small">
										Cancel
									</button>
								</div>
							</fieldset>
						</form>
					</section>

					<!-- Allergies -->
					<section class="box patient-info associated-data">
						<header class="box-header">
							<h3 class="box-title">
								<span class="icon-patient-clinician-hd_flag"></span>
								CVI Status
							</h3>
							<a href="#" class="toggle-trigger toggle-hide">
								<span class="icon-showhide">
									Show/hide this section
								</span>
							</a>
						</header>
						<table class="plain patient-data">
							<thead>
								<tr>
									<th>Date</th>
									<th>Diagnosis</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2 Feb 2006</td>
									<td>Right Acquired anophthalmos</td>
									<td><a href="#">Remove</a></td>
								</tr>
								<tr>
									<td>2013</td>
									<td>Right Ectropion</td>
									<td><a href="#">Remove</a></td>
								</tr>
							</tbody>
						</table>

						<div class="box-actions">
							<button class="secondary small" disabled>
								Add allergy
							</button>
						</div>

						<form class="form add-data">

							<fieldset class="field-row">

								<legend><strong>Add allergy</strong></legend>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Allergy:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="buttons">
									<button type="submit" class="secondary small">
										Save
									</button>
									<button class="warning small">
										Cancel
									</button>
								</div>
							</fieldset>
						</form>
					</section>

					<!-- Family history -->
					<section class="box patient-info associated-data">
						<header class="box-header">
							<h3 class="box-title">
								<span class="icon-patient-clinician-hd_flag"></span>
								Family history
							</h3>
							<a href="#" class="toggle-trigger toggle-hide">
								<span class="icon-showhide">
									Show/hide this section
								</span>
							</a>
						</header>
						<table class="plain patient-data">
							<thead>
								<tr>
									<th>Date</th>
									<th>Diagnosis</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2 Feb 2006</td>
									<td>Right Acquired anophthalmos</td>
									<td><a href="#">Remove</a></td>
								</tr>
								<tr>
									<td>2013</td>
									<td>Right Ectropion</td>
									<td><a href="#">Remove</a></td>
								</tr>
							</tbody>
						</table>

						<div class="box-actions">
							<button class="secondary small" disabled>
								Add family history
							</button>
						</div>

						<form class="form add-data">

							<fieldset class="field-row">

								<legend><strong>Add allergy</strong></legend>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Relative:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Site:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Side:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Condition:</label>
									</div>
									<div class="large-7 column end">
										<select>
											<option>-- Select --</option>
										</select>
									</div>
								</div>

								<div class="field-row row">
									<div class="large-3 column">
										<label for="">Relative:</label>
									</div>
									<div class="large-9 column">
										<input type="text" />
									</div>
								</div>

								<div class="buttons">
									<button type="submit" class="secondary small">
										Save
									</button>
									<button class="warning small">
										Cancel
									</button>
								</div>
							</fieldset>
						</form>
					</section>
				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>