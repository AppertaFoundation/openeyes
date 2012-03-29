<?php echo '<?php'; ?>
class m000000_000001_event_type_<?php echo $this->moduleID; ?> extends CDbMigration
{
	public function up() {
		// create an event_type entry for this event type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'<?php echo $this->moduleSuffix; ?>'))->queryRow()) {
			$group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name',array(':name'=>'<?php echo $this->eventGroupName; ?>'))->queryRow();
			$this->insert('event_type', array('name' => '<?php echo $this->moduleSuffix; ?>','event_group_id' => $group['id']));
		}
		// select the event_type id for this event type name
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'<?php echo $this->moduleSuffix; ?>'))->queryRow();

		<?php
			if (isset($elements)) {
				foreach ($elements as $element) {
					// create an element_type entry for this element type name if one doesn't already exist
				?>
					if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'<?php echo $element['name'];?>'))->queryRow()) {
						$this->insert('element_type', array('name' => '<?php echo $element['name'];?>','class_name' => '<?php echo $element['class_name'];?>', 'event_type_id' => $event_type['id'], 'display_order' => 1));
					}

					// select the element_type_id for this element type name
					$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'<?php echo $element['name'];?>'))->queryRow();
				<?
				}
			}
		?>
		<?php
		if (isset($elements)) {
			foreach ($elements as $element) {
		?>
		// create the table for this element type: et_modulename_elementtypename
		$this->createTable('<?php echo $element['table_name'];?>', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			### FIELDS
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_last_modified_user_id_fk','et_ophtroperationnote_procedurelist','last_modified_user_id','user','id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_created_user_id_fk','et_ophtroperationnote_procedurelist','created_user_id','user','id');
	<?php
	}
	?>

	public function down() {
		echo "m000000_000001_event_type_<?php echo $this->moduleID; ?> does not support migration down.\n";
		return false;
	}
<?php echo '?>';?>
