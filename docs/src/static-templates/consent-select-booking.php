<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
<link rel="stylesheet" href="<?php echo $assets_root_path?>assets/modules/OphTrConsent/assets/css/module.css" />
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header-logged-in.php'; ?>

		<div class="container content">
			<h1 class="badge">Episodes and events</h1>

			<div class="box content">
				<div class="row">
					<? include 'components/events/sidebar.php'; ?>
					<? include 'components/events/consent-select-booking.php'; ?>
				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>