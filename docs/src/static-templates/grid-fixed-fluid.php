<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
<style type="text/css">
.container, .container.content {
	background: white !important;
	border: 0 !important;
	min-height: 0 !important;
}
.column {
	border: 1px solid red;
}
.example {
	min-height: 200px;
}
.row.test .fixed.column {
	width: 300px;
}
.row.test .fluid.column {
	width: 100%;
}
@media only screen and (min-width: 768px) {
	.row.test .fluid.column {
		width: calc(100% - 300px);
	}
}
</style>
</head>
<body class="open-eyes">
	<div class="container main" role="main">
		<div class="container content">
			<div class="row test">
				<div class="fixed column">
					<div class="example">
						Fixed column here
					</div>
				</div>
				<div class="fluid column">
					<div class="example">
						Fluid column here
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>