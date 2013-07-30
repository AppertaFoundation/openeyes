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
					<div class="large-3 column">
						<div class="actions-label">
							Use the filters below to find patients:
						</div>
					</div>
					<div class="large-9 column text-right">
						<? include 'components/waiting-list-button-bar.php'; ?>
					</div>
				</div>

				<? include 'components/waiting-list-filters.php'; ?>
				<? include 'components/waiting-list-table.php'; ?>

			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>