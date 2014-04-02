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
.row.centered-example {
	margin-bottom: 0;
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
				<div class="box generic">
					<h2>Overview</h2>
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
					<p>
						<em><strong>Please note:</strong>
						Although we have tried to use the grid to layout everything, in some cases we had to customize the grid to fit in with existing designs (like the events sidebar), thus
						the traditional 12 column does not fit in to the event screens.</em></p>
				</div>

				<hr />

				<div class="box generic">
					<h2>Example columns</h2>
					<p>This demonstrates how split a 12 column grid into even column widths.</p>
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

				<hr />

				<div class="box generic">
					<h2>Different length columns</h2>
					<p>This demonstrates how split a 12 column grid into even variable columns widths.</p>
				</div>

				<div class="row">
					<div class="large-12 column">
					</div>
				</div>

				<div class="row example">
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
					<div class="large-11 column">
						<div class="content-box">
							11 columns
						</div>
					</div>
				</div>

				<div class="row example">
					<div class="large-2 column">
						<div class="content-box">
							2 columns
						</div>
					</div>
					<div class="large-10 column">
						<div class="content-box">
							10 columns
						</div>
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

				<div class="row example">
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
					</div>
				</div>

				<div class="row example">
					<div class="large-7 column">
						<div class="content-box">
							7 columns
						</div>
					</div>
					<div class="large-5 column">
						<div class="content-box">
							5 columns
						</div>
					</div>
				</div>

				<div class="row example">
					<div class="large-8 column">
						<div class="content-box">
							8 columns
						</div>
					</div>
					<div class="large-4 column">
						<div class="content-box">
							4 columns
						</div>
					</div>
				</div>

				<div class="row example">
					<div class="large-9 column">
						<div class="content-box">
							9 columns
						</div>
					</div>
					<div class="large-3 column">
						<div class="content-box">
							3 columns
						</div>
					</div>
				</div>

				<div class="row example">
					<div class="large-10 column">
						<div class="content-box">
							10 columns
						</div>
					</div>
					<div class="large-2 column">
						<div class="content-box">
							2 columns
						</div>
					</div>
				</div>

				<div class="row example">
					<div class="large-11 column">
						<div class="content-box">
							11 columns
						</div>
					</div>
					<div class="large-1 column">
						<div class="content-box">
							1 column
						</div>
					</div>
				</div>

				<hr />

				<div class="box generic">
					<h2>Nested columns</h2>
					<p>Columns can be nested infinitely. Remember, column widths are set as a percentage of
					the container width.</p>
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

				<hr />

				<div class="box generic">
					<h2>Centered columns</h2>
				</div>

				<div class="row example centered-example">
					<div class="large-1 large-centered column">
						<div class="content-box">
							1 column centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-2 large-centered column">
						<div class="content-box">
							2 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-3 large-centered column">
						<div class="content-box">
							3 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-4 large-centered column">
						<div class="content-box">
							4 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-5 large-centered column">
						<div class="content-box">
							5 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-6 large-centered column">
						<div class="content-box">
							6 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-7 large-centered column">
						<div class="content-box">
							7 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-8 large-centered column">
						<div class="content-box">
							8 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-9 large-centered column">
						<div class="content-box">
							9 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-10 large-centered column">
						<div class="content-box">
							10 columns centered
						</div>
					</div>
				</div>
				<div class="row example centered-example">
					<div class="large-11 large-centered column">
						<div class="content-box">
							11 columns centered
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