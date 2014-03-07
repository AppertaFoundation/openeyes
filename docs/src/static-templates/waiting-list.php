<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
<link rel="stylesheet" href="<?php echo $assets_root_path;?>assets/modules/OphTrOperationbooking/assets/css/module.css" />
</head>
<body class="open-eyes">

	<div class="alert-box watermark admin">
		You are logged in as admin. So this is OpenEyes Goldenrod Edition
	</div>

	<div class="container main" role="main">

		<? include 'components/header-logged-in-no-patient.php'; ?>

		<div class="container content">
			<h1 class="badge">Partial bookings waiting list</h1>

			<div class="box content">

				<div class="panel panel actions row">
					<div class="large-12 column">
						<div class="label">
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