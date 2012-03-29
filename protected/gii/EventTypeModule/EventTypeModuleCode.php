<?php
class EventTypeModuleCode extends ModuleCode // CCodeModel
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
			$destination_file = preg_replace("/EVENTNAME|EVENTTYPENAME|MODULENAME/", strtolower($this->moduleID), $file);
			if($file!==$moduleTemplateFile) {
				if(CFileHelper::getExtension($file)==='php') {
					if (preg_match("/\/migrations\//", $file)) {
						$content=$this->renderMigrations($file);
						$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
					} elseif (preg_match("/ELEMENTNAME|ELEMENTTYPENAME/", $file)) {
						# FIXME: Loop through generating this file for each element type
						$content=$this->render($file);
						$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
					} else {
						$content=$this->render($file);
						$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
					}
				// an empty directory
				} else if(basename($file)==='.yii') {
					$file=dirname($file);
					$content=null;
					$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
				} else {
					$content=file_get_contents($file);
					$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
					// $this->files[]=new CCodeFile($modulePath.substr($file,strlen($templatePath)), $content);
				}
				$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
			}
		}
	}

	# array ( 'Specialty' => array ( 'id' => '1', ), 'EventGroup' => array ( 'id' => '1', ), 'EventTypeModuleCode' => array ( 'moduleSuffix' => 'oij oij oij oij oij oij ', 'template' => 'default', ), 'elementName1' => 'oij oij oij oij ', 'elementName1FieldName1' => 'oij oij ', 'elementName1FieldLabel1' => 'oij oij oij ', 'elementType1FieldType1' => '1', 'elementName1FieldName2' => 'oijoi joij oij ', 'elementName1FieldLabel2' => 'oij oij oij oij ', 'elementType1FieldType2' => '1', 'elementName2' => 'oijoij', 'elementName2FieldName1' => 'oijoij', 'elementName2FieldLabel1' => 'oijoij', 'elementType2FieldType1' => '1', 'elementName2FieldName2' => 'oijoijoij', 'elementName2FieldLabel2' => 'oijoijoij', 'elementType2FieldType2' => '1', 'preview' => 'Preview', )
	public function renderMigrations($file) {
		echo $this->moduleSuffix; exit;
		foreach ($_POST as $key => $value) {
			if (preg_match('/^elementName([0-9]+)$/',$key, $matches)) {
				$field = $matches[0]; $number = $matches[1]; $name = $value;

				$fields = Array();
				// get all fields for elementNameX
				foreach ($_POST as $fields_key => $fields_value) {
					$pattern = '/^' . $field . 'FieldName([0-9]+)$/';
					if (preg_match($pattern, $fields_key, $field_matches)) {
						$field_number = $field_matches[1];
						$field_name = $fields_value;
						$field_label = $_POST[$field . "FieldLabel".$field_number];
						$field_type = $_POST["elementType" . $number . "FieldType".$field_number];
						$fields[$number]['name'] = $field_name;
						$fields[$number]['number'] = $field_number;
						$fields[$number]['type'] = $field_type;
					}
				}


				# Textbox, Textarea, Date picker, Dropdown list, Checkboxes, Radio buttons, Boolean, EyeDraw
				// generate this element
				$sql = '';
				foreach (array_keys($fields[$number]) as $f) {
					if ($f['type'] == 'Textbox') {
						$sql .= '';
					} elseif ($f['type'] == 'Textarea') {
						$sql .= '';
					} elseif ($f['type'] == 'Date picker') {
						$sql .= '';
					} elseif ($f['type'] == 'Dropdown list') {
						$sql .= '';
					} elseif ($f['type'] == 'Checkboxes') {
						$sql .= '';
					} elseif ($f['type'] == 'Radio buttons') {
						$sql .= '';
					} elseif ($f['type'] == 'Boolean') {
						$sql .= '';
					} elseif ($f['type'] == 'EyeDraw') {
						$sql .= '';
					}
				}
			}
		}
		return $this->render($file);
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
