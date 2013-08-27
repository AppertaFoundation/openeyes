<div class="large-10 column event container">
	<header class="event-header">
		<ul class="inline-list tabs event-actions">
			<li>
				<a href="#">View</a>
			</li>
			<li class="selected">
				<a href="#">Edit</a>
			</li>
		</ul>
		<div class="button-bar right">
			<a href="#" class="button secondary small">
				Save
			</a>
			<a href="#" class="button warning small">
				Cancel
			</a>
		</div>
	</header>
	<div class="box event content edit booking">
		<h2 class="event-title">Operation booking</h2>

		<div class="element">
			<h3 class="element-title">Diagnosis</h3>
			<fieldset class="element-fields">
				<div class="row">
					<div class="small-2 column">
						<label for="username">Eyes:</label>
					</div>
					<div class="small-10 column">
						<label class="inline highlight">
							<input type="radio" />
							Right
						</label>
						<label class="inline highlight">
							<input type="radio" />
							Both
						</label>
						<label class="inline highlight">
							<input type="radio" />
							Left
						</label>
					</div>
				</div>
				<div class="row">
					<div class="small-2 column">
						<label>Diagnosis:</label>
					</div>
					<div class="small-4 column end">
						<div class="panel element-field">Blepharospasm</div>
						<fieldset class="panel element-field">
							<h4>Change diagnosis:</h4>
							<select>
								<option>Select a commonly used diagnosis</option>
							</select>
							<input type="text" placeholder="or type the first few characters of a diagnosis" />
						</fieldset>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="element">
			<h3 class="element-title">Operation</h3>
			<fieldset class="element-fields">
				<div class="row">
					<div class="small-2 column">
						<label for="username">Eyes:</label>
					</div>
					<div class="small-10 column">
						<label class="inline highlight">
							<input type="radio" />
							Right
						</label>
						<label class="inline highlight">
							<input type="radio" />
							Both
						</label>
						<label class="inline highlight">
							<input type="radio" />
							Left
						</label>
					</div>
				</div>
				<div class="row">
					<div class="large-2 column">
						<label>
							Procedures:
						</label>
					</div>
					<div class="large-4 column">
						<fieldset class="panel element-field">
							<h4>Add a procedure:</h4>
							<select>
								<option>Select a subsection</option>
							</select>
							<input type="text" placeholder="or enter procedure here..." />
						</fieldset>
					</div>
					<div class="large-6 column">
						<div class="panel element-field">
							<h4>Procedures:</h4>
							<table class="plain">
								<thead>
									<tr>
										<td>Actions</td>
										<td>Procedure</td>
										<td>Duration</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><a href="#">(remove)</a></td>
										<td>Punctoplasty - 3 snp</td>
										<td>30 mins</td>
									</tr>
									<tr>
										<td><a href="#">(remove)</a></td>
										<td>Blepharoplasty of lower lid - Bleph lower lid</td>
										<td>40 mins</td>
									</tr>
								</tbody>
							</table>
							<table class="grid">
								<tfoot>
									<tr>
										<td>
											Calculated Total Duration:
										</td>
										<td>
											115 mins
										</td>
										<td>
											Estimated Total Duration:
										</td>
										<td>
											<input type="text" />
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="large-2 column">
						<label>Consultant required:</label>
					</div>
					<div class="large-4 column end">
						<label class="inline highlight">
							<input type="radio" />
							Yes
						</label>
						<label class="inline highlight">
							<input type="radio" />
							No
						</label>
					</div>
				</div>
				<div class="row">
					<div class="large-2 column">
						<label>Add comments:</label>
					</div>
					<div class="large-4 column end">
						<textarea></textarea>
					</div>
				</div>
				<div class="row">
					<div class="large-2 column">
						<label>
							Site:
						</label>
					</div>
					<div class="large-4 column end">
						<select>
							<option>City Road</option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="large-2 column">
						<label>Decision date:</label>
					</div>
					<div class="large-2 column end">
						<input type="text" />
					</div>
				</div>
			</fieldset>
		</div>
		<div class="element">
			<h3 class="element-title">Schedule operation</h3>
			<fieldset class="element-fields">
				<div class="row">
					<div class="large-2 column">
						<label>
							Schedule options
						</label>
					</div>
					<div class="large-4 column end">
						<label class="inline highlight">
							<input type="radio" />
							As soon as possible
						</label>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</div>