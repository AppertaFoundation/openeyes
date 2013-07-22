<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body>
	<div class="container" role="main">

		<? include 'components/header.php'; ?>

		<div class="content">
			<h1 class="badge">Please login</h1>
			<div class="row">
				<div class="medium-11 small-11 small-centered medium-centered columns">
					<form class="form login panel">
						<div data-alert class="alert-box radius alert">
							Invalid login.
							<a href="#" class="close">×</a>
						</div>
						<div class="row">
							<div class="small-4 columns">
								<label for="username" class="inline error">Username:</label>
							</div>
							<div class="small-8 columns">
								<input type="text" id="username" placeholder="Enter username...">
							</div>
						</div>
						<div class="row">
							<div class="small-4 columns">
								<label for="password" class="inline error">Password:</label>
							</div>
							<div class="small-8 columns">
								<input type="password" id="password" placeholder="Enter password...">
							</div>
						</div>
						<div class="row text-right">
							<div class="small-12 columns">
								<button type="submit">
									Login
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>