<?php
class BaseEventTypeElement extends BaseElement
{
	function getElementType() {
		return ElementType::model()->find('class_name=?', array(get_class($this)));
	}

	function render($action) {
		$this->Controller->renderPartial();
	}

	function getFormOptions($table) {
		$options = array();

		if (Yii::app()->getDb()->getSchema()->getTable("element_type_$table")) {
			foreach (Yii::app()->db->createCommand()
				->select("$table.*")
				->from($table)
				->join("element_type_$table","element_type_$table.{$table}_id = $table.id")
				->where("element_type_id = ".$this->getElementType()->id)
				->order("display_order asc")
				->queryAll() as $option) {

				$options[$option['id']] = $option['name'];
			}
		} else {
			foreach (Yii::app()->db->createCommand()
				->select("$table.*")
				->from($table)
				->queryAll() as $option) {

				$options[$option['id']] = $option['name'];
			}
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

	function getSetting($key) {
		$element_type = ElementType::model()->find('class_name=?',array(get_class($this)));

		if (!$metadata = SettingMetadata::model()->find('element_type_id=? and `key`=?',array($element_type->id,$key))) {
			return false;
		}

		if ($setting = SettingInstallation::model()->find('element_type_id=? and `key`=?',array($element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		$site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

		if ($setting = SettingInstitution::model()->find('institution_id=? and element_type_id=? and `key`=?',array($site->institution_id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingSite::model()->find('site_id=? and element_type_id=? and `key`=?',array($site->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		if ($setting = SettingSpecialty::model()->find('specialty_id=? and element_type_id=? and `key`=?',array($firm->serviceSubspecialtyAssignment->subspecialty->specialty_id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingSubspecialty::model()->find('subspecialty_id=? and element_type_id=? and `key`=?',array($firm->serviceSubspecialtyAssignment->subspecialty_id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingFirm::model()->find('firm_id=? and element_type_id=? and `key`=?',array($firm->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		$user = Yii::app()->session['user'];

		if ($setting = SettingUser::model()->find('user_id=? and element_type_id=? and `key`=?',array($user->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		return $metadata->default_value;
	}

	function parseSetting($setting, $metadata) {
		if (@$data = unserialize($metadata->data)) {
			if (isset($data['model'])) {
				$model = $data['model'];
				return $model::model()->findByPk($setting->value);
			}
		}

		return $setting->value;
	}
}
