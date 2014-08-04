<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>OpenEyes - Elements</title>
<meta name="viewport" content="width=device-width" />
<?php
$assets_root_path = '';
include 'fragments/assets.php';
?>
<style type="text/css">
.event {
	border: 0;
}
</style>
</head>
<body>
	<div class="container main" role="main">

		<header class="header row">
			<div class="large-3 columns">
				<a class="logo" href="index.php">
					OpenEyes
				</a>
			</div>
		</header>
		<div class="container content">
			<h1 class="badge">Event elements</h1>

			<div class="row">
				<aside class="large-2 column sidebar">
					<div class="box generic">
						<h2>Navigation</h2>
						<ul class="side-nav">
							<li><a href="#overview">Overview</a></li>
						</ul>
					</div>
				</aside>

				<div class="large-10 column example-box event edit">

					<div class="box generic navigation">

						<!-- Overview -->
						<div class="row">
							<div class="large-12 column">
								<h2 id="overview">Overview</h2>
								<p>Elements may have very different layouts. This page lists some of
								the more common element layouts.</p>
							</div>
						</div>

						<hr />

						<!-- Default layout -->
						<div class="row">
							<div class="large-12 column">
								<h2 id="form-layouts">Default layouts</h2>
								<p>An element is essentially a form with different form controls.</p>

								<!-- Default element -->
								<div class="example">

									<header>
										<h3>Example element</h3>
									</header>

									<div class="show-markup">
										<section class="element">
											<header class="element-header">
												<h3 class="element-title">Element title</h3>
												<div class="element-actions">
													<a href="#" class="button button-icon small">
														<span class="icon-button-small-mini-cross"></span>
														<span class="hide-offscreen">Remove element</span>
													</a>
												</div>
											</header>
											<div class="element-fields">
												<div class="row field-row">
													<div class="large-2 column">
														<label>Your choice:</label>
													</div>
													<div class="large-10 column">
														<select>
															<option>-- Select --</option>
														</select>
													</div>
												</div>
												<div class="row field-row">
													<div class="large-2 column">
														<label>Comments:</label>
													</div>
													<div class="large-10 column">
														<textarea placeholder="Enter comments here..."></textarea>
													</div>
												</div>
											</div>
										</section>
									</div>
								</div>

								<!-- Example element with disabled sub-element -->

								<div class="example">

									<header>
										<h3>Example element with disabled sub-elements</h3>
									</header>

									<div class="show-markup">
										<section class="element">
											<header class="element-header">
												<h3 class="element-title">Element title</h3>
												<div class="element-actions">
													<a href="#" class="button button-icon small">
														<span class="icon-button-small-mini-cross"></span>
														<span class="hide-offscreen">Remove element</span>
													</a>
												</div>
											</header>
											<div class="element-fields">
												<div class="row field-row">
													<div class="large-2 column">
														<label>Your choice:</label>
													</div>
													<div class="large-10 column">
														<select>
															<option>-- Select --</option>
														</select>
													</div>
												</div>
												<div class="row field-row">
													<div class="large-2 column">
														<label>Comments:</label>
													</div>
													<div class="large-10 column">
														<textarea placeholder="Enter comments here..."></textarea>
													</div>
												</div>
											</div>
											<div class="sub-elements inactive">
												<ul class="sub-elements-list">
													<li>
														<a href="#">Sub-element 1</a>
													</li>
													<li>
														<a href="#">Sub-element 2</a>
													</li>
												</ul>
											</div>
										</section>
									</div>
								</div>

								<!-- Example element with enabled sub-element/s -->

								<div class="example">

									<header>
										<h3>Example element with enabled sub-element/s</h3>
									</header>

									<div class="show-markup">
										<section class="element">
											<header class="element-header">
												<h3 class="element-title">Element title</h3>
												<div class="element-actions">
													<a href="#" class="button button-icon small">
														<span class="icon-button-small-mini-cross"></span>
														<span class="hide-offscreen">Remove element</span>
													</a>
												</div>
											</header>
											<div class="element-fields">
												<div class="row field-row">
													<div class="large-2 column">
														<label>Your choice:</label>
													</div>
													<div class="large-10 column">
														<select>
															<option>-- Select --</option>
														</select>
													</div>
												</div>
												<div class="row field-row">
													<div class="large-2 column">
														<label>Comments:</label>
													</div>
													<div class="large-10 column">
														<textarea placeholder="Enter comments here..."></textarea>
													</div>
												</div>
											</div>
											<div class="sub-elements">

												<!-- Cataract management sub-element -->
												<section class="sub-element">
													<header class="sub-element-header">
														<h4 class="sub-element-title">Sub-element title</h4>
														<div class="sub-element-actions">
															<a href="#" class="button button-icon small">
																<span class="icon-button-small-mini-cross"></span>
																<span class="hide-offscreen">Remove sub-element</span>
															</a>
														</div>
													</header>
													<div class="sub-element-fields">
														<div class="row field-row">
															<div class="large-3 column">
																<label>
																	Eye:
																</label>
															</div>
															<div class="large-9 column">
																<label class="inline highlight">
																	<input type="radio" />
																	First eye
																</label>
																<label class="inline highlight">
																	<input type="radio" />
																	Second eye
																</label>
															</div>
														</div>
														<div class="row field-row">
															<div class="large-3 column">
																<label>
																	Suitable for surgeon:
																</label>
															</div>
															<div class="large-9 column">
																<select>
																	<option value="">- Please select -</option>
																</select>
																<label class="inline">
																	<input type="checkbox" value="1" />	Supervised
																</label>
															</div>
														</div>
													</div>
												</section>
											</div>
										</section>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include '../fragments/footer.php'; ?>
	</div>
</body>
</html>