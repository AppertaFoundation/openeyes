<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>OpenEyes Front-end documentation</title>
<meta name="viewport" content="width=device-width" />
<?php
$assets_root_path = '';
include 'fragments/assets.php';
?>
</head>
<body>
	<div class="container main" role="main">

		<header class="header row">
			<div class="large-3 columns">
				<a class="logo" href="?">
					OpenEyes
				</a>
			</div>
		</header>
		<div class="container content">
			<h1 class="badge">OpenEyes Front-end Documentation</h1>

			<div class="row">

				<aside class="large-2 column sidebar">

					<div class="box generic">
						<h2>Navigation</h2>
						<ul class="side-nav">
							<li><a href="styleguide/index.html">Styleguide</a></li>
							<li><a href="jsdoc/index.html">Javascript API</a></li>
							<li><a href="static-templates/index.php">Site templates</a></li>
							<li><a href="components/forms.php">Forms</a></li>
							<li><a href="components/grids.php">Grids</a></li>
							<li><a href="components/elements.php">Elements</a></li>
						</ul>
					</div>
				</aside>

				<div class="large-10 column">

					<div class="box generic">
						<h2>Overview</h2>
						<p>Welcome to the OpenEyes front-end documentation.</p>
						<p>The documention should be used as a guide for developing the front-end
						of the OpenEyes application.</p>
						<p>Use the navigation on the left to select the area of documentation you would like to view.</p>
					</div>
				</div>
			</div>
		</div>
		<?php include 'fragments/footer.php'; ?>
	</div>
</body>
</html>