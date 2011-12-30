<div class="banner">
	<div class="seal"><img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></div>
	<div class="logo"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></div>
</div>
<div class="fromAddress">
	<?php echo $site->letterhtml ?>
	<br />Tel: <?php echo CHtml::encode($site->telephone) ?>
	<?php if($site->fax) { ?>
	<br />Fax: <?php echo CHtml::encode($site->fax) ?>
	<?php } ?>
</div>
<div class="toAddress">
	<?php echo $patient->addressname ?>
	<br /><?php echo $patient->address->letterhtml ?>
</div>
<div class="date">
	<?php echo date('d M Y') ?>
</div>
<div class="content">
	<p>
		<strong>Hospital number reference: <?php echo $patient->hos_num ?>
			<?php if (!empty($patient->nhs_num)) { ?>
				<br />NHS number: <?php echo $patient->nhs_num; } ?>
		</strong>
	</p>

	<p>
		Dear <?php echo $patient->salutationname ?>,
	</p>

