<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>OpenEyes - Grids</title>
<meta name="viewport" content="width=device-width" />
<?php
$assets_root_path = '../';
include '../fragments/assets.php';
?>
<style type="text/css">
.example .column {
	border: 1px solid #e3e3e3;
}
.example .row {
	margin-bottom: 10px;
}
</style>

</head>
<body>
	<div class="container main" role="main">

		<header class="header row">
			<div class="large-3 columns">
				<a class="logo" href="../index.php">
					OpenEyes
				</a>
			</div>
		</header>
		<div class="container content">

			<h1 class="badge">Grids</h1>

			<div class="row">

				<aside class="large-2 column sidebar">
					<div class="box generic">
						<h2>Navigation</h2>
						<ul class="side-nav">
							<li><a href="#overview">Overview</a></li>
							<li><a href="#usage">Grid usage</a></li>
						</ul>
					</div>
				</aside>

				<div class="large-10 column">
					<div class="box generic">
						<h2 id="overview">Overview</h2>
						<p>The layout of the OpenEyes application is based on a 12 column grid. The grid does not
						use set dimensions, instead the width of the columns are percentage based, and
						the overall width of the rows are based on the width of the container. This
						gives you the freedom of creating complex layouts without the need for creating
						additional CSS.</p>
						<hr />
						<h2 id="usage">Grid usage</h2>
						<p>Like a table, the grid system relies on columns and rows. Columns need to be wrapped
						in rows, for example:</p>
						<div class="example">
							<header>
								<h3>Nested example</h3>
							</header>
							<div class="row">
								<div class="column large-6">
									50% Column
								</div>
								<div class="column large-6">
									50% Column
								</div>
							</div>
						</div>
						<p>Grids can be nested infinitely, for example:</p>
						<div class="example">
							<header>
								<h3>2 column example</h3>
							</header>
							<div class="row">
								<div class="column large-6">
									50% Column
									<div class="row">
										<div class="column large-3">
											25% Column
										</div>
										<div class="column large-3">
											25% Column
										</div>
										<div class="column large-6">
											50% Column
										</div>
									</div>
								</div>
								<div class="column large-6">
									50% Column
								</div>
							</div>
						</div>
						<p>Please refer to the <a href="http://foundation.zurb.com/docs/components/grid.html">foundation documentation</a> for more information on how the
						grid system works.</p>
					</div>
				</div>
			</div>
		</div>
		<?php include '../fragments/footer.php'; ?>
	</div>
</body>
</html>