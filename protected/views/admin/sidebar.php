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
