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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

?><!doctype html> 
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]--> 
<!--[if IE 7]>		<html class="no-js ie7 oldie" lang="en"> <![endif]--> 
<!--[if IE 8]>		<html class="no-js ie8 oldie" lang="en"> <![endif]--> 
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]--> 
<head> 
	<meta charset="utf-8"> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
	<title><?php echo CHtml::encode($this->pageTitle); ?></title> 
	<meta name="viewport" content="width=device-width"> 
	<link rel="icon" href="favicon.ico" type="image/x-icon" /> 
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico"/> 
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css"> 
	<link rel="stylesheet" type="text/css" href="/css/jquery.fancybox-1.3.4.css" />
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jui/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/jquery.watermark.min.js"></script>
	<script type="text/javascript" src="/js/jquery.fancybox-1.3.4.pack.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/modernizr-2.0.6.min.js"></script> 
</head> 
 
<body> 
	<?php if (Yii::app()->params['watermark']) {?>
		<div class="h1-watermark"><?php echo Yii::app()->params['watermark']?></div>
	<?php }?>

	<?php echo $this->renderPartial('/base/_debug',array())?> 
	<div id="container"> 
		<div id="header" class="clearfix"> 
			<div id="brand" class="ir"><a href="/site/index"><h1>OpenEyes</h1></a></div> 
			<?php echo $this->renderPartial('//base/_form', array()); ?>
			<div id="patientID">
				<div class="i_patient">
					<a href="/patient/view/<?php echo $this->model->id?>" class="small">View Summary</a>
					<img class="i_patient" src="/img/_elements/icons/patient_small.png" alt="patient_small" width="26" height="30" />
				</div>

				<div class="patientReminder">
					<span class="given"><?php echo $this->model->first_name?></span>
					<span class="surname"><?php echo $this->model->last_name?></span>
					<span class="number"><?php echo $this->model->hos_num?></span>
				</div>
			</div> <!-- #patientID -->

		</div> <!-- #header --> 
		<!--div id="mainmenu">
			<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'Home', 'url'=>array('/site/index'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Admin', 'url'=>array('/admin'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Search Patients', 'url'=>array('/patient/admin'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Phrases for this firm', 'url'=>array('/phraseByFirm/index'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
					array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
				),
			)); ?>
		</div--><!-- mainmenu -->

		<div id="content"> 
			<?php echo $content; ?>
<!-- ====================================================  P R I N T  S T U F F ============  -->

  <div class="printable">
  <!-- ================================================ -->
  <!-- * * * * * * * * * *  LETTER  * * * * * * * * * * -->
  <!-- ================================================ -->
  
  <div id="letters">
  	<div id="letterTemplate">
  		<div id="l_type">Type of Letter</div>
  		<div id="l_address">

  			<table width="100%">
  				<tr>
  					<td style="text-align:left;" width="50%"><img src="img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></td>
  					<td style="text-align:right;"><img src="img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></td>
  				</tr>
  				<tr>
  					<td colspan="2" style="text-align:right;">
					LocationFullName<br />

					LocationAddress1<br />
					LocationAddress2<br />
					LocationAddress3<br />
					LocationAddress4<br />
					</td>
  				</tr>
  				<tr>

  					<td style="text-align:left;">
					Parent/Guardian of PatientName<br />
					PatientAddress1<br />
					PatientAddress2<br />
					PatientCity<br />
					PatientPostCode<br />

					PatientCountry<br />
					</td>
					<td style="text-align:right;">
					&nbsp;<br />
					Tel LocationTel<br />
					Fax LocationFax<br />
					</td>

  				</tr>
  				<tr>
  					<td colspan="2" style="text-align:right;">
					LetterDate
					</td>
  				</tr>
  			</table>
  		</div>
  		<div id="l_content">

<p><strong>Hospital number reference: INP/A/Hosnum<br />
NHS number:</strong></p>  

<p>Dear Parent or Guardian of PatientName,</p>

<p>I have been asked to arrange your child's admission for surgery under the care of CONSULTANT. This is currently anticipated to be a<ADMIT TYPE> procedure STAYLENGTH in hospital.</p>

<p>Please will you telephone CONTACT within TIME LIMIT of the date of this letter to discuss and agree a convenient date for this operation. If there is no reply, please leave a message and contact number on the answer phone.</p>

<p>Should your child no longer require treatment, please let me know as soon as possible.</p>


<p>Yours sincerely,
<br />
<br />
<br />
<br />
<br />
Admissions Officer</p>
  		</div>
  		
  		
  	</div> <!-- #letterTemplate -->
  </div> <!-- #letters -->

  
<div id="letterFooter">   <!--  letter footer -->
Patron: Her Majesty The Queen<br />
Chairman: Rudy Markham<br />
Chief Executive: John Pelly<br />
</div>
  
  <!-- ================================================ -->
  <!-- * * * * * * * * end of LETTER  * * * * * * * * * --> 
  <!-- ================================================ -->
  
  
  <!-- ================================================ -->

  <!-- * * * * * * * * *    FORM    * * * * * * * * * * --> 
  <!-- ================================================ -->
  
<div id="printForm">
  	<div id="printFormTemplate">
		<table width="100%">
			<tr>
				<td colspan="2" style="border:none;">&nbsp;</td>
				<td colspan="4" style="text-align:right; border:none;"><img src="img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></td>
			</tr>

			<tr>
				<td colspan="2" width="50%"> <!-- width control -->
					<span class="title">Admission Form</span>
				</td>
				<td rowspan="4">
					Patient Name,<br />
					Address<br />

					Address<br />
				</td>
				<td rowspan="4">
					Patient Name,<br />
					Address 1<br />
					Address 1<br />
				</td>

			</tr>
			<tr>
				<td>Hospital Number</td>
				<td>number</td>
			</tr>
			<tr>
				<td>DOB</td>

				<td>dd/mm/yyyy</td>
			</tr>
			<tr>
				<td>[empty]</td>
				<td>[empty]</td>
			</tr>
		</table>

		
		<table width="100%">
			<tr>
				<td width="25%"><strong>Admitting Consultant:</strong></td> <!-- width control -->
				<td width="25%">Consultant</td>
				<td><strong>Decision to admit date (or today�s date):</strong></td>
				<td>AdminDate</td>

			</tr>
			<tr>
				<td>Service:</td>				
				<td>Service</td>				
				<td>Telephone:</td>				
				<td>Telephone</td>				
			</tr>
			<tr>
				<td>Site:</td>				
				<td>site</td>				
				<td colspan="2">

					<table width="100%" class="subTableNoBorders">
						<tr>
							<td>AlternatePhone</td>
							<td>WorkPhone</td>
							<td>MobilePhone</td>
						</tr>
					</table>

				</td>				
			</tr>
			<tr>
				<td><strong>Person organising admission:</strong></td>				
				<td>Doctor</td>				
				<td><strong>Dates patient unavailable:</strong></td>				
				<td>DatesCantComeIn</td>
			</tr>
			<tr>

				<td colspan="2" style="border-bottom:1px dotted #000;">Signature:</td>								
				<td>Available at short notice:</td>				
				<td>ShortNotice</td>
			</tr>
		</table>
		<span class="subTitle">ADMISSION DETAILS</span>
		<table width="100%">
			<tr>

				<td width="25%"><strong>Urgency:</strong></td> <!-- width control -->
				<td width="25%">Urgency</td>
				<td><strong>Consultant to be present:</strong></td>
				<td>Cons</td>
			</tr>
			<tr>

				<td>Admission category:</td>				
				<td>DayCase</td>				
				<td colspan="2" rowspan="5" align="center" style="vertical-align:middle;">
					<strong>Patient Added to Waiting List.<br />
					Admission Date to be arranged</strong>
				</td>				
								
			</tr>
			<tr>

				<td><strong>Diagnosis:</strong></td>				
				<td>Diagnosis</td>				
								
			</tr>
			<tr>
				<td><strong>Intended procedure:</strong></td>				
				<td>Operation</td>				
				
			</tr>
			<tr>
				<td><strong>Eye:</strong></td>								
				<td>eye</td>				
			</tr>

			<tr>
				<td><strong>Total theatre time (mins):</strong></td>								
				<td>duration</td>				
			</tr>
		</table>
		<span class="subTitle">PRE-OP ASSESSMENT INFORMATION</span>
		<table width="100%">
			<tr>

				<td width="25%"><strong>Anaesthesia:</strong></td> <!-- width control -->
				<td width="25%">anaesth</td>
				<td><strong>Likely to need anaesthetist review:</strong></td>
				<td>anaes</td>
			</tr>
			<tr>

				<td><strong>Anaesthesia is:</strong></td>
				<td>anaesth</td>
				<td><strong>Does the patient need to stop medication:</strong></td>
				<td>stopMed</td>
			</tr>
		</table>
		<span class="subTitle">COMMENTS</span>

		<table width="100%">
			<tr>
				<td style="border:2px solid #666; height:7em;">Comments</td>
			</tr>
			
		</table>	
		
  	</div> <!-- adminFormTemplate -->
 </div> <!-- printForm -->
  
  
  <!-- ================================================ -->

  <!-- * * * * * * * *  end of FORM   * * * * * * * * * --> 
  <!-- ================================================ -->
  
  </div>
  <!-- ====================================================  end of P R I N T  S T U F F ============  -->

		</div> <!-- #content --> 
		<div id="help" class="clearfix"> 
			<div class="hint">
				<p><strong>Online Help</strong></p>
				<p><a href="#">Quick Reference Guide</a></p>
				<p>&nbsp;</p>
				<p><strong>Helpdesk</strong></p>
				<p>Telephone: <?php echo Yii::app()->params['helpdesk_phone']?></p>
				<p>Email: <a href="mailto:<?php echo Yii::app()->params['helpdesk_email']?>"><?php echo Yii::app()->params['helpdesk_email']?></a></p>
			</div>
		</div> 
	</div>
	<!--#container --> 

	<?php echo $this->renderPartial('/base/_footer',array())?>
 
	<script defer src="<?php echo Yii::app()->request->baseUrl; ?>/js/plugins.js"></script>
	<script defer src="<?php echo Yii::app()->request->baseUrl; ?>/js/script.js"></script>

	<script type="text/javascript">
		$('select[id=selected_firm_id]').die('change').live('change', function() {
			var firmId = $('select[id=selected_firm_id]').val();
			$.ajax({
				type: 'post',
				url: '<?php echo Yii::app()->createUrl('site'); ?>',
				data: {'selected_firm_id': firmId },
				success: function(data) {
					console.log(data);
					window.location.href = '<?php echo Yii::app()->createUrl('site'); ?>';
				}
			});
		});

                function printContent(content) {
			$('printable').display();
                        //window.print();
                }
	</script>

	<?php if (Yii::app()->params['watermark']) {?>
		<div class="h1-watermark"><?php echo Yii::app()->params['watermark']?></div>
	<?php }?>

	<?php if (Yii::app()->params['google_analytics_account']) {?>
		<script type="text/javascript">

			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?php echo Yii::app()->params['google_analytics_account']?>']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();

		</script>
	<?php }?>
</body> 
</html>
