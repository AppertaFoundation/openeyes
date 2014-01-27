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
			<h1 class="badge">Audit logs</h1>

			<div class="box content">
				<? include 'components/audit-filters.php'; ?>
				<? include 'components/audit-logs.php'; ?>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>