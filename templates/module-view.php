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
						<aside class="large-2 column sidebar episodes">

							<h2 class="reader">Episodes and events</h2>

							<button class="secondary small" type="button">
								<span class="icon icon-button-small icon-button-small-plus-sign"></span>
								Add episode
							</button>

							<div class="panel episode">
								<h3>Ophthalmology</h3>
							</div>


						</aside>
						<div class="large-10 column">
							Right column
						</div>
					</div>

					<br/><br/><br/><br/><br/><br/><br/><br/>


				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>