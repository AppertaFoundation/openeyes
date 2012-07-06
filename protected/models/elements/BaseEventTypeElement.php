<?php
class BaseEventTypeElement extends BaseElement
{
	function getElementType() {
		echo $this->tableSchema->name."\n";

		$model_path = $this->find_model(Yii::app()->basePath, $this->tableSchema->name);

		if (preg_match('/\/modules\/(.*?)\//',$model_path,$m)) {
			$event_type = EventType::model()->find('class_name=?',array($m[1]));
			return ElementType::model()->find('class_name=? and event_type_id=?',array(get_class($this),$event_type->id));
		}
		
		return ElementType::model()->find('class_name=?', array(get_class($this)));
	}

	function find_model($path, $table_name) {
		$dh = opendir($path);

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				if (is_file($path."/".$file)) {
					if ($this->is_model($path."/".$file, $table_name)) {
						return $path."/".$file;
					}
				} else {
					if ($model_path = $this->find_model($path."/".$file, $table_name)) {
						return $model_path;
					}
				}
			} 
		}
		
		closedir($dh);
	}
	
	function is_model($path, $table_name) {
		$data = file_get_contents($path);

		if (preg_match('/function tableName.*?return[\s\t]+\'(.*?)\'/s',$data,$m)) {
			if ($m[1] == $table_name) {
				return $path;
			}
		}

		return false;
	}

	function getElementType() {
		return ElementType::model()->find('class_name=? and event_type_id=?', array(get_class($this),$this->event->event_type_id));
	}

	function render($action) {
		$this->Controller->renderPartial();
	}

	function getFormOptions($table) {
		$options = array();

		foreach (Yii::app()->db->createCommand()
			->select("$table.*")
			->from($table)
			->join("element_type_$table","element_type_$table.{$table}_id = $table.id")
			->where("element_type_id = ".$this->getElementType()->id)
			->order("display_order asc")
			->queryAll() as $option) {

			$options[$option['id']] = $option['name'];
		}

		return $options;
	}

	function getInfoText() {
	}

	function getCreate_view() {
		return get_class($this);
	}

	function getUpdate_view() {
		return get_class($this);
	}

	function getView_view() {
		return get_class($this);
	}

	function getPrint_view() {
		return get_class($this);
	}

	function isEditable() {
		return true;
	}
}
