<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header.php'; ?>

		<div class="container content">
			<h1 class="badge">Please login</h1>
			<div class="row">
				<div class="large-11 small-11 small-centered large-centered column">
					<form class="form panel login">
						<div data-alert class="alert-box alert">
							Invalid login.
							<a href="#" class="close">×</a>
						</div>
						<div class="row field-row">
							<div class="small-4 column">
								<label for="username" class="error align">Username:</label>
							</div>
							<div class="small-8 column">
								<input type="text" id="username" class="large" placeholder="Enter username...">
							</div>
						</div>
						<div class="row field-row">
							<div class="small-4 column">
								<label for="password" class="error align">Password:</label>
							</div>
							<div class="small-8 column">
								<input type="password" id="password" class="large" placeholder="Enter password...">
							</div>
						</div>
						<div class="row text-right">
							<div class="small-12 column">
								<button type="submit" class="button long">
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