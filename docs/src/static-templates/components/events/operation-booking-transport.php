<div class="large-12 column">
	<h2>TCIs for today onwards.</h2>
	<div class="box generic transport">
		<form>
			<div class="row">
				<div class="large-6 column date-filter">
					<div class="field-row">
						<label class="inline align" for="">
							From:
						</label>
						<input type="text" class="inline fixed-width" />
						<label class="inline align" for="">
							To:
						</label>
						<input type="text" class="inline fixed-width" />
						<button class="small">
							Filter
						</button>
						<button class="small">
							View all
						</button>
					</div>
					<div class="field-row">
						<fieldset class="inline">
							<legend>Include:</legend>
							<label class="inline">
								<input type="checkbox" />
								Bookings
							</label>
							<label class="inline">
								<input type="checkbox" />
								Reschedules
							</label>
							<label class="inline">
								<input type="checkbox" />
								Cancellations
							</label>
						</fieldset>
					</div>
				</div>
				<div class="large-6 column text-right">
					<button>
						Confirm
					</button>
					<button>
						Print list
					</button>
					<button>
						Download CSV
					</button>
				</div>
			</div>
		</form>

		<table class="grid transport">
			<thead>
				<tr>
					<th>Hospital number</th>
					<th>Patient</th>
					<th>TCI date</th>
					<th>Admission time</th>
					<th>Site</th>
					<th>Ward</th>
					<th>Method</th>
					<th>Firm</th>
					<th>Subspeciality</th>
					<th>DTA</th>
					<th>Priority</th>
					<th><input type="checkbox" /></th>
				</tr>
			</thead>
			<tbody>
				<tr class="status Grey">
					<td>1000440</td>
					<td class="patient">
						<a href="#">
							<strong>KENDALL</strong>, Ellen
						</a>
					</td>
					<td>29-Oct-2013</td>
					<td>08:30:00</td>
					<td>St George's</td>
					<td>Duke Elder</td>
					<td>Booked</td>
					<td>ABOY</td>
					<td>AD</td>
					<td>25 Oct 2013</td>
					<td>Routine</td>
					<td><input type="checkbox" /></td>
				</tr>
				<tr class="status Green">
					<td>1000440</td>
					<td class="patient">
						<a href="#">
							<strong>KENDALL</strong>, Ellen
						</a>
					</td>
					<td>29-Oct-2013</td>
					<td>08:30:00</td>
					<td>St George's</td>
					<td>Duke Elder</td>
					<td>Booked</td>
					<td>ABOY</td>
					<td>AD</td>
					<td>25 Oct 2013</td>
					<td>Routine</td>
					<td><input type="checkbox" /></td>
				</tr>
				<tr class="status Red">
					<td>1000440</td>
					<td class="patient">
						<a href="#">
							<strong>KENDALL</strong>, Ellen
						</a>
					</td>
					<td>29-Oct-2013</td>
					<td>08:30:00</td>
					<td>St George's</td>
					<td>Duke Elder</td>
					<td>Booked</td>
					<td>ABOY</td>
					<td>AD</td>
					<td>25 Oct 2013</td>
					<td>Routine</td>
					<td><input type="checkbox" /></td>
				</tr>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="12">
						<ul class="pagination right" id="yw0">
							<li class="first unavailable"><a href="/admin/users">&lt;&lt; First</a></li>
							<li class="previous unavailable"><a href="/admin/users">&lt; Previous</a></li>
							<li class="page current"><a href="/admin/users">1</a></li>
							<li class="page"><a href="/admin/users?page=2">2</a></li>
							<li class="page"><a href="/admin/users?page=3">3</a></li>
							<li class="page"><a href="/admin/users?page=4">4</a></li>
							<li class="page"><a href="/admin/users?page=5">5</a></li>
							<li class="next"><a href="/admin/users?page=2">Next &gt;</a></li>
							<li class="last"><a href="/admin/users?page=5">Last &gt;&gt;</a></li>
						</ul>
					</td>
				</tr>
			</tfoot>
		</table>
		<div class="text-right">
			<button>
				Confirm
			</button>
			<button>
				Print list
			</button>
			<button>
				Download CSV
			</button>
		</div>
	</div>
</div>