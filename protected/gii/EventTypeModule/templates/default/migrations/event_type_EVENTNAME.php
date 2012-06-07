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

		<?php
		if (isset($elements)) {
			foreach ($elements as $element) {
				foreach ($element['lookup_tables'] as $lookup_table) {?>
		// element lookup table <?php echo $lookup_table['name']?>

		$this->createTable('<?php echo $lookup_table['name']?>', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `<?php echo $lookup_table['last_modified_user_key']?>` (`last_modified_user_id`)',
				'KEY `<?php echo $lookup_table['created_user_key']?>` (`created_user_id`)',
				'CONSTRAINT `<?php echo $lookup_table['last_modified_user_key']?>` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `<?php echo $lookup_table['created_user_key']?>` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

			<?php foreach ($lookup_table['values'] as $value) {?>
			$this->insert('<?php echo $lookup_table['name']?>',array('name'=>'<?php echo str_replace("'","\\'",$value)?>'));
			<?php }?>
				<?php }?>

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
				'KEY `<?php echo $element['last_modified_user_key']?>` (`last_modified_user_id`)',
				'KEY `<?php echo $element['created_user_key']?>` (`created_user_id`)',
				'KEY `<?php echo $element['event_key']?>` (`event_id`)',
				<?php foreach ($element['foreign_keys'] as $foreign_key) {?>
				'KEY `<?php echo $foreign_key['name']?>` (`<?php echo $foreign_key['field']?>`)',
				<?php }?>
				'CONSTRAINT `<?php echo $element['last_modified_user_key']?>` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `<?php echo $element['created_user_key']?>` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `<?php echo $element['event_key']?>` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				<?php foreach ($element['foreign_keys'] as $foreign_key) {?>
				'CONSTRAINT `<?php echo $foreign_key['name']?>` FOREIGN KEY (`<?php echo $foreign_key['field']?>`) REFERENCES `<?php echo $foreign_key['table']?>` (`id`)',
				<?php }?>
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	<?php } ?>
	<?php } ?>
	}

	public function down() {
		// --- drop any element related tables ---
		// --- drop element tables ---
		<?php
		if (isset($elements)) {
			foreach ($elements as $element) {
		?>
$this->dropTable('<?php echo $element['table_name']; ?>');

		<?php foreach ($element['lookup_tables'] as $lookup_table) {?>
		$this->dropTable('<?php echo $lookup_table['name']?>');
		<?php }?>

		<?php }} ?>

		// --- delete event entries ---
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'<?php echo $this->moduleSuffix; ?>'))->queryRow();

		foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id'=>$event_type['id']))->queryAll() as $row) {
			$this->delete('audit', 'event_id='.$row['id']);
			$this->delete('event', 'id='.$row['id']);
		}

		// --- delete entries from element_type ---
		$this->delete('element_type', 'event_type_id='.$event_type['id']);

		// --- delete entries from event_type ---
		$this->delete('event_type', 'id='.$event_type['id']);

		// echo "m000000_000001_event_type_<?php echo $this->moduleID; ?> does not support migration down.\n";
		// return false;
		echo "If you are removing this module you may also need to remove references to it in your configuration files\n";
		return true;
	}
}
<?php echo '?>';?>
