<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header-logged-in.php'; ?>

		<div class="container content">
			<h1 class="badge">Episodes &amp; Events</h1>

			<div class="row">
				<div class="large-8 large-centered column">
					<div class="box content">
						<div class="panel">
							<div class="alert-box alert with-icon">There are currently no episodes for this patient, please click the Add episode button to open a new episode.</div>
							<button class="small">
								Add episode
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>