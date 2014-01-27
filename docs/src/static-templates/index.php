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
	.not-complete {
		color: red;
		white-space: nowrap;
	}
	.complete:before {
		content: "✓ ";
	}
	.not-complete:before {
		content: "✖ ";
	}
	table {
		background: #fff;
	}
	table tr td {
		border-top: 1px solid #ddd;
	}
	table tr:nth-of-type(even) {
		background: #fff;
	}
	tr.heading h3,
	tr.heading h4 {
		font-size: inherit;
		margin: 0;
		padding: 0;
		font-weight: bold;
	}
	tr.heading h4 {
		color: #666;
	}
</style>
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header.php'; ?>

		<div class="container content">
			<h1 class="badge">OpenEyes templates</h1>

			<div class="box content">

				<!-- Overview -->
				<div class="row">
					<div class="large-12 column">

						<br />
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
								<tr>
									<td>
										<a href="error.php">Error</a>
									</td>
									<td>The error template used for all application errors.</td>
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
								<tr>
									<td>
										<a href="theatre-diaries-searching.php">Theatre diaries searching</a>
									</td>
									<td>The theatre diaries template showing a searching message.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr>
									<td>
										<a href="theatre-diaries-no-results.php">Theatre diaries no results</a>
									</td>
									<td>The theatre diaries template showing a no-results message.</td>
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
									<td colspan="3"><h3>Audit logs</h3></td>
								</tr>
								<tr>
									<td>
										<a href="audit.php">Audit logs</a>
									</td>
									<td>The main audit logs template, showing the filters and logs with log details.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h3>Patient summary</h3></td>
								</tr>
								<tr>
									<td>
										<a href="patient-summary.php">Patient Summary</a>
									</td>
									<td>The patient summary template.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr>
									<td>
										<a href="patient-summary-add-episode-no-episodes.php">No episodes</a>
									</td>
									<td>The 'no episodes' page when trying to create a new episode from the patient summary.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr>
									<td>
										<a href="patient-summary-add-episode-no-episodes-with-dialog.php">No episodes (with dialog)</a>
									</td>
									<td>The 'no episodes' page when trying to create a new episode from the patient summary.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h3>Episodes</h3></td>
								</tr>
								<tr>
									<td>
										<a href="episode-view.php">Episode view</a>
									</td>
									<td>The episode detail view template.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr>
									<td>
										<a href="episode-create.php">Episode create</a>
									</td>
									<td>The episode create template.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h3>Event modules</h3></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h4>Examination</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h4>Operation booking</h4></td>
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
										<a href="operation-booking-transport.php">Operation booking transport</a>
									</td>
									<td>The operation booking transport template.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr>
									<td>
										<a href="operation-booking-schedule.php">Operation booking schedule</a>
									</td>
									<td>The operation booking schedule template.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h4>Consent</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h4>Anaesthetic satisfaction audit</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h4>Correspondence</h4></td>
								</tr>
								<tr>
									<td>
										<a href="correspondence-create.php">Correspondence create</a>
									</td>
									<td>The correspondence create template.</td>
									<td><span class="not-complete">Not complete</span></td>
								</tr>
								<tr>
									<td>
										<a href="correspondence-view.php">Correspondence view</a>
									</td>
									<td>The correspondence view template.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h4>Intravitreal injection</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h4>Laser</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h4>Operation note</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h4>Phasing</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h4>Prescription</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h4>Therapy application</h4></td>
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
								<tr class="heading">
									<td colspan="3"><h3>Bookings</h3></td>
								</tr>
								<tr>
									<td>
										<a href="schedule-operation.php">Schedule operation</a>
									</td>
									<td>
										The main schedule operation template for bookings which shows a calender.
									</td>
									<td><span class="not-complete">Not started</span></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h3>Profile templates</h3></td>
								</tr>
								<tr>
									<td>
										<a href="profile-info.php">Profile information</a>
									</td>
									<td>The user profile information template with a simple form.</td>
									<td><span class="complete">Complete</span></td>
								</tr>
								<tr>
									<td>
										<a href="profile-info-sites.php">Profile information - sites</a>
									</td>
									<td>The profile template which lists the sites you work at.</td>
									<td><span class="not-complete">Not started</span></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h3>Interaction components</h3></td>
								</tr>
								<tr>
									<td>
										<a href="interaction-components-alerts-tooltip.php">Alerts tooltip</a>
									</td>
									<td>Shows a custom tooltip when hovering on any of the alerts icons.</td>
									<td><span class="not-complete">Not started</span></td>
								</tr>
								<tr class="heading">
									<td colspan="3"><h3>Admin templates</h3></td>
								</tr>
								<tr>
									<td>
										<a href="admin-layout.php">Admin layout</a>
									</td>
									<td>The main admin layout used for all admin templates.</td>
									<td><span class="not-complete">Not started</span></td>
								</tr>
								<tr>
									<td>
										<a href="admin-users-list.php">Admin users</a>
									</td>
									<td>The admin users template that shows a list of users in a table.</td>
									<td><span class="not-complete">Not started</span></td>
								</tr>
								<tr>
									<td>
										<a href="admin-user-edit.php">Admin user edit</a>
									</td>
									<td>The admin user edit template which shows a basic edit form.</td>
									<td><span class="not-complete">Not started</span></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

			</div>
		</div>

		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>