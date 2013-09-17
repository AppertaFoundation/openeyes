<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en" >
<head>
<? include 'components/head.php'; ?>
<style type="text/css">
	.complete {
		color: green;
		white-space: nowrap;
	}
	.complete:before {
		content: "✓ ";
	}
	table {
		background: #e1eef9
	}
	table tr td {
		border-top: 1px solid #ddd;
	}
	table tr:nth-of-type(even) {
		background: #e1eef9
	}
	tr.heading h3 {
		font-size: inherit;
		margin: 0;
		padding: 0;
		font-weight: bold;
	}
</style>
</head>
<body>
	<div class="container main" role="main">

		<? include 'components/header.php'; ?>

		<div class="container content">
			<h1 class="badge">OpenEyes templates</h1>

			<div class="box content">
				<div class="row">
					<aside class="large-12 column">

						<!-- Overview -->
						<div class="row">
							<div class="large-12 column">
								<h2>Overview</h2>
								<p>Here you will find templates containing the markup used for
								the OpenEyes application.</p>
								<h2>Templates</h2>

								<p>Select the template you'd like to view:</p>

								<table class="">
									<thead>
										<tr>
											<th>Template</th>
											<th>Description</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<tr class="heading">
											<td colspan="3"><h3>Home templates</h3></td>
										</tr>
										<tr>
											<td>
												<a href="login.php">Home login</a>
											</td>
											<td>The main login template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="login-validation.php">Home login validation</a>
											</td>
											<td>The main login template with validation errors.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="search.php">Home search (logged in)</a>
											</td>
											<td>The main search template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr class="heading">
											<td colspan="3"><h3>General templates</h3></td>
										</tr>
										<tr>
											<td>
												<a href="logged-in.php">Logged in</a>
											</td>
											<td>Contains the patient summary panel, user panel and navigation which is shown once a user is logged in.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr class="heading">
											<td colspan="3"><h3>Theatre diaries</h3></td>
										</tr>
										<tr>
											<td>
												<a href="theatre-diaries.php">Theatre diaries</a>
											</td>
											<td>The main theatre diaries template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr class="heading">
											<td colspan="3"><h3>Waiting list</h3></td>
										</tr>
										<tr>
											<td>
												<a href="waiting-list.php">Waiting list</a>
											</td>
											<td>The main waiting list template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr class="heading">
											<td colspan="3"><h3>Event modules</h3></td>
										</tr>
										<tr>
											<td>
												<a href="examination-create.php">Examination create</a>
											</td>
											<td>The examination create template - contains most of the element, sub-element, and optional element variations, as well as split-element and validation errors.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="examination-view.php">Examination view</a>
											</td>
											<td>The examination view template - a good template to refer to when displaying sub-elements.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="operation-booking-edit.php">Operation booking edit</a>
											</td>
											<td>The operation booking edit template - shows an example of adding and listing procedures.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="operation-booking-view.php">Operation booking view</a>
											</td>
											<td>The operation booking view template - shows an example of highlighting element fields.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="consent-select-booking.php">Consent select booking</a>
											</td>
											<td>The consent select booking template - shows an example of selecting a booking prior to creating.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="consent-create.php">Consent create</a>
											</td>
											<td>The consent create template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="consent-view.php">Consent view</a>
											</td>
											<td>The consent view template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="anaesthetic-satisfaction-audit-create.php">Anaesthetic satisfaction audit create</a>
											</td>
											<td>The anaesthetic satisfaction audit create template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="anaesthetic-satisfaction-audit-view.php">Anaesthetic satisfaction audit view</a>
											</td>
											<td>The anaesthetic satisfaction audit view template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="correspondence-create.php">Correspondence create</a>
											</td>
											<td>The correspondence create template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="correspondence-view.php">Correspondence view</a>
											</td>
											<td>The correspondence view template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="intravitreal-injection-create.php">Intravitreal injection create</a>
											</td>
											<td>The intravitreal injection create template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="intravitreal-injection-view.php">Intravitreal injection view</a>
											</td>
											<td>The intravitreal injection view template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="laser-create.php">Laser create</a>
											</td>
											<td>The laser create template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="laser-view.php">Laser view</a>
											</td>
											<td>The laser view template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="operation-note-select-booking.php">Operation note select booking</a>
											</td>
											<td>The operation note select booking template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="operation-note-create.php">Operation note create</a>
											</td>
											<td>The operation note create template - shows an example of displaying "on-demand" elements.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="operation-note-view.php">Operation note view</a>
											</td>
											<td>The operation note view template - shows an example of highlighting element fields.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="phasing-create.php">Phasing create</a>
											</td>
											<td>The phasing create template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="phasing-view.php">Phasing view</a>
											</td>
											<td>The phasing view template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="prescription-create.php">Prescription create</a>
											</td>
											<td>The prescription create template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="prescription-view.php">Prescription view</a>
											</td>
											<td>The prescription view template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="therapy-application-create.php">Therapy application create</a>
											</td>
											<td>The therapy application create template - shows an example of sub-form panels.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="therapy-application-view.php">Therapy application view</a>
											</td>
											<td>The therapy application view template.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>