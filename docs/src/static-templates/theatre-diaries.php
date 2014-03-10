<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
<link rel="stylesheet" href="<?php echo $assets_root_path;?>assets/modules/OphTrOperationbooking/assets/css/module.css" />
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header-logged-in-no-patient.php'; ?>

		<div class="container content">
			<h1 class="badge">Theatre Diaries</h1>

			<div class="box content">

				<div class="panel panel actions row">
					<div class="large-12 column">
						<div class="label">
							Use the filters below to view Theatre schedules:
						</div>
						<div class="button-bar">
							<? include 'components/theatre-diaries-button-bar.php'; ?>
						</div>
					</div>
				</div>

				<? include 'components/theatre-diaries-search-filters.php'; ?>
				<? include 'components/theatre-diaries-bookings.php'; ?>

			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>