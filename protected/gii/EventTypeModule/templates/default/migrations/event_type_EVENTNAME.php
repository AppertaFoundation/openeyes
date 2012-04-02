<?php echo '<?php '; ?>

class m<?php if (isset($migrationid)) echo $migrationid; ?>_event_type_<?php echo $this->moduleID; ?> extends CDbMigration
{
	public function up() {

		// --- EVENT TYPE ENTRIES ---

		// create an event_type entry for this event type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'<?php echo $this->moduleSuffix; ?>'))->queryRow()) {
			$group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name',array(':name'=>'<?php echo $this->eventGroupName; ?>'))->queryRow();
			$this->insert('event_type', array('class_name' => '<?php echo $this->moduleID;?>', 'name' => '<?php echo $this->moduleSuffix; ?>','event_group_id' => $group['id']));
		}
		// select the event_type id for this event type name
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'<?php echo $this->moduleSuffix; ?>'))->queryRow();

		// --- ELEMENT TYPE ENTRIES ---

		<?php
			if (isset($elements)) {
				foreach ($elements as $element) {
				?>
		// create an element_type entry for this element type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'<?php echo $element['name'];?>'))->queryRow()) {
			$this->insert('element_type', array('name' => '<?php echo $element['name'];?>','class_name' => '<?php echo $element['class_name'];?>', 'event_type_id' => $event_type['id'], 'display_order' => 1));
		}
		// select the element_type_id for this element type name
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'<?php echo $element['name'];?>'))->queryRow();
				<?
				}
			}
		?>

		// --- ELEMENT TYPE TABLES ---

		<?php
		if (isset($elements)) {
			foreach ($elements as $element) {
		?>
		// create the table for this element type: et_modulename_elementtypename
		$this->createTable('<?php echo $element['table_name'];?>', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			<?php
				$number = $element['number']; $count = 1;
				foreach ($element['fields'] as $field => $value) {
					$type = $element['fields'][$count]['type'];
					$name = $element['fields'][$count]['name'];
					$label = $element['fields'][$count]['label'];

			echo preg_replace("/\n/", "\n\t\t\t", $this->renderDBField($type, $name, $label));
					$count++;
				}
			?>
'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->addForeignKey('<?php echo $element['table_name'];?>_last_modified_user_id_fk','<?php echo $element['table_name'];?>','last_modified_user_id','user','id');
		$this->addForeignKey('<?php echo $element['table_name'];?>_created_user_id_fk','<?php echo $element['table_name'];?>','created_user_id','user','id');
	<?php } ?>
	<?php } ?>

		// --- TABLES RELATING TO SPECIFIC ELEMENTS ---

	}

	public function down() {
		echo "m000000_000001_event_type_<?php echo $this->moduleID; ?> does not support migration down.\n";
		return false;
	}
}
<?php echo '?>';?>
