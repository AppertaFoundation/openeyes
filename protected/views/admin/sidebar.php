<div class="box admin">
	<h2>System</h2>
	<ul class="navigation admin">
		<?php foreach (array(
			'Settings' => '/admin/settings',
		) as $title => $uri) {?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?> class="selected"<?php }?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?>
					<?php echo CHtml::link($title,array($uri),array('class' => 'selected'))?>
				<?php } else {?>
					<?php echo CHtml::link($title,array($uri))?>
				<?php }?>
			</li>
		<?php }?>
	</ul>
</div>
<div class="box admin">
	<h2>Core</h2>
	<ul class="navigation admin">
		<?php foreach (array(
			'Users' => '/admin/users',
			'Firms' => '/admin/firms',
			'Contacts' => '/admin/contacts',
			'Contact labels' => '/admin/contactlabels',
			'Data sources' => '/admin/datasources',
			'Institutions' => '/admin/institutions',
			'Sites' => '/admin/sites',
			'Commissioning bodies' => '/admin/commissioning_bodies',
			'Commissioning body types' => '/admin/commissioning_body_types',
			'Commissioning body services' => '/admin/commissioning_body_services',
			'Commissioning body service types' => '/admin/commissioning_body_service_types',
			'Event deletion requests' => '/admin/eventDeletionRequests',
			'Custom episode summaries' => '/admin/episodeSummaries',
			'Medication Stop Reason'=>'/admin/editmedicationstopreason',
			'Previous Ophthalmic Surgery'=>'/admin/editpreviousoperation',
			'Social History'=>'/admin/socialhistory',
			'Common Ophthalmic Disorder Groups' => '/admin/editcommonophthalmicdisordergroups',
			'Common Ophthalmic Disorders' => '/admin/editcommonophthalmicdisorder',
		 	'Secondary Common Ophthalmic Disorders' => '/admin/editsecondarytocommonophthalmicdisorder',
			'Findings' => '/admin/managefindings',
			'Anaesthetic Agent' => '/admin/viewAnaestheticAgent'
		) as $title => $uri) {?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?> class="selected"<?php }?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?>
					<?php echo CHtml::link($title,array($uri),array('class' => 'selected'))?>
				<?php } else {?>
					<?php echo CHtml::link($title,array($uri))?>
				<?php }?>
			</li>
		<?php }?>
	</ul>
</div>
<div class="box admin">
	<h2>Procedure Management</h2>
	<ul class="navigation admin">
		<?php foreach (array(
						'Procedures' => '/oeadmin/procedure/list',
						'Benefits' => '/oeadmin/benefit/list',
					    'Complications' => '/oeadmin/complication/list',
						'OPCS Codes' => '/oeadmin/opcsCode/list'
					   ) as $title => $uri) {?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?> class="selected"<?php }?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//','',$uri)) {?>
					<?php echo CHtml::link($title,array($uri),array('class' => 'selected'))?>
				<?php } else {?>
					<?php echo CHtml::link($title,array($uri))?>
				<?php }?>
			</li>
		<?php }?>
</div>
<div class="box admin">
	<h2>Drugs</h2>
	<ul class="navigation admin">
		<?php foreach (array(
						'Common Drugs List' => '/OphDrPrescription/admin/CommonDrugs',
						'Common Medications List ' => '/oeadmin/commonMedications/list',
						'Drug Sets' => '/OphDrPrescription/admin/DrugSets',
						'Medication List' => '/oeadmin/medication/list',
						'Formulary Drugs' => '/oeadmin/formularyDrugs/list',
						'Per Op Drugs' => '/OphTrOperationnote/admin/viewPostOpDrugs',
						'Per Op Drug Mappings' => '/OphTrOperationnote/admin/postOpDrugMappings',
					) as $title => $uri) { ?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)
			) { ?> class="selected"<?php } ?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)) { ?>
					<?php echo CHtml::link($title, array($uri), array('class' => 'selected')) ?>
				<?php } else { ?>
					<?php echo CHtml::link($title, array($uri)) ?>
				<?php } ?>
			</li>
		<?php } ?>
</div>
<div class="box admin">
	<h2>Disorders</h2>
	<ul class="navigation admin">
		<?php foreach (array(
						   'Common Systemic Disorders' => '/oeadmin/CommonSystemicDisorder/list',
					   ) as $title => $uri) { ?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)) { ?> class="selected"<?php } ?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)) { ?>
					<?php echo CHtml::link($title, array($uri), array('class' => 'selected')) ?>
				<?php } else { ?>
					<?php echo CHtml::link($title, array($uri)) ?>
				<?php } ?>
			</li>
		<?php } ?>
</div>
<div class="box admin">
	<h2>Consent</h2>
	<ul class="navigation admin">
		<?php foreach (array(
						   'Leaflets' => '/oeadmin/Leaflets/list',
					   ) as $title => $uri) { ?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)
			) { ?> class="selected"<?php } ?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)) { ?>
					<?php echo CHtml::link($title, array($uri), array('class' => 'selected')) ?>
				<?php } else { ?>
					<?php echo CHtml::link($title, array($uri)) ?>
				<?php } ?>
			</li>
		<?php } ?>
</div>
<div class="box admin">
	<h2>Operation Notes</h2>
	<ul class="navigation admin">
		<?php foreach (array(
						   'Anaesthetic Agent Defaults' => '/oeadmin/AnaestheticAgentDefaults/list',
					   ) as $title => $uri) { ?>
			<li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)
			) { ?> class="selected"<?php } ?>>
				<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)) { ?>
					<?php echo CHtml::link($title, array($uri), array('class' => 'selected')) ?>
				<?php } else { ?>
					<?php echo CHtml::link($title, array($uri)) ?>
				<?php } ?>
			</li>
		<?php } ?>
</div>

<?php foreach (ModuleAdmin::getAll() as $module => $items) {?>
	<div class="admin box">
		<h2><?php echo $module?></h2>
		<ul class="navigation admin">
			<?php foreach ($items as $item => $uri) {
				$e = explode('/',$uri);
				$action = array_pop($e)?>
				<li<?php if (Yii::app()->getController()->action->id == $action) {?> class="selected"<?php }?>>
					<?php if (Yii::app()->getController()->action->id == $action) {?>
						<?php echo CHtml::link($item,array($uri), array('class' => 'selected'))?>
					<?php } else {?>
						<?php echo CHtml::link($item,array($uri))?>
					<?php }?>
				</li>
			<?php }?>
		</ul>
	</div>
<?php }?>
