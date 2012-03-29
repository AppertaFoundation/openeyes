<?php echo '<?php'; ?>
class m000000_000001_event_type_<?php echo strtolower($this->moduleID); ?> extends CDbMigration
{
	public function up() {
		// create an event_type entry for this event type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'<?php echo $this->moduleSuffix; ?>'))->queryRow()) {
			$group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name',array(':name'=>'<?php echo $this->eventGroupName; ?>'))->queryRow();
			$this->insert('event_type', array('name' => '<?php echo $this->moduleSuffix; ?>','event_group_id' => $group['id']));
		}
		// select the event_type id for this event type name
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'<?php echo $this->moduleSuffix; ?>'))->queryRow();


		// create an element_type entry for this element type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'Procedure list'))->queryRow()) {
			$this->insert('element_type', array('name' => 'Procedure list','class_name' => 'ElementProcedureList', 'event_type_id' => $event_type['id'], 'display_order' => 1));
		}
		// select the element_type_id for this element type name
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'Procedure list'))->queryRow();

		// create the table for this element type: et_modulename_elementtypename
		$this->createTable('et_ophtroperationnote_procedurelist', array(
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
	}

	public function down() {
		echo "m000000_000001_event_type_EVENTTYPENAME does not support migration down.\n";
		return false;
	}
<?php echo '?>';?>
