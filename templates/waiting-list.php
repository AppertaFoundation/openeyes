<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body>
	<div class="container" role="main">

		<? include 'components/header-logged-in-no-patient.php'; ?>

		<div class="content">
			<h1 class="badge">Partial bookings waiting list</h1>

			<div class="box-content">

				<div class="panel actions-panel row">
					<div class="large-12 column">
						<div class="actions-label">
							Use the filters below to find patients:
						</div>
						<div class="button-bar">
							<? include 'components/waiting-list-button-bar.php'; ?>
						</div>
					</div>
				</div>

				<? include 'components/waiting-list-search-filters.php'; ?>
				<? include 'components/waiting-list-table.php'; ?>

			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>