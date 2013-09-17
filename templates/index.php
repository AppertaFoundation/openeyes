<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en" >
<head>
<? include 'components/head.php'; ?>
<style type="text/css">
.complete {
	color: green;
}
table {
	background: #e1eef9
}
table.grid tr td {
	border: 0;
	border-top: 1px solid #ddd;
}
table.grid tr th {
	border: 0;
}
table tr:nth-of-type(even) {
	background: #e1eef9
}
tr.heading, tr.heading:nth-of-type(even) {
	background: none;
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

								<table class="grid">
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
											<td>The main login page.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="search.php">Home search (logged in)</a>
											</td>
											<td>The main search page.</td>
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
											<td>The main theatre diaries page.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr class="heading">
											<td colspan="3"><h3>Waiting list</h3></td>
										</tr>
										<tr>
											<td>
												<a href="waiting-list.php">Waiting list</a>
											</td>
											<td>The main waiting list page.</td>
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
											<td>The operation booking edit page.</td>
											<td><span class="complete">Complete</span></td>
										</tr>
										<tr>
											<td>
												<a href="operation-booking-view.php">Operation booking view</a>
											</td>
											<td>The operation booking view page.</td>
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