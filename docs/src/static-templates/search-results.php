<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header-logged-in-no-patient.php'; ?>

		<div class="container content">

			<h1 class="badge">Search</h1>

			<div class="row">
				<div class="large-9 column">
					<div class="box generic">
						<p>
							<strong>50 patients found</strong>,
							based on FIRST NAME: <strong>"violet"</strong>
						</p>
					</div>
					<div class="box generic">
						<h2>Results. You are viewing patients 1 - 20 of 50</h2>
						<table class="grid">
							<thead>
								<tr>
									<th><a href="#">Hospital Number</a></th>
									<th><a href="#">Title</a></th>
									<th><a href="#">First name</a></th>
									<th><a href="#">Last name</a></th>
									<th><a href="#">Date of birth</a></th>
									<th><a href="#">Gender</a></th>
									<th><a href="#">NHS number</a></th>
								</tr>
							</thead>
							<tbody>
								<tr class="clickable">
									<td>1000005</td>
									<td>Mrs</td>
									<td>Violet</td>
									<td>Hodgkinson</td>
									<td>1958-01-10</td>
									<td>F</td>
									<td>853 986 5548</td>
								</tr>
								<tr class="clickable">
									<td>1000005</td>
									<td>Mrs</td>
									<td>Violet</td>
									<td>Hodgkinson</td>
									<td>1958-01-10</td>
									<td>F</td>
									<td>853 986 5548</td>
								</tr>
								<tr class="clickable">
									<td>1000005</td>
									<td>Mrs</td>
									<td>Violet</td>
									<td>Hodgkinson</td>
									<td>1958-01-10</td>
									<td>F</td>
									<td>853 986 5548</td>
								</tr>
								<tr class="clickable">
									<td>1000005</td>
									<td>Mrs</td>
									<td>Violet</td>
									<td>Hodgkinson</td>
									<td>1958-01-10</td>
									<td>F</td>
									<td>853 986 5548</td>
								</tr>
								<tr class="clickable">
									<td>1000005</td>
									<td>Mrs</td>
									<td>Violet</td>
									<td>Hodgkinson</td>
									<td>1958-01-10</td>
									<td>F</td>
									<td>853 986 5548</td>
								</tr>
								<tr class="clickable">
									<td>1000005</td>
									<td>Mrs</td>
									<td>Violet</td>
									<td>Hodgkinson</td>
									<td>1958-01-10</td>
									<td>F</td>
									<td>853 986 5548</td>
								</tr>
								<tr class="clickable">
									<td>1000005</td>
									<td>Mrs</td>
									<td>Violet</td>
									<td>Hodgkinson</td>
									<td>1958-01-10</td>
									<td>F</td>
									<td>853 986 5548</td>
								</tr>
							</tbody>
							<tfoot class="pagination-container">
								<tr>
									<td colspan="7">
										<ul class="pagination right">
											<li class="label">Viewing patients:</li>
											<li class="current"><a href="#">1-20</a></li>
											<li><a href="#">21-40</a></li>
											<li><a href="#">41-50</a></li>
										</ul>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="large-3 column">
					<div class="box generic">
						<p>
							<a href="#">Clear this search and <span class="highlight">start a new search</span>.</a>
						</p>
					</div>
				</div>
			</div>
		</div>

		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>