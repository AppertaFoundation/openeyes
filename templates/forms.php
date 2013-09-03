<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
<link rel="stylesheet" href="/module_assets/examination/css/module_new.css" />
<style type="text/css">
.box.event {
	border: 0;
}
.form-example {
	padding: 10px;
	border: 1px solid #ddd;
}
.form-title {
	color: #888;
}
.example-box {
	background: #fafafa;
	margin: 0;
}
pre {
	font-size: .8em;
	display: none;
	margin-top: .5em;
}
</style>

<script src="/js/components/google-code-prettify/src/prettify.js"></script>
<link rel="stylesheet" href="/js/components/google-code-prettify/src/prettify.css" />

</head>
<body>
	<div class="container main" role="main">

		<? include 'components/header.php'; ?>

		<div class="container content">
			<h1 class="badge">Event forms</h1>

			<div class="box content">
				<div class="row">
					<aside class="large-2 column sidebar">
						<h2>Navigation</h2>
						<ul class="side-nav">
							<li><a href="#overview">Overview</a></li>
							<li><a href="#basic-forms">Basic forms</a></li>
							<li><a href="#form-layouts">Form layouts</a></li>
							<li><a href="#">Default fields</a></li>
							<li><a href="#">Custom fields</a></li>
							<li><a href="#">Field definitions</a></li>
						</ul>
					</aside>

					<div class="large-10 column example-box">

						<!-- Overview -->
						<div class="row">
							<div class="large-12 column">
								<h2 id="overview">Overview</h2>
								<p>This page lists all the possible form fields and layouts to
								be used in the OpenEyes application. Use this documentation as a
								guide to creating forms for event elements.</p>

								<p>A lot of the terminology used in this page is taken from the
								<a href="http://www.w3.org/TR/html51/forms.html">latest
								html5 spec.</a></p>
							</div>
						</div>

						<hr />

						<!-- Basic forms -->
						<div class="row">
							<div class="large-12 column">
								<h2 id="basic-forms">Basic forms</h2>
								<p>Below is an example of a basic form. Each field row is wrapped in a
								container, and labels are used to describe the fields. Fieldsets
								are used to wrap fields groups, like radio or checkbox groups.</p>

								<div class="row">
									<div class="column large-5 with-markup">

										<form class="form-example">

											<h3 class="form-title">Vertical layout</h3>

											<!-- Text input -->
											<div class="field-row">
												<label for="input-text-1">
													Text input
												</label>
												<input type="text" id="input-text-1" />
											</div>

											<!-- Password input -->
											<div class="field-row">
												<label for="input-password-1">
													Password input
												</label>
												<input type="password" id="input-password-1" />
											</div>

											<!-- Textarea input -->
											<div class="field-row">
												<label for="input-textarea-1">
													Textarea input
												</label>
												<textarea id="input-textarea-1"></textarea>
											</div>

											<!-- Radio group -->
											<div class="field-row">
												<fieldset>
													<legend>Radio group</legend>
													<label class="inline">
														<input type="radio" name="radio-input-1" />
														Option 1
													</label>
													<label class="inline">
														<input type="radio" name="radio-input-1" />
														Option 2
													</label>
												</fieldset>
											</div>

											<!-- Checkbox group -->
											<div class="field-row">
												<fieldset>
													<legend>Checkbox group</legend>
													<label class="inline">
														<input type="checkbox" name="checkbox-input-1" />
														Option 1
													</label>
													<label class="inline">
														<input type="checkbox" name="checkbox-input-1" />
														Option 2
													</label>
												</fieldset>
											</div>

											<!-- Submit button -->
											<div class="field-row">
												<button type="submit">
													Submit
												</button>
											</div>

										</form>
									</div>

									<div class="column large-7">
										<div class="form-example">
											<h3 class="form-title">
												Markup
											</h3>
										</div>
									</div>
								</div>

							</div>
						</div>

						<hr />

						<!-- Form layouts -->
						<div class="row">
							<div class="large-12 column">
								<h2 id="form-layouts">Form layouts</h2>
								<p>There are no set dimensions for the form components. This allows
								forms to be flexible and to adapt to fit different areas of the document without
								the need for creating additional CSS.</p>
								<p>If you want to create custom form layouts, you need to use the grid system. If you
								are unfamiliar with how the grid system works, please view the <a href="#">grid documentation</a>.</p>
								<p>Here is an example of a custom form layout that uses the grid system
								to create a horizontal form:</p>

								<div class="row">
									<div class="large-5 column with-markup">

										<form class="form-example">

											<h3 class="form-title">Horizontal layout</h3>

											<!-- Text input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-text-1">
														Text input
													</label>
												</div>
												<div class="large-8 column">
													<input type="text" id="input-text-1" />
												</div>
											</div>

											<!-- Password input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-password-1">
														Password input
													</label>
												</div>
												<div class="large-8 column">
													<input type="password" id="input-password-1" />
												</div>
											</div>

											<!-- Textarea input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-textarea-1">
														Textarea input
													</label>
												</div>
												<div class="large-8 column">
													<textarea id="input-textarea-1"></textarea>
												</div>
											</div>

											<!-- Radio group -->
											<div class="row field-row">
												<fieldset>
													<div class="large-4 column">
														<legend>Radio group</legend>
													</div>
													<div class="large-8 column">
														<label class="inline">
															<input type="radio" name="radio-input-1" />
															Option 1
														</label>
														<label class="inline">
															<input type="radio" name="radio-input-1" />
															Option 2
														</label>
													</div>
												</fieldset>
											</div>

											<!-- Checkbox group -->
											<div class="row field-row">
												<fieldset>
													<div class="large-4 column">
														<legend>Checkbox group</legend>
													</div>
													<div class="large-8 column">
														<label class="inline">
															<input type="checkbox" name="checkbox-input-1" />
															Option 1
														</label>
														<label class="inline">
															<input type="checkbox" name="checkbox-input-1" />
															Option 2
														</label>
													</div>
												</fieldset>
											</div>

											<!-- Submit button -->
											<div class="row field-row">
												<div class="large-8 large-offset-4 column">
													<button type="submit">
														Submit
													</button>
												</div>
											</div>
										</form>
									</div>

									<div class="column large-7">
										<div class="form-example">
											<h3 class="form-title">
												Markup
											</h3>
										</div>
									</div>
								</div>
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

		return;

		function htmlEntities(str) {
		    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		}

		$('.with-markup').each(function() {

			var markup = $(this).find('form').html();

			markup = markup.replace(/\t/gm, '    '); // tabs to 4 spaces
			markup = markup.replace(/^\n/gm, '');    // remove blank lines

			// Get the indentation level from the first line
			var indentation = markup.split('\n')[0].replace(/^([^>]+)<.*/, '$1').length

			markup = markup.replace(new RegExp('^\\s{0,'+indentation+'}', 'gm'), '');

			var pre = $(this).next().find('pre');

			pre.addClass('prettyprint lang-html').html(htmlEntities(markup));

			$(this).next().find('.show-markup').on('click', function() {
				pre.toggle();
			})
		});

    prettyPrint();

	}());
	</script>
</body>
</html>