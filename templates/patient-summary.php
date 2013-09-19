<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body>
	<div class="container main" role="main">

		<? include 'components/header-logged-in.php'; ?>

		<div class="container content">
			<h1 class="badge">Patient summary</h1>

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
							<table class="plain patient-contacts">
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
						<div class="data-row">
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
						</div>
					</section>

					<!-- Patient associated data box -->
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
					</section>

				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>