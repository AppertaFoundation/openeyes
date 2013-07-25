<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
</head>
<body>
	<div class="container" role="main">

		<? include 'components/header-logged-in-no-patient.php'; ?>

		<div class="content">
			<h1 class="badge">Partial bookings waiting list</h1>

			<div class="box-content">
				<div class="panel filters-panel row">
					<div class="large-3 column">
						<div class="filters-label">
							Use the filters below to find patients:
						</div>
					</div>
					<div class="large-9 column text-right">
						<button id="btn_print_all" class="small">Print all</button>
						<button id="btn_print" class="small">Print selected</button>
						<div class="panel panel-orange">
								<label for="adminconfirmdate">Set latest letter sent to be:</label>
								<input class="small" type="text" style="width:110px" />
						</div>
						<div class="panel panel-orange">
							<select name="adminconfirmto" id="adminconfirmto">
								<option value="OFF">Off</option>
								<option value="noletters">No letters sent</option>
								<option value="0">Invitation letter</option>
								<option value="1">1st reminder letter</option>
								<option value="2">2nd reminder letter</option>
								<option value="3">GP letter</option>
							</select>
						</div>
						<button class="small secondary">
							Confirm selected
						</button>
					</div>
				</div>

				<div class="row">
					<div class="large-12 column">
						<h2>Search partial bookings waiting lists by:</h2>
					</div>
					<div class="large-12 column">
						<table class="grid">
							<tbody>
								<tr>
									<th>Service:</th>
									<th>Firm:</th>
									<th>Next letter due:</th>
									<th>Site:</th>
									<th>Hospital no:</th>
									<th>&nbsp;</th>
								</tr>
								<tr>
									<td>
										<select>
											<option>All specialties</option>
										</select>
									</td>
									<td>
										<select>
											<option>All firms</option>
										</select>
									</td>
									<td>
										<select>
											<option>Any</option>
										</select>
									</td>
									<td>
										<select>
											<option>All sites</option>
										</select>
									</td>
									<td>
										<input type="text" />
									</td>
									<td class="text-right">
										<button class="secondary">
											Search
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="large-12 column">

						<h2>Search Results:</h2>

						<table class="grid waiting-list">
							<tbody>
								<tr>
									<th>
										Letters sent
									</th>
									<th>
										Patient
									</th>
									<th>
										Hospital number
									</th>
									<th>
										Location
									</th>
									<th>
										Procedure
									</th>
									<th>
										Eye
									</th>
									<th>
										Firm
									</th>
									<th>
										Decision date
									</th>
									<th>
										Priority
									</th>
									<th>
										Book status (requires...)
									</th>
									<th>
										<label>
											<input type="checkbox" id="checkall" >
											All
										</label>
									</th>
								</tr>
								<tr>
									<td class="letter-status">
										<img src="/assets/2f6770e4/img/letterIcons/invitation.png" alt="Invitation" width="17" height="17">
										<img src="/assets/2f6770e4/img/letterIcons/letter1.png" alt="1st reminder" width="17" height="17">
										<img src="/assets/2f6770e4/img/letterIcons/letter2.png" alt="2nd reminder" width="17" height="17">
									</td>
									<td class="patient">
										<a href="/OphTrOperationbooking/default/view/67">
											<strong>COTTRELL</strong>, Kristen
										</a>
									</td>
									<td>
										1001982
									</td>
									<td>
										City Road
									</td>
									<td>
										Cyclodiode
									</td>
									<td>
										Left
									</td>
									<td>
										Garway-Heath David (Glaucoma)
									</td>
									<td>
										25 Dec 2011
									</td>
									<td>
										Routine
									</td>
									<td>
										Scheduling
									</td>
									<td cass="admin-td">
										<div>
											<input type="checkbox" id="operation67" value="1">
										</div>
									</td>
								</tr>
								<tr>
									<td class="letter-status send-another-reminder">
										<img src="/assets/2f6770e4/img/letterIcons/invitation.png" alt="Invitation" width="17" height="17">
										<img src="/assets/2f6770e4/img/letterIcons/letter1.png" alt="1st reminder" width="17" height="17">
									</td>
									<td class="patient">
										<a href="/OphTrOperationbooking/default/view/64">
											<strong>GALTON</strong>, Andy
										</a>
									</td>
									<td>
										1001985
									</td>
									<td>
										City Road
									</td>
									<td>
										EUA
									</td>
									<td>
										Left
									</td>
									<td>
										Garway-Heath David (Glaucoma)
									</td>
									<td>
										1 Jan 2012
									</td>
									<td>
										Routine
									</td>
									<td>
										Scheduling
									</td>
									<td>
										<div>
											<input type="checkbox" id="operation64" value="1">
										</div>
									</td>
								</tr>
								<tr>
									<td class="letter-status send-another-reminder">
										<img src="/assets/2f6770e4/img/letterIcons/invitation.png" alt="Invitation" width="17" height="17">
										<img src="/assets/2f6770e4/img/letterIcons/letter1.png" alt="1st reminder" width="17" height="17">
									</td>
									<td class="patient">
										<a href="/OphTrOperationbooking/default/view/100">
											<strong>BLAKE</strong>, Alexander
										</a>
									</td>
									<td>
										1001002
									</td>
									<td>
										City Road
									</td>
									<td>
										DonorSclera
									</td>
									<td>
										Left
									</td>
									<td>
										Wormald Richard (Glaucoma)
									</td>
									<td>
										2 Jan 2012
									</td>
									<td>
										Routine
									</td>
									<td>
										Scheduling
									</td>
									<td>
										<div>
											<input type="checkbox" id="operation100" value="1">
										</div>
									</td>
								</tr>
								<tr>
									<td class="letter-status send-another-reminder">
										<img src="/assets/2f6770e4/img/letterIcons/invitation.png" alt="Invitation" width="17" height="17">
										<img src="/assets/2f6770e4/img/letterIcons/letter1.png" alt="1st reminder" width="17" height="17">
									</td>
									<td class="patient">
										<a href="/OphTrOperationbooking/default/view/66">
											<strong>LAWRENCE</strong>, Earl
										</a>
									</td>
									<td>
										1001983
									</td>
									<td>
										City Road
									</td>
									<td>
										Move IOL, Iridoplasty, PI
									</td>
									<td>
										Right
									</td>
									<td>
										Garway-Heath David (Glaucoma)
									</td>
									<td>
										3 Jan 2012
									</td>
									<td>
										Routine
									</td>
									<td>
										Scheduling
									</td>
									<td>
										<div>
											<input type="checkbox" id="operation66" value="1">
										</div>
									</td>
								</tr>
								<tr>
									<td class="letter-status send-another-reminder">
										<img src="/assets/2f6770e4/img/letterIcons/invitation.png" alt="Invitation" width="17" height="17">
										<img src="/assets/2f6770e4/img/letterIcons/letter1.png" alt="1st reminder" width="17" height="17">
									</td>
									<td class="patient">
										<a href="/OphTrOperationbooking/default/view/68">
											<strong>BOATWRIGHT</strong>, Peter
										</a>
									</td>
									<td>
										1001981
									</td>
									<td>
										City Road
									</td>
									<td>
										Cyclodiode
									</td>
									<td>
										Left
									</td>
									<td>
										Garway-Heath David (Glaucoma)
									</td>
									<td>
										6 Jan 2012
									</td>
									<td>
										Routine
									</td>
									<td>
										Scheduling
									</td>
									<td>
										<div>
											<input type="checkbox" id="operation68" value="1">
										</div>
									</td>
								</tr>
								<tr>
									<td class="letter-status send-another-reminder">
										<img src="/assets/2f6770e4/img/letterIcons/invitation.png" alt="Invitation" width="17" height="17">
										<img src="/assets/2f6770e4/img/letterIcons/letter1.png" alt="1st reminder" width="17" height="17">
									</td>
									<td class="patient">
										<a href="/OphTrOperationbooking/default/view/65">
											<strong>GISSING</strong>, Edwin
										</a>
									</td>
									<td>
										1001984
									</td>
									<td>
										City Road
									</td>
									<td>
										NeedlingBleb
									</td>
									<td>
										Left
									</td>
									<td>
										Garway-Heath David (Glaucoma)
									</td>
									<td>
										10 Jan 2012
									</td>
									<td>
										Routine
									</td>
									<td>
										Scheduling
									</td>
									<td>
										<div>
											<input type="checkbox" id="operation65" value="1">
										</div>
									</td>
								</tr>
								<tr>
									<td class="letter-status send-invitation-letter">
									</td>
									<td class="patient">
										<a href="/OphTrOperationbooking/default/view/134">
											<strong>COLEGROVE</strong>, Paula
										</a>
									</td>
									<td>
										1001003
									</td>
									<td>
										City Road
									</td>
									<td>
										Goniotomy
									</td>
									<td>
										Left
									</td>
									<td>
										Wormald Richard (Glaucoma)
									</td>
									<td>
										14 Jan 2012
									</td>
									<td>
										Routine
									</td>
									<td>
										Scheduling
									</td>
									<td>
										<div>
											<input type="checkbox" id="operation134" value="1">
										</div>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="11">
										<div id="key">
											<span>
												Colour Key:
											</span>
											<div class="container" id="sendflag-invitation">
												<div class="color_box">
												</div>
												<div class="label">
													Send invitation letter
												</div>
											</div>
											<div class="container" id="sendflag-reminder">
												<div class="color_box">
												</div>
												<div class="label">
													Send another reminder (2 weeks)
												</div>
											</div>
											<div class="container" id="sendflag-GPremoval">
												<div class="color_box">
												</div>
												<div class="label">
													Send GP removal letter
												</div>
											</div>
											<div class="container" id="sendflag-remove">
												<div class="color_box">
												</div>
												<div class="label">
													Patient is due to be removed
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="11" class="small">
										<div id="letters-key">
											<span>
												Letters sent out:
											</span>
											&nbsp;&nbsp;
											<img src="/assets/2f6770e4/img/letterIcons/invitation.png" alt="Invitation" height="17" width="17">
											- Invitation
											<img src="/assets/2f6770e4/img/letterIcons/letter1.png" alt="1st reminder" height="17" width="17">
											- 1
											<sup>
												st
											</sup>
											Reminder
											<img src="/assets/2f6770e4/img/letterIcons/letter2.png" alt="2nd reminder" height="17" width="17">
											- 2
											<sup>
												nd
											</sup>
											Reminder
											<img src="/assets/2f6770e4/img/letterIcons/GP.png" alt="GP" height="17" width="17">
											- GP Removal
										</div>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
	</div>
</body>
</html>