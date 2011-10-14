<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

?><div id="theatreList">
<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerCSSFile('/css/theatre.css', 'all');
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');
$cs->registerCSSFile('/css/jqueryui/theme/jquery-ui.css', 'all');
$cs->registerScriptFile($baseUrl.'/js/jquery.multi-open-accordion-1.5.2.min.js');

if (empty($theatres)) { ?>
<h2 class="theatre">No theatre schedules match your search criteria.</h2>
</div>
<?php
} else { ?>
    <div id="multiOpenAccordion">
<?php
    $panels = array();
	$firstTheatreShown = false;
    foreach ($theatres as $name => $dates) { ?>
<h2 class="theatre<?php if (!$firstTheatreShown) { echo ' firstTheatre'; } ?>"><?php echo $name; ?></h2>
<?php   $firstTheatreShown = true;
		foreach ($dates as $date => $sessions) {
            $timestamp = strtotime($date); ?>
<h3 class="date"><a href="#"><?php echo date('d ', $timestamp);
            echo substr(date('F', $timestamp), 0, 3);
            echo date(' Y', $timestamp);
            echo ' - ' . date('l', $timestamp); ?></a></h3>
<div>
    <table>
    <tr>
        <th class="first">Session</th>
        <th class="repeat leftAlign">Patient (Age)</th>
        <th class="repeat leftAlign">[Eye] Operation</th>
        <th class="repeat">Duration</th>
        <th class="repeat">Ward</th>
        <th class="repeat">Anaesthetic</th>
        <th class="last">Alerts</th>
    </tr>
<?php        $lastSession = $sessions[0];
            foreach ($sessions as $session) {
                if ($session['sessionId'] != $lastSession['sessionId']) { ?>
    <tr>
        <th class="footer" colspan="7">Time unallocated: <?php
                    echo '<span';
                    if ($lastSession['timeAvailable'] < 0) {
                        echo ' class="full"';
                    }
                    echo ">{$lastSession['timeAvailable']}"; ?> min</span></th>
    </tr>
<?php                $lastSession = $session;
                } ?>
    <tr>
        <td class="session"><?php echo substr($session['startTime'], 0, 5) . '-' . substr($session['endTime'], 0, 5); ?></td>
        <td class="patient leftAlign"><?php echo ($session['patientName'] || $session['patientAge'] ? $session['patientName'] . ' (' . $session['patientAge'] . ')' : ''); ?></td>
        <td class="operation leftAlign"><?php echo ($session['eye'] ? '['.$session['eye'].']' : ''); ?> <?php echo !empty($session['procedures']) ? $session['procedures'] : 'No procedures'; ?></td>
        <td class="duration"><?php echo $session['operationDuration']; ?></td>
        <td class="ward"><?php echo $session['ward']; ?></td>
        <td class="anaesthetic"><?php echo $session['anaesthetic']; ?></td>
        <td class="alerts"><div class="alert gender invisible <?php echo $session['patientGender']; ?>"></div><?php
        if (!empty($session['operationComments'])) { ?><div class="alert comments invisble"><img class="invisible" src="/images/icon_comments.gif" alt="comments"title="<?php echo $session['operationComments']; ?>" /></div><?php
        } ?></td>
    </tr>
<?php
            } ?>
    <tr>
        <th class="footer" colspan="7">Time unallocated: <?php
                    echo '<span';
                    if ($session['timeAvailable'] < 0) {
                       echo ' class="full"';
                    }
                    echo ">{$session['timeAvailable']}"; ?> min</span></th>
    </tr>
    </table>
</div>
<?php
        }
    }
    ?>
    </div>
</div>
<div id="alertOptions">
    <input type="checkbox" name="theatre_alerts" value="comments" /> Comments<br />
    <input type="checkbox" name="theatre_alerts" value="gender" /> Gender<br />
    <input type="checkbox" name="theatre_alerts" value="latex" disabled="true" /> Latex allergy<br />
    <input type="checkbox" name="theatre_alerts" value="consultant" disabled="true" /> Consultant required
</div>
<div class="clear"></div>
<script type="text/javascript">
	$('input[name=theatre_alerts][value=comments]').click(function() {
		if ($(this).is(':checked')) {
			$('.comments').removeClass('invisible');
			$('.comments img').removeClass('invisible');
		} else {
			$('.comments').addClass('invisible');
			$('.comments img').addClass('invisible');
		}
	});
	$('input[name=theatre_alerts][value=gender]').click(function() {
		if ($(this).is(':checked')) {
			$('.gender').removeClass('invisible');
		} else {
			$('.gender').addClass('invisible');
		}
	});
	$('#multiOpenAccordion').multiOpenAccordion({
		autoHeight: false,
		clearStyle: true });
	// if we've selected today, or a same-day custom date range, show expanded
	if ('today' == $('input[name=date-filter]:checked').val() ||
		($('input[name=date-filter]:checked').val() == 'custom' &&
		$('input[id=date-start]').val() == $('input[id=date-end]').val())) {
		$('#multiOpenAccordion').multiOpenAccordion("option", "active", "all");
	} else {
		$('#multiOpenAccordion').multiOpenAccordion("option", "active", "none");
	}
</script>
<?php
}
