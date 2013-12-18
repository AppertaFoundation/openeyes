<div class="large-10 column event ophtroperationbooking edit">
	<header class="event-header">
	</header>
	<div class="event-content">

		<h2 class="event-title">Schedule Operation</h2>

		<div class="alert-box alert with-icon hide">
			<p>Please fix the following input errors:</p>
			<ul>
				<li>&nbsp;</li>
			</ul>
		</div>

		<div class="panel">
			<span class="patient"><span class="patient-surname">COFFIN</span>, <span class="patient-name">Violet</span> (1009465)</span>
		</div>

		<div id="firmSelect">
			<div class="element-fields change-firm">
				<div class="field-row">
					<span class="field-label">
						Viewing the schedule for <strong>Abou-Rayyah Yassir</strong>
					</span>
					<select id="firm_id" class="inline firm-switcher">
						<option value="">Select a different firm</option>
						<option value="EMG">Emergency List</option>
					</select>
				</div>
			</div>
		</div>

		<div id="operation">

			<h3>Select theatre slot</h3>

			<h4>Select a session date:</h4>

			<div id="session_dates">
				<div id="details">
					<div id="dates" class="clearfix">
						<div id="current_month" class="column">November 2013</div>
						<div class="left" id="month_back">
							<div class="primary" id="previous_month">
								<a class="button primary" href="/OphTrOperationbooking/booking/schedule/156?firm_id=134&amp;date=201310">&#x25C0;&nbsp;&nbsp;previous month</a>
							</div>
						</div>
						<div class="right" id="month_forward">
							<div id="next_month">
								<a class="button primary" href="/OphTrOperationbooking/booking/schedule/156?firm_id=134&amp;date=201312"><span class="button-span button-span-blue">next month&nbsp;&nbsp;&#x25B6;</span></a>
							</div>
						</div>
					</div>
					<table id="calendar">
						<tbody>
							<tr>
								<th>Mon</th>
								<td class="available">
									4
								</td>
								<td class="available">
									11
								</td>
								<td class="available">
									18
								</td>
								<td class="available">
									25
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5">
									<div id="key">
										<span>Key:</span>
										<div class="container" id="day"><div class="color_box"></div><div class="label">Day of the week</div></div>
										<div class="container" id="available"><div class="color_box"></div><div class="label">Slots Available</div></div>
										<div class="container" id="limited"><div class="color_box"></div><div class="label">Limited Slots</div></div>
										<div class="container" id="full"><div class="color_box"></div><div class="label">Full</div></div>
										<div class="container" id="closed"><div class="color_box"></div><div class="label">Theatre Closed</div></div>
										<div class="container" id="selected_date"><div class="color_box"></div><div class="label">Selected Date</div></div>
										<div class="container" id="outside_rtt"><div class="color_box"></div><div class="label">Outside RTT</div></div>
									</div>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

			<div id="theatres">
			</div>
			<div id="sessionDetails">
			</div>
		</div>
	</div>
</div>
