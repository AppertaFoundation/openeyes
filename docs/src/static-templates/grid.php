<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
<style type="text/css">
.row.example .column {
	background: #eee;
	outline: 1px solid #888;
}
.content-box {
	background: #fff;
	padding-top: 12px;
	padding-bottom: 12px;
	text-align: center;
	margin-top: 12px;
	margin-bottom: 12px;
}
.row.example {
	margin-bottom: 16px;
}
.col-width {
	display: block;
	font-size: 12px;
	color: #888;
}
</style>
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header.php'; ?>

		<div class="container content">
			<h1 class="badge">OpenEyes Grid</h1>

			<div class="box content">
				<div class="row">
					<div class="large-12 column">
						<h2>Overview</h2>
					</div>
				</div>
				<div class="box generic">
					<p>We use a grid to layout the interface. There's many advantages to using a grid, but mostly it allows for consistency and for responsive layouts. We use
					the zurb foundation grid which is extremely flexible</p>
					<ul>
						<li>
							The max-width of the main page container is set to 1230px
							<ul>
								<li><em>(The main page width is 1234px but that includes the 2px border on either side.)</em></li>
							</ul>
						</li>
						<li>The grid is 12 columns</li>
						<li>Column widths are percentages of the container width (12 columns is 100% width, 6 columns is 50% width)</li>
						<li>Gutters are set at 12px</li>
						<li>Each column will have a gutter width of 12px on either side, thus you'll have a 12px margin on either side of the grid, and
							a 24px gutter width between columns</li>
					</ul>
					<p><em><strong>Please note:</strong> Although we have tried to use the grid to layout everything, in some cases we had to customize the grid to fit in with existing designs (like the events sidebar).</em></p>
				</div>

				<div class="row">
					<div class="large-12 column">
						<h2>Example columns</h2>
					</div>
				</div>

				<!-- 12 columns -->
				<div class="row example">
					<div class="large-12 column">
						<div class="content-box">
							12 columns
						</div>
					</div>
				</div>

				<!-- 6 columns -->
				<div class="row example">
					<div class="large-6 column">
						<div class="content-box">
							6 columns
						</div>
					</div>
					<div class="large-6 column">
						<div class="content-box">
							6 columns
						</div>
					</div>
				</div>

				<!-- 3 columns -->
				<div class="row example">
					<div class="large-3 column">
						<div class="content-box">
							3 columns
						</div>
					</div>
					<div class="large-3 column">
						<div class="content-box">
							3 columns
						</div>
					</div>
					<div class="large-3 column">
						<div class="content-box">
							3 columns
						</div>
					</div>
					<div class="large-3 column">
						<div class="content-box">
							3 columns
						</div>
					</div>
				</div>

				<!-- 2 columns -->
				<div class="row example">
					<div class="large-2 column">
						<div class="content-box">
							2 columns
						</div>
					</div>
					<div class="large-2 column">
						<div class="content-box">
							2 columns
						</div>
					</div>
					<div class="large-2 column">
						<div class="content-box">
							2 columns
						</div>
					</div>
					<div class="large-2 column">
						<div class="content-box">
							2 columns
						</div>
					</div>
					<div class="large-2 column">
						<div class="content-box">
							2 columns
						</div>
					</div>
					<div class="large-2 column">
						<div class="content-box">
							2 columns
						</div>
					</div>
				</div>

				<!-- 1 column -->
				<div class="row example">
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
				</div>

				<div class="row">
					<div class="large-12 column">
						<h2>Different length columns</h2>
					</div>
				</div>

				<div class="row example">
					<div class="large-3 column">
						<div class="content-box">
							3 columns
						</div>
					</div>
					<div class="large-9 column">
						<div class="content-box">
							9 columns
						</div>
					</div>
				</div>

				<div class="row">
					<div class="large-12 column">
						<h2>Nested columns</h2>
					</div>
				</div>

				<div class="row example">
					<div class="large-5 column">
						<div class="content-box">
							5 columns
						</div>
					</div>
					<div class="large-7 column">
						<div class="content-box">
							7 columns
						</div>
						<div class="row">
							<div class="large-4 column">
								<div class="content-box">
									4 columns
								</div>
							</div>
							<div class="large-8 column">
								<div class="content-box">
									8 columns
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>

	<script>
		(function() {
			function addColumnWidth() {
				var box = $(this);
				var column = box.closest('.column');
				var width = column.innerWidth();
				var widthElement = $('<span />', { 'class': 'col-width' }).appendTo(box);
				widthElement.text(width + 'px');
			}
			$('.content-box').each(addColumnWidth);
		}());
	</script>
</body>
</html>