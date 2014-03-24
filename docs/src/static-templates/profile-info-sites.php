<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/admin/head.php'; ?>
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header-logged-in.php'; ?>

		<div class="container content">
			<h1 class="badge admin">Profile</h1>

			<div class="box content admin-content">
				<div class="row">
					<? include 'components/admin/profile-sidebar.php'; ?>
					<? include 'components/admin/profile-info-sites.php'; ?>
				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>