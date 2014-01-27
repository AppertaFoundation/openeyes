<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
<style type="text/css">
.container, .container.content {
	background: white !important;
	border: 0 !important;
}
.column {
	border: 1px solid red;
}
.example {
	min-height: 200px;
}
</style>
</head>
<body class="open-eyes">
	<div class="container main" role="main">
		<div class="container content">
			<div class="row">
				<div class="large-2 column">
					<div class="example">
						Sidebar here
					</div>
				</div>
				<div class="large-10 column">
					<div class="example">
						Content here
					</div>
					<div class="row">
						<div class="large-6 column">
							<div class="example">
								Content here
							</div>
							<div class="row">
								<div class="large-6 column">
									<div class="example">
										Content here
									</div>
								</div>
								<div class="large-6 column">
									<div class="example">
										Content here
									</div>
								</div>
							</div>
						</div>
						<div class="large-6 column">
							<div class="example">
								Content here
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>