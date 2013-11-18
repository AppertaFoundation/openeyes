<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>OpenEyes - Forms</title>
<meta name="viewport" content="width=device-width" />
<?php
$assets_root_path = '../';
include '../fragments/assets.php';
?>
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
			<h1 class="badge">Event forms</h1>

				<div class="row">
				<aside class="large-2 column sidebar">

					<div class="box generic">
						<h2>Navigation</h2>
						<ul class="side-nav">
							<li><a href="#overview">Overview</a></li>
							<li>
								<a href="#form-layouts">Form layouts</a>
								<ul>
									<li><a href="#vertical-layout-example">Vertical layout</a></li>
									<li><a href="#horizontal-layout-example">Horizontal layout</a></li>
									<li><a href="#table-layout-example">Table layout</a></li>
								</ul>
							</li>
							<li>
								<a href="#default-fields">Default form fields</a>
								<ul>
									<li><a href="#text-input-example">Text input</a></li>
									<li><a href="#password-input-example">Password input</a></li>
									<li><a href="#select-dropdown-example">Select dropdown</a></li>
									<li><a href="#radio-input-example">Radio input</a></li>
									<li><a href="#checkbox-input-example">Checkbox input</a></li>
									<li><a href="#textarea-example">Textarea</a></li>
								</ul>
							</li>
							<li><a href="#field-labels">Field labels</a></li>
							<li>
								<a href="#custom-fields">Custom fields</a>
								<ul>
									<li><a href="#multiselect-example">Multi-select</a></li>
									<li><a href="#highlight-example">Highlight</a></li>
									<li><a href="#select-search-example">Select search</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</aside>

				<div class="large-10 column">

					<div class="box generic">

						<div class="row">
							<div class="large-12 column">
								<h2 id="overview">Overview</h2>
								<p>This page lists all the possible form fields and layouts to
								be used in the OpenEyes application. Use this documentation as a
								guide to creating forms throughout the application.</p>

								<p>A lot of the terminology used in this page is taken from the
								<a href="http://www.w3.org/TR/html51/forms.html">latest
								html5 spec.</a></p>

								<p>Click on the <code>View Markup</code> links to view the HTML
								used in constructing the forms.</p>
							</div>
						</div>

						<hr />

						<!-- Form layouts -->
						<div class="row">
							<div class="large-12 column">
								<h2 id="form-layouts">Form layouts</h2>
								<p>Each field row is wrapped in a container, and labels are used to describe the fields. Fieldsets
								are used to wrap fields groups, like radio or checkbox groups.</p>
								<p>There are no set dimensions for the form components. This allows
								forms to be flexible and to adapt to fit different areas of the document without
								the need for creating additional CSS. If you want to create custom form layouts, you need to use the grid system. If you
								are unfamiliar with how the grid system works, please view the <a href="#">grid documentation</a>.</p>

								<h3>Basic form layouts</h3>

								<form class="example" id="vertical-layout-example">

									<header>
										<h3>Vertical layout</h3>
									</header>

									<div class="row">
										<div class="large-6 column show-markup">

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

											<!-- Select input -->
											<div class="field-row">
												<label for="select-1">
													Select input
												</label>
												<select id="select-1">
													<option>Please select...</option>
													<option>Option 1</option>
													<option>Option 2</option>
												</select>
											</div>

											<!-- Textarea input -->
											<div class="field-row">
												<label for="input-textarea-1">
													Textarea input
												</label>
												<textarea id="input-textarea-1"></textarea>
											</div>

											<!-- Small text input with postfix field info -->
											<div class="field-row">
												<label for="input-text-postfix-1">Small text input with postfix label</label>
												<div class="row collapse">
													<div class="large-6 column">
														<input type="text" id="input-text-postfix-1" />
													</div>
													<div class="large-6 column">
														<div class="postfix field-info align">
															Postfix label
														</div>
													</div>
												</div>
											</div>

											<!-- Radio group -->
											<div class="field-row">
												<fieldset>
													<legend>Radio group (inline)</legend>
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
													<legend>Checkbox group (inline)</legend>
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
												<button type="submit" class="small">
													Submit
												</button>
											</div>

										</div>
									</div>
								</form>

								<form class="example" id="horizontal-layout-example">

									<header>
										<h3>Horizontal layout</h3>
									</header>

									<div class="row">
										<div class="large-6 column show-markup">

											<!-- Text input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-text-2">
														Text input
													</label>
												</div>
												<div class="large-8 column">
													<input type="text" id="input-text-2" />
												</div>
											</div>

											<!-- Password input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-password-2">
														Password input
													</label>
												</div>
												<div class="large-8 column">
													<input type="password" id="input-password-2" />
												</div>
											</div>

											<!-- Select input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="select-2">
														Select input
													</label>
												</div>
												<div class="large-8 column">
													<select id="select-2">
														<option>Please select...</option>
														<option>Option 1</option>
														<option>Option 2</option>
													</select>
												</div>
											</div>

											<!-- Textarea input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-textarea-2">
														Textarea input
													</label>
												</div>
												<div class="large-8 column">
													<textarea id="input-textarea-2"></textarea>
												</div>
											</div>

											<!-- Small text input with postfix field-info -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-text-postfix-1">Small text input with postfix label</label>
												</div>
													<div class="large-8 column">
													<div class="row collapse">
														<div class="large-6 column">
															<input type="text" id="input-text-postfix-1" />
														</div>
														<div class="large-6 column">
															<div class="postfix field-info align">
																Postfix label
															</div>
														</div>
													</div>
												</div>
											</div>

											<!-- Radio group -->
											<div class="row field-row">
												<fieldset>
													<legend class="large-4 column">Radio group (inline)</legend>
													<div class="large-8 column">
														<label class="inline">
															<input type="radio" name="radio-input-2" />
															Option 1
														</label>
														<label class="inline">
															<input type="radio" name="radio-input-2" />
															Option 2
														</label>
													</div>
												</fieldset>
											</div>

											<!-- Checkbox group (inline) -->
											<div class="row field-row">
												<fieldset>
													<legend class="large-4 column">Checkbox group (inline)</legend>
													<div class="large-8 column">
														<label class="inline">
															<input type="checkbox" name="checkbox-input-3" />
															Option 1
														</label>
														<label class="inline">
															<input type="checkbox" name="checkbox-input-3" />
															Option 2
														</label>
													</div>
												</fieldset>
											</div>

											<!-- Checkbox group (block) -->
											<div class="row field-row">
												<fieldset>
													<legend class="large-4 column">Checkbox group (block)</legend>
													<div class="large-8 column">
														<label>
															<input type="checkbox" name="checkbox-input-4" />
															Option 1
														</label>
														<label>
															<input type="checkbox" name="checkbox-input-4" />
															Option 2
														</label>
													</div>
												</fieldset>
											</div>

											<!-- Submit button -->
											<div class="row field-row">
												<div class="large-8 large-offset-4 column">
													<button type="submit" class="small">
														Submit
													</button>
												</div>
											</div>
										</div>
									</div>
								</form>

								<form class="example" id="horizontal-layout-example">

									<header>
										<h3>Field widths</h3>
									</header>

									<p>Fields will fill the width of their containers. To make a field
									smaller, you simply need to make the container column smaller.
									Although we're working with a 12 column grid, it's not necessary
									to always ensure 12 columns exist. <strong>Note:</strong> Remember to add a class of 'end'
									to the last column, if the combined columns don't equal 12.</p>

									<div class="row">
										<div class="large-6 column show-markup">

											<!-- Large text input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-text-2">
														Large text input
													</label>
												</div>
												<div class="large-8 column">
													<input type="text" id="input-text-2" />
												</div>
											</div>

											<!-- Small text input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="input-text-2">
														Small text input
													</label>
												</div>
												<div class="large-4 column end">
													<input type="text" id="input-text-2" />
												</div>
											</div>

											<!-- Large select input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="select-2">
														Large select
													</label>
												</div>
												<div class="large-8 column">
													<select id="select-2">
														<option>Please select...</option>
														<option>Option 1</option>
														<option>Option 2</option>
													</select>
												</div>
											</div>

											<!-- Small select input -->
											<div class="row field-row">
												<div class="large-4 column">
													<label for="select-2">
														Small select
													</label>
												</div>
												<div class="large-4 column end">
													<select id="select-2">
														<option>Please select...</option>
														<option>Option 1</option>
														<option>Option 2</option>
													</select>
												</div>
											</div>


											<!-- Submit button -->
											<div class="row field-row">
												<div class="large-8 large-offset-4 column">
													<button type="submit" class="small">
														Submit
													</button>
												</div>
											</div>
										</div>
									</div>
								</form>


								<form class="example" id="table-form-layout-example">

									<header>
										<h3>Table layout</h3>
									</header>

									<div class="show-markup">

										<table class="plain">
											<thead>
												<tr>
													<th>Time</th>
													<th>Drug</th>
													<th>Drops</th>
													<th>Actions</th>
												</tr>
											</thead>
											<tbody class="plain">
												<tr>
													<td>
														<input class="input-time small" type="text" value="09:49">
													</td>
													<td>
														Cyclopentolate 0.5%
													</td>
													<td>
														<select>
															<option value="1" selected="selected">1</option>
														</select>
													</td>
													<td>
														<a href="#" class="removeTreatment">Remove</a>
													</td>
												</tr>
												<tr>
													<td>
														<input class="input-time small" type="text" value="10:24">
													</td>
													<td>
														<span class="drug-name">Phenylephrine 2.5%</span>
													</td>
													<td>
														<select>
															<option value="1">1</option>
														</select>
													</td>
													<td>
														<a href="#" class="removeTreatment">Remove</a>
													</td>
												</tr>
												<tr>
													<td>
														<input class="input-time small" type="text" value="10:24">
													</td>
													<td>
														<span class="drug-name">Tropicamide 0.5%</span>
													</td>
													<td>
														<select>
															<option value="1">1</option>
														</select>
													</td>
													<td>
														<a href="#" class="removeTreatment">Remove</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</form>
							</div>
						</div>

						<hr />

						<!-- Form fields -->
						<div class="row">
							<div class="large-12 column">

								<h2 id="default-fields">Default form fields</h2>
								<p>For the application to have consistent forms, it's important
								that developers understand the purpose of the different form fields and when
								to use them.</p>

								<!-- Text input -->
								<div class="example" id="text-input-example">
									<header>
										<h3>Text input</h3>
									</header>
									<p>A single-line text input control.</p>
									<h4>When to use:</h4>
									<p>When asking the user to input a small, single line of plain text.</p>
									<h4>Rules:</h4>
									<ul>
										<li>Text inputs should always have corresponding labels, even if they are hidden.</li>
										<li>Make use of the placeholder attribute for a more descriptive label.</li>
									</ul>
									<h4>Example:</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<label for="field-text-input-name">Your name:</label>
											<input type="text" placeholder="Please enter your name" id="field-text-input-name" />
										</div>
									</div>
								</div>

								<!-- Password input -->
								<div class="example" id="password-input-example">
									<header>
										<h3>Password input</h3>
									</header>
									<p>A single-line text input control that masks the input text.</p>
									<h4>When to use:</h4>
									<p>When asking the user to input a plain text password.</p>
									<h4>Rules:</h4>
									<ul>
										<li>Password inputs should always have corresponding labels, even if they are hidden.</li>
										<li>Make use of the placeholder attribute for a more descriptive label.</li>
									</ul>
									<h4>Example:</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<label for="field-text-input-password">Your name:</label>
											<input type="password" id="field-text-input-password" placeholder="Please enter your password" />
										</div>
									</div>
								</div>

								<!-- Select dropdown -->
								<div class="example" id="select-dropdown-example">
									<header>
										<h3>Select dropdown</h3>
									</header>
									<p>Provides a menu of choices.</p>
									<h4>When to use:</h4>
									<p>When asking the user to select exactly one option from a <em>large</em> list
											of options, where <em>not all</em> of the options are relevant to the user.</p>
									<h4>Rules:</h4>
									<ul>
										<li>Select dropdowns should always have corresponding labels, even if they are hidden.</li>
										<li>Make use of &lt;optgroup&gt; tags to group sets of options.</li>
									</ul>
									<h4>Example:</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<label for="field-select-1">
												Select your choice:
											</label>
											<select id="field-select-1">
												<option selected>Option 1</option>
												<option>Option 2</option>
											</select>
										</div>
									</div>
									<h4>Validation:</h4>
									<p>You can enforce a consious user selection by not making any of the
									options selected, and by adding an additional option at
									the top of the list with text "-- Select --" with a null value, for example:</p>
									<div class="field-row row">
										<div class="large-4 column end">
											<label for="field-select-2">
												Select your choice:
											</label>
											<select id="field-select-2">
												<option value="" selected>-- Select --</option>
												<option>Option 2</option>
											</select>
										</div>
									</div>
								</div>

								<!-- Radio input -->
								<div class="example" id="radio-input-example">
									<header>
										<h3>Radio input</h3>
									</header>
									<p>Provides a list of choices.</p>
									<h4>When to use:</h4>
									<p>When asking the user to select exactly one option from a <em>small</em> list
											of options, where <em>all</em> of the options are relevant to the user.</p>
									<h4>Rules:</h4>
									<ul>
										<li>Radio inputs should always have corresponding labels and the labels should never be hidden.</li>
										<li>Radio groups should always be wrapped in a fieldset with a descriptive legend.</li>
									</ul>
									<h4>Example:</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<fieldset>
												<legend>Your choices:</legend>
												<label>
													<input type="radio" name="input-radio-1" checked /> Choice 1
												</label>
												<label>
													<input type="radio" name="input-radio-1" /> Choice 2
												</label>
											</fieldset>
										</div>
									</div>
									<h4>Validation:</h4>
									<p>You can enforce a consious user selection by not making any of the
									radio inputs checked, for example:</p>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<fieldset>
												<legend>Your choices:</legend>
												<label>
													<input type="radio" name="input-radio-2" /> Choice 1
												</label>
												<label>
													<input type="radio" name="input-radio-2" /> Choice 2
												</label>
											</fieldset>
										</div>
									</div>
								</div>

								<!-- Checkbox input -->
								<div class="example" id="checkbox-input-example">
									<header>
										<h3>Checkbox input</h3>
									</header>
									<p>Provides a list of optional choices.</p>
									<h4>When to use:</h4>
									<p>When asking the user to select none, one or more options from a list
											of options.</p>
									<p>A stand-alone checkbox is used for a single option that the user can turn on or off.</p>
									<h4>Rules</h4>
									<ul>
										<li>Checkbox inputs should always have corresponding labels and the labels should never be hidden.</li>
										<li>Checkbox groups should always be wrapped in a fieldset with a descriptive legend.</li>
									</ul>
									<h4>Example:</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<fieldset>
												<legend>Your choices:</legend>
												<label>
													<input type="checkbox" name="input-checkbox-1" checked /> Choice 1
												</label>
												<label>
													<input type="checkbox" name="input-checkbox-1" /> Choice 2
												</label>
											</fieldset>
										</div>
									</div>
								</div>

								<!-- Textarea input -->
								<div class="example" id="textarea-example">
									<header>
										<h3>Textarea</h3>
									</header>
									<p>A multi-line text input control.</p>
									<h4>When to use:</h4>
									<p>When asking the user to input a large, possibly multi-line section of plain text.</p>
									<h4>Rules:</h4>
									<ul>
										<li>Textareas should always have corresponding labels, even if they are hidden.</li>
										<li>Make use of the placeholder attribute for a more descriptive label.</li>
										<li>Textareas should never be styled like a text input, the height of the textarea
										should always be at least 2 X line-height.</li>
									</ul>
									<h4>Example:</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<label for="field-textarea-address">Your address:</label>
											<textarea placeholder="Please enter your address" id="field-textarea-address"></textarea>
										</div>
									</div>
								</div>

							</div>
						</div>

						<!-- Field labels -->

						<div class="row">
							<div class="large-12 column">

								<h2 id="field-labels">Field labels</h2>
								<p>All form fields must have associated labels. There
								are situations where labels may be hidden:</p>
								<ul>
									<li>When a form contains only one field and a heading is used
									to describe the field. This is most common in event elements.</li>
								</ul>
							</div>
						</div>

						<hr />

						<!-- Custom fields -->
						<div class="row">
							<div class="large-12 column">

								<h2 id="custom-fields">Custom fields</h2>
								<p>Sometimes the default form fields do not provide the functionality
								or features required by the elements or forms throughout the application.</p>
								<p>It's important to be familiar with these custom fields so that
								they can re-used appropriately when required.</p>

								<div class="example" id="multiselect-example">

									<header>
										<h3>Multi-select</h3>
									</header>

									<p>A multi-select custom field provides the ability for the user
									to choose multiple options as well. Default multi-selects are generally cumbersome to use as they require
									the user to select batch options, and if the user makes a mistake then all options are
									deselected. This custom multi-select allows the user to select an option <em>
									one at a time.</em></p>
									<h4>When to use:</h4>
									<p>When asking the user to select mulitple options from a massive list of options.</p>
									<h4>Rules:</h4>
									<ul>
									</ul>
									<h4>Example 1 (default style):</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<div class="multi-select">
												<div class="field-row">
													<label for="multiselect-risks">
														Risks:
													</label>
													<select id="multiselect-risks">
														<option>-- Select --</option>
														<option>Pre-existing glaucoma</option>
														<option>Previous glaucoma surgery</option>
														<option>Allergy to povidone iodine</option>
														<option>Previous interocular surgery</option>
														<option>CVA</option>
														<option>MI</option>
													</select>
												</div>
												<ul class="multi-select-selections field-row">
													<li>
														Previous glaucoma surgery
														<a href="#" class="remove-one">Remove</a>
													</li>
													<li>
														Hyperopia
														<a href="#" class="remove-one">Remove</a>
													</li>
												</ul>
											</div>
										</div>
									</div>

									<h4>Example 2 (list style):</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">
											<div class="multi-select multi-select-list">
												<div class="field-row">
													<label for="multiselect-risks">
														Risks:
													</label>
													<select id="multiselect-risks">
														<option>-- Select --</option>
														<option>Pre-existing glaucoma</option>
														<option>Previous glaucoma surgery</option>
														<option>Allergy to povidone iodine</option>
														<option>Previous interocular surgery</option>
														<option>CVA</option>
														<option>MI</option>
													</select>
												</div>
												<ul class="multi-select-selections field-row">
													<li>
														Previous glaucoma surgery
														<a href="#" class="remove-one">Remove</a>
													</li>
													<li>
														Hyperopia
														<a href="#" class="remove-one">Remove</a>
													</li>
												</ul>
											</div>
										</div>
									</div>

								</div>

								<!-- Highlighted field -->

								<div class="example" id="highlight-example">

									<header>
										<h3>Highglight</h3>
									</header>

									<p>A highlighted field may be used to show validation errors or
									to show different levels of importanance.</p>
									<h4>Example:</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">

											<!-- High importance -->
											<div class="field-row">
												<label for="input-text-highlight">
													High importance
												</label>
												<div class="field-highlight high">
													<input type="text" id="input-text-highlight" />
												</div>
											</div>

											<!-- Moderate importance -->
											<div class="field-row">
												<label for="input-text-highlight">
													Moderate importance
												</label>
												<div class="field-highlight moderate">
													<input type="text" id="input-text-highlight" />
												</div>
											</div>

											<!-- Low importance -->
											<div class="field-row">
												<label for="select-1">
													Low importance
												</label>
												<div class="field-highlight low">
													<select id="select-1">
														<option>Please select...</option>
														<option>Option 1</option>
														<option>Option 2</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>

								<!-- Select search -->

								<div class="example" id="select-search-example">

									<header>
										<h3>Select search</h3>
									</header>

									<p>A select search component is a combination of a default
									select dropdown with an option to search for additional options.</p>
									<h4>When to use:</h4>
									<p>When displaying a reduced list of common and relevant options from a massive list in a select dropdown,
									and allowing the user to search for additional options within the massive list.</p>
									<h4>Example:</h4>
									<div class="field-row row">
										<div class="large-4 column end show-markup">

											<div class="field-row row collapse">
												<div class="large-10 column">
													<select>
														<option>-- Select --</option>
														<option>Idiopathic polypoidal choroidal vasculopathy with some very long text</option>
													</select>
												</div>
												<div class="large-2 column">
													<div class="postfix">
														<a href="#" class="button button-icon small">
															<span class="icon-button-small-search"></span>
															<span class="hide-offscreen">Search</span>
														</a>
													</div>
												</div>
											</div>
											<div class="field-row">
												<input type="text" placeholder="Search for diagnosis..." />
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
		<?php include '../fragments/footer.php'; ?>
	</div>
</body>
</html>