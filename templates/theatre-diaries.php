<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body>
	<div class="container" role="main">

		<? include 'components/header-logged-in-no-patient.php'; ?>

		<div class="content">
			<h1 class="badge">Theatre Diaries</h1>

			<div class="box-content">

				<div class="panel actions-panel row">
					<div class="large-12 column">
						<div class="actions-label">
							Use the filters below to view Theatre schedules:
						</div>
						<div class="button-bar">
							<? include 'components/theatre-diaries-button-bar.php'; ?>
						</div>
					</div>
				</div>

				<? include 'components/theatre-diaries-search-filters.php'; ?>

			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>