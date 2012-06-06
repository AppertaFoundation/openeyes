<?php
class EventTypeModuleCode extends BaseModuleCode // CCodeModel
{
	public $moduleID;
	public $moduleName;
	public $moduleSuffix;
	public $eventGroupName;
	public $template = "default";
	public $form_errors = array();

	private $validation_rules = array(
		'element_name' => array(
			'required' => true,
			'required_error' => 'Please enter an element name.',
			'regex' => '/^[a-zA-Z\s]+$/',
			'regex_error' => 'Element name must be letters and spaces only.'
		),
		'element_field_name' => array(
			'required' => true,
			'required_error' => 'Please enter a field name.',
			'regex' => '/^[a-z_]+$/',
			'regex_error' => 'Field name must be a-z and underscores only.'
		),
		'element_field_label' => array(
			'required' => true,
			'required_error' => 'Please enter a field label.',
			'regex' => '/^[a-zA-Z0-9\s]+$/',
			'regex_error' => 'Field label must be letters, numbers and spaces only.'
		)
	);
	public $cssPath, $jsPath, $imgPath;

	public function rules() {
		return array(
			array('moduleSuffix', 'required'),
			array('moduleSuffix', 'safe'),
		);
	}

	public function prepare() {
		$this->moduleID = ucfirst(strtolower(Specialty::model()->findByPk($_REQUEST['Specialty']['id'])->code)) . ucfirst(strtolower(EventGroup::model()->findByPk($_REQUEST['EventGroup']['id'])->code)) . Yii::app()->request->getQuery('Specialty[id]') . preg_replace("/ /", "", ucfirst($this->moduleSuffix));
		parent::prepare();

		$this->moduleName = $this->moduleID;
		$this->eventGroupName = EventGroup::model()->findByPk($_REQUEST['EventGroup']['id'])->name;
		$this->files=array();
		$templatePath=$this->templatePath;
		$modulePath=$this->modulePath;
		$moduleTemplateFile=$templatePath.DIRECTORY_SEPARATOR.'module.php';

		$this->files[]=new CCodeFile($modulePath.'/'.$this->moduleClass.'.php', $this->render($moduleTemplateFile));

		$files=CFileHelper::findFiles($templatePath,array('exclude'=>array('.svn'),));

		foreach($files as $file) {
			$destination_file = preg_replace("/EVENTNAME|EVENTTYPENAME|MODULENAME/", $this->moduleID, $file);
			if($file!==$moduleTemplateFile) {
				if(CFileHelper::getExtension($file)==='php') {
					if (preg_match("/\/migrations\//", $file)) {
						# $matches = Array();
						if (file_exists($modulePath.'/migrations/') and ($matches = $this->regExpFile("/m([0-9]+)\_([0-9]+)\_event_type_".$this->moduleID."/",$modulePath.'/migrations/'))) {
							// migration file exists, so overwrite it rather than creating a new timestamped file
							$migrationid = $matches[1] . '_' . $matches[2];
						} else {
							$migrationid = gmdate('ymd_His');
						}
						$destination_file = preg_replace("/\/migrations\//", '/migrations/m'.$migrationid.'_', $destination_file);
						$content=$this->renderMigrations($file, $migrationid);
						$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
					} elseif (preg_match("/ELEMENTNAME|ELEMENTTYPENAME/", $file)) {
						foreach ($this->getElementsFromPost() as $element) {
							$destination_file = preg_replace("/ELEMENTNAME|ELEMENTTYPENAME/", $element['class_name'], $file);
							$params = array(); $params['element'] = $element; $content=$this->render($file, $params);
							$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
						}
					} else {
						$content=$this->render($file);
						$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
					}
				// an empty directory
				} else if(basename($file)==='.yii') {
					$file=dirname($file);
					$content=null;
				} else {
					$content=file_get_contents($file);
					$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
				}
			}
		}
	}	

	function regExpFile($regExp, $dir) {
		$open = opendir($dir); $matches = Array();
		while( ($file = readdir($open)) !== false ) {
			if ( preg_match($regExp, $file, $matches) ) {
				return $matches;
			}
		}
		return false;
	}

	public function getElementsFromPost() {
		$elements = Array();
		foreach ($_POST as $key => $value) {
			if (preg_match('/^elementName([0-9]+)$/',$key, $matches)) {
				$field = $matches[0]; $number = $matches[1]; $name = $value;
				$elements[$number]['name'] = $value;
				$elements[$number]['class_name'] = 'OEElement' . preg_replace("/ /", "", ucwords(strtolower($value)));
				$elements[$number]['table_name'] = 'et_' . strtolower($this->moduleID) . '_' . strtolower(preg_replace("/ /", "", $value));;
				$elements[$number]['number'] = $number;

				$elements[$number]['last_modified_user_key'] = $elements[$number]['table_name'] . '_last_modified_user_id_fk';
				$elements[$number]['created_user_key'] = $elements[$number]['table_name'] . '_created_user_id_fk';
				$elements[$number]['event_key'] = $elements[$number]['table_name'] . '_event_id_fk';

				if (strlen($elements[$number]['last_modified_user_key']) >64 || strlen($elements[$number]['created_user_key']) >64 || strlen($elements[$number]['event_key']) >64) {
					$elements[$number]['last_modified_user_key'] = $this->generateKeyName('last_modified_user_id',$value);
					$elements[$number]['created_user_key'] = $this->generateKeyName('created_user_id',$value);
					$elements[$number]['event_key'] = $this->generateKeyName('event_id',$value);
				}

				$fields = Array();
				foreach ($_POST as $fields_key => $fields_value) {
					$pattern = '/^' . $field . 'FieldName([0-9]+)$/';
					if (preg_match($pattern, $fields_key, $field_matches)) {
						$field_number = $field_matches[1];
						$elements[$number]['fields'][$field_number] = Array();
						$elements[$number]['fields'][$field_number]['name'] = $fields_value;
						$elements[$number]['fields'][$field_number]['label'] = $_POST[$field . "FieldLabel".$field_number];
						$elements[$number]['fields'][$field_number]['number'] = $field_number;
						$elements[$number]['fields'][$field_number]['type'] = $_POST["elementType" . $number . "FieldType".$field_number];
					}
				}
			}
		}
		return $elements;
	}

	public function generateKeyName($field, $elementName) {
		$key = 'et_' . strtolower($this->moduleID) . '_';

		foreach (explode(' ',$elementName) as $segment) {
			$key .= strtolower($segment[0]);
		}

		return $key . '_'.$field.'_fk';
	}

	public function renderMigrations($file, $migrationid) {
		$params = array(); $params['elements'] = $this->getElementsFromPost(); $params['migrationid'] = $migrationid;
		return $this->render($file, $params);
	}

	public function renderDBField($type, $name, $label) {
		$sql = '';
		if ($type == 'Textbox') {
			$sql = "'{$name}' => 'varchar(255) DEFAULT \'\'', // {$label}\n";
		} elseif ($type == 'Textarea') {
			$sql = "'{$name}' => 'text DEFAULT \'\'', // {$label}\n";
		} elseif ($type == 'Date picker') {
			// $sql = "'{$name}' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'', // {$label}\n";
			$sql = "'{$name}' => 'date DEFAULT NULL', // {$label}\n";
		} elseif ($type == 'Dropdown list') {
			$sql = "'{$name}' => 'int(10) unsigned NOT NULL', // {$label}\n";
		} elseif ($type == 'Checkbox') {
			$sql = "'{$name}' => 'tinyint(1) unsigned NOT NULL', // {$label}\n";
		} elseif ($type == 'Radio buttons') {
			// we don't create a field for these, as they'll need to be stored in a linked table
		} elseif ($type == 'Boolean') {
			$sql = "'{$name}' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // {$label}\n";
		} elseif ($type == 'EyeDraw') {
			// we create two fields for eyedraw: one for json, and one for the report
			$sql = "'{$name}_json' => 'text DEFAULT \'\'', // {$label} (eyedraw: json)\n";
			$sql .="'{$name}_report' => 'text DEFAULT \'\'', // {$label} (eyedraw: report)\n";
		}
		return $sql;
	}

	public function init() {
		if (isset($_GET['ajax']) && preg_match('/^[a-z_]+$/',$_GET['ajax'])) {
			Yii::app()->getController()->renderPartial($_GET['ajax'],$_GET);
			exit;
		}

		if (!empty($_POST)) {
			$this->validate_form();
		}

		parent::init();
	}

	public function validate_form() {
		$errors = array();

		foreach ($_POST as $key => $value) {
			if (preg_match('/^elementName[0-9]+$/',$key)) {
				if ($this->validation_rules['element_name']['required'] && strlen($value) <1) {
					$errors[$key] = $this->validation_rules['element_name']['required_error'];
				} else if (!preg_match($this->validation_rules['element_name']['regex'],$value)) {
					$errors[$key] = $this->validation_rules['element_name']['regex_error'];
				}
			}

			if (preg_match('/^elementName[0-9]+FieldName[0-9]+$/',$key)) {
				if ($this->validation_rules['element_field_name']['required'] && strlen($value) <1) {
					$errors[$key] = $this->validation_rules['element_field_name']['required_error'];
				} else if (!preg_match($this->validation_rules['element_field_name']['regex'],$value)) {
					$errors[$key] = $this->validation_rules['element_field_name']['regex_error'];
				}
			}

			if (preg_match('/^elementName[0-9]+FieldLabel[0-9]+$/',$key)) {
				if ($this->validation_rules['element_field_label']['required'] && strlen($value) <1) {
					$errors[$key] = $this->validation_rules['element_field_label']['required_error'];
				} else if (!preg_match($this->validation_rules['element_field_label']['regex'],$value)) {
					$errors[$key] = $this->validation_rules['element_field_label']['regex_error'];
				}
			}
		}

		Yii::app()->getController()->form_errors = $errors;
	}
}
