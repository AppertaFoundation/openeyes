<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body>
	<div class="container" role="main">

		<? include 'components/header-logged-in-no-patient.php'; ?>

		<div class="content">
			<h1 class="badge">Episodes and events</h1>

			<div class="box content">
				<div class="row">
					<? include 'components/booking-sidebar.php'; ?>
					<? include 'components/booking-edit.php'; ?>
				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>