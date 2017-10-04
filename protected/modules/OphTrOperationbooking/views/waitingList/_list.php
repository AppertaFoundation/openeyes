<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<h2>Search Results:</h2>

<div class="panel">

	<div id="pas_warnings" class="alert-box alert with-icon hide">
		<ul>
			<li>One or more patients has no GP practice address, please correct in PAS before printing GP letter.</li>
			<li>One or more patients has no Address, please correct in PAS before printing a letter for them.</li>
		</ul>
		<a href="#" class="close">×</a>
	</div>

	<table class="grid waiting-list">
		<thead>
		<tr>
			<th>Letters sent</th>
			<th>Patient</th>
			<th>Hospital number</th>
			<th>Location</th>
			<th>Procedure</th>
			<th>Eye</th>
			<th>Firm</th>
			<th>Decision date</th>
			<th>Priority</th>
			<th>Book status (requires...)</th>
			<th>
				<label>
					<input type="checkbox" id="checkall" value="" /> All
				</label>
			</th>
            <?php if($this->module->isTheatreDiaryDisabled()): ?>
            <th></th>
            <?php endif; ?>
		</tr>
		</thead>
		<tbody>
		<?php if (empty($operations)) {?>
			<tr>
				<td>
					There are no patients who match the specified criteria.
				</td>
			</tr>
		<?php } else {?>
			<?php
            $i = 0;
    foreach ($operations as $eo) {
        $patient = $eo->event->episode->patient;
        $contact = $patient->contact;
        if (isset($_POST['status']) and $_POST['status'] != '') {
            if ($eo->getNextLetter() != $_POST['status']) {
                continue;
            }
                }?>

				<?php if ($eo->getWaitingListStatus() == Element_OphTrOperationbooking_Operation::STATUS_PURPLE) {
    $letterStatusClass = 'send-invitation-letter';
} elseif ($eo->getWaitingListStatus() == Element_OphTrOperationbooking_Operation::STATUS_GREEN1) {
    $letterStatusClass = 'send-another-reminder';
} elseif ($eo->getWaitingListStatus() == Element_OphTrOperationbooking_Operation::STATUS_GREEN2) {
    $letterStatusClass = 'send-another-reminder';
} elseif ($eo->getWaitingListStatus() == Element_OphTrOperationbooking_Operation::STATUS_ORANGE) {
    $letterStatusClass = 'send-gp-removal-letter';
} elseif ($eo->getWaitingListStatus() == Element_OphTrOperationbooking_Operation::STATUS_RED) {
    $letterStatusClass = 'patient-due-removed';
} else {
    $letterStatusClass = '';
                }?>

				<tr>
					<?php //FIXME: waiting list color needs adding to style for below to work ?>
					<td class="letter-status <?php echo $letterStatusClass ?>">
						<?php if ($eo->sentInvitation()) {?>
							<img src="<?php echo $assetPath?>/img/letterIcons/invitation.png" alt="Invitation" width="17" height="17" />
						<?php }?>
						<?php if ($eo->sent1stReminder()) {?>
							<img src="<?php echo $assetPath?>/img/letterIcons/letter1.png" alt="1st reminder" width="17" height="17" />
						<?php }?>
						<?php if ($eo->sent2ndReminder()) {?>
							<img src="<?php echo $assetPath?>/img/letterIcons/letter2.png" alt="2nd reminder" width="17" height="17" />
						<?php }?>
						<?php if ($eo->sentGPLetter()) {?>
							<img src="<?php echo $assetPath?>/img/letterIcons/GP.png" alt="GP" width="17" height="17" />
						<?php }?>
					</td>
					<td class="patient">
						<?php echo CHtml::link('<strong>'.trim(strtoupper($contact->last_name)).'</strong>, '.$contact->first_name." ({$patient->age})", Yii::app()->createUrl('/OphTrOperationbooking/default/view/'.$eo->event_id))?>
					</td>
					<td><?php echo $patient->hos_num ?></td>
					<td><?php echo $eo->site->short_name?></td>
					<td><?php echo $eo->getProceduresCommaSeparated('short_format') ?></td>
					<td><?php echo $eo->eye->name ?></td>
					<td><?php echo $eo->event->episode->firm->name ?> (<?php echo $eo->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->name?>)</td>
					<td><?php echo $eo->NHSDate('decision_date') ?></td>
					<td><?php echo $eo->priority->name?></td>
					<td><?php echo ucfirst(preg_replace('/^Requires /', '', $eo->status->name)) ?></td>
					<td<?php if ($letterStatusClass == '' && Yii::app()->user->checkAccess('admin')) { ?> class="admin-td"<?php } ?>>

						<?php if (($patient && $patient->contact->correspondAddress)
                            && $eo->id
                            && ($eo->getDueLetter() != Element_OphTrOperationbooking_Operation::LETTER_GP
                                || ($eo->getDueLetter() == Element_OphTrOperationbooking_Operation::LETTER_GP && $patient->practice && $patient->practice->contact->address)
                            )) {?>
							<div>
								<input<?php if ($letterStatusClass == '' && !Yii::app()->user->checkAccess('admin')) { ?> disabled="disabled"<?php } ?> type="checkbox" id="operation<?php echo $eo->id ?>" value="1" />
							</div>
						<?php }?>

						<?php if (!$patient->practice || !$patient->practice->contact->address) { ?>
							<script type="text/javascript">
								$('#pas_warnings').show();
								$('#pas_warnings .no_gp').show();
							</script>
							<span class="no-gp error">No GP</span>
						<?php }?>

						<?php if ($patient && !$patient->contact->correspondAddress) { ?>
							<script type="text/javascript">
								$('#pas_warnings').show();
								$('#pas_warnings .no_address').show();
							</script>
							<span class="no-address error">No Address</span>
						<?php }?>
					</td>
                    <?php if($this->module->isTheatreDiaryDisabled()): ?>
                    <td>
                        <button data-event-id="<?php echo $eo->event_id; ?>" class="small btn-booked">Booked</button>
                    </td>
                    <?php endif; ?>
				</tr>
				<?php
                ++$i;
    }

            if ($i == 0) {?>
				<tr>
					<td colspan="7">
						There are no patients who match the specified criteria.
					</td>
				</tr>
			<?php }?>
		<?php }?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="11" class="waiting-list-key">
				<h3>Colour Key:</h3>
				<ul class="inline-list">
					<li>
						<span class="key-box send-invitation-letter"></span>
						Send invitation letter
					</li>
					<li>
						<span class="key-box send-another-reminder"></span>
						Send another reminder (2 weeks)
					</li>
					<li>
						<span class="key-box send-gp-removal-letter"></span>
						Send GP removal letter
					</li>
					<li>
						<span class="key-box patient-due-removed"></span>
						Patient is due to be removed
					</li>
				</ul>
		</tr>
		<tr>
			<td colspan="11" class="letters-sent-out">
				<h3>Letters sent out:</h3>
				<ul class="inline-list">
					<li>
						<img src="<?php echo $assetPath?>/img/letterIcons/invitation.png" alt="Invitation" height="17" width="17">
						- Invitation
					</li>
					<li>
						<img src="<?php echo $assetPath?>/img/letterIcons/letter1.png" alt="1st reminder" height="17" width="17">
						- 1 <sup>st</sup> Reminder
					</li>
					<li>
						<img src="<?php echo $assetPath?>/img/letterIcons/letter2.png" alt="2nd reminder" height="17" width="17">
						- 2 <sup>nd</sup> Reminder
					</li>
					<li>
						<img src="<?php echo $assetPath?>/img/letterIcons/GP.png" alt="GP" height="17" width="17">
						- GP Removal
					</li>
				</ul>
			</td>
		</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	$('#checkall').click(function() {
		$('input[id^="operation"]:enabled').attr('checked',$('#checkall').is(':checked'));
	});

	// Row highlighting
	$(this).undelegate('.waiting-list td','click').delegate('.waiting-list td','click',function() {
		var $tr = $(this).closest("tr");
		$tr.toggleClass('hover');
	});

    // Mark item as booked (in case theatre diary is disabled)
    $(document).on("click", ".btn-booked", function(e){
        e.preventDefault();
        var event_id = $(this).data("event-id");
        $.get("/OphTrOperationbooking/waitingList/setBooked?event_id="+event_id,
                function(data){
                    if(data.success)
                    {
                        window.location.reload();
                    }
                    else
                    {
                        var alert = new OpenEyes.UI.Dialog.Alert({
                            content: 'An error occured: '+data.message
                        });
                        alert.open();
                    }
                });
    })
</script>
