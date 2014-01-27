<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en" >
<head>
<? include 'components/head.php'; ?>
<style type="text/css">
.example {
	position: relative;
	margin: 2em;
	min-height: 2em;
}
</style>
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header.php'; ?>

		<div class="container content">
			<h1 class="badge">OpenEyes templates</h1>

			<div class="box content">


				<div class="row">
					<div class="large-12 column">
						<h2>Alerts tooltip</h2>

						<div class="example">

							<div class="tooltip alerts" style="display:block">
								<img width="17" height="17" src="/assets/5d39527b/img/diaryIcons/booked_user.png">
								Created by: Enoch Root
								Last modified by: Enoch Root
							</div>

						</div>

						<div class="example">
							<div class="tooltip quicklook" style="display:block">
								<div class="event-name">Operation booking</div>
								<div class="event-info">Insertion of orbital implant</div>
								<div class="event-issue">Operation requires scheduling</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>