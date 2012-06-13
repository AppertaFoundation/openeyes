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
							$content = $this->render($file, array('element'=>$element));
							$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
						}
					} elseif (preg_match('/LOOKUPTABLE/',$file)) {
						foreach ($this->getElementsFromPost() as $element) {
							foreach ($element['lookup_tables'] as $lookup_table) {
								$destination_file = preg_replace('/LOOKUPTABLE/',$lookup_table['class'],$file);
								$content = $this->render($file, array('lookup_table'=>$lookup_table));
								$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
							}
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
		file_put_contents("/tmp/debug",print_r($_POST,true));

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

				$elements[$number]['foreign_keys'] = array();
				$elements[$number]['lookup_tables'] = array();
				$elements[$number]['relations'] = array();
				$elements[$number]['defaults'] = array();

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
						$elements[$number]['fields'][$field_number]['required'] = (boolean)@$_POST['isRequiredField'.$number.'_'.$field_number];

						if ($elements[$number]['fields'][$field_number]['type'] == 'Dropdown list') {
							$elements = $this->extraElementFieldWrangling_DropdownList($elements, $number, $field_number, $fields_value);
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'Textarea with dropdown') {
							$elements = $this->extraElementFieldWrangling_TextareaWithDropdown($elements, $number, $field_number, $fields_value);
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'Radio buttons') {
							$elements = $this->extraElementFieldWrangling_RadioButtons($elements, $number, $field_number, $fields_value);
						}
					}
				}
			}
		}

		return $elements;
	}

	public function extraElementFieldWrangling_DropdownList($elements, $number, $field_number, $fields_value) {
		// Dropdown list fields should end with _id
		if (!preg_match('/_id$/',$fields_value)) {
			$_POST['elementName'.$number.'FieldName'.$field_number] = $elements[$number]['fields'][$field_number]['name'] = $fields_value = $fields_value.'_id';
		}

		$elements[$number]['fields'][$field_number]['empty'] = @$_POST['dropDownUseEmpty'.$number.'Field'.$field_number];

		if (@$_POST['dropDownFieldValueTextInputDefault'.$number.'Field'.$field_number]) {
			$elements[$number]['defaults'][$fields_value] = @$_POST['dropDownFieldValueTextInputDefault'.$number.'Field'.$field_number];
		}

		if (@$_POST['dropDownMethod'.$number.'Field'.$field_number] == 0) {
			$elements[$number]['fields'][$field_number]['method'] = 'Manual';

			// Manually-entered values
			$field_values = array();

			foreach ($_POST as $value_key => $value_value) {
				if (preg_match('/^dropDownFieldValue'.$number.'Field'.$field_number.'/',$value_key)) {
					$field_values[] = $value_value;
				}
			}

			$lookup_table = array(
				'name' => $elements[$number]['fields'][$field_number]['lookup_table'] = $elements[$number]['table_name'].'_'.preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name'])
			);

			$key_name = $lookup_table['name'].'_fk';

			if (strlen($key_name) >64) {
				$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$value);
			}

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table['name']
			);

			$lookup_table['last_modified_user_key'] = $lookup_table['name'] . '_last_modified_user_id_fk';
			$lookup_table['created_user_key'] = $lookup_table['name'] . '_created_user_id_fk';
			$lookup_table['values'] = $field_values;

			if (strlen($lookup_table['last_modified_user_key']) >64 || strlen($lookup_table['created_user_key']) >64) {
				$lookup_table['last_modified_user_key'] = $lookup_table['name'] . '_lmui_fk';
				$lookup_table['created_user_key'] = $lookup_table['name'] . '_cui_fk';
			}

			$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = str_replace(' ','',ucwords(str_replace('_',' ',$lookup_table['name'])));

			$elements[$number]['lookup_tables'][] = $lookup_table;

			$elements[$number]['relations'][] = array(
				'name' => preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name']),
				'class' => $lookup_table['class'],
				'field' => $elements[$number]['fields'][$field_number]['name'],
			);

		} else {
			$elements[$number]['fields'][$field_number]['method'] = 'Table';

			// Point at table

			$lookup_table = $_POST['dropDownFieldSQLTable'.$number.'Field'.$field_number];

			$key_name = $elements[$number]['table_name'].'_'.$elements[$number]['fields'][$field_number]['name'].'_fk';

			if (strlen($key_name) >64) {
				$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$value);
			}

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table,
			);

			$elements[$number]['relations'][] = array(
				'name' => preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name']),
				'class' => $elements[$number]['fields'][$field_number]['lookup_class'] = $this->findModelClassForTable($lookup_table),
				'field' => $elements[$number]['fields'][$field_number]['name'],
			);
		}

		return $elements;
	}

	public function extraElementFieldWrangling_TextareaWithDropdown($elements, $number, $field_number, $fields_value) {
		// Manually-entered values
		$field_values = array();

		foreach ($_POST as $value_key => $value_value) {
			if (preg_match('/^textAreaDropDownFieldValue'.$number.'Field'.$field_number.'/',$value_key)) {
				$field_values[] = $value_value;
			}
		}

		$lookup_table = array(
			'name' => $elements[$number]['fields'][$field_number]['lookup_table'] = $elements[$number]['table_name'].'_'.preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name'])
		);

		$key_name = $lookup_table['name'].'_fk';

		if (strlen($key_name) >64) {
			$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$value);
		}

		$lookup_table['last_modified_user_key'] = $lookup_table['name'] . '_last_modified_user_id_fk';
		$lookup_table['created_user_key'] = $lookup_table['name'] . '_created_user_id_fk';
		$lookup_table['values'] = $field_values;

		if (strlen($lookup_table['last_modified_user_key']) >64 || strlen($lookup_table['created_user_key']) >64) {
			$lookup_table['last_modified_user_key'] = $lookup_table['name'] . '_lmui_fk';
			$lookup_table['created_user_key'] = $lookup_table['name'] . '_cui_fk';
		}

		$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = str_replace(' ','',ucwords(str_replace('_',' ',$lookup_table['name'])));

		$elements[$number]['lookup_tables'][] = $lookup_table;

		return $elements;
	}

	public function extraElementFieldWrangling_RadioButtons($elements, $number, $field_number, $fields_value) {
		// Radio button fields should end with _id
		if (!preg_match('/_id$/',$fields_value)) {
			$_POST['elementName'.$number.'FieldName'.$field_number] = $elements[$number]['fields'][$field_number]['name'] = $fields_value = $fields_value.'_id';
		}

		if (@$_POST['radioButtonFieldValueTextInputDefault'.$number.'Field'.$field_number]) {
			$elements[$number]['defaults'][$fields_value] = @$_POST['radioButtonFieldValueTextInputDefault'.$number.'Field'.$field_number];
		}

		if (@$_POST['radioButtonMethod'.$number.'Field'.$field_number] == 0) {
			$elements[$number]['fields'][$field_number]['method'] = 'Manual';

			// Manually-entered values
			$field_values = array();

			foreach ($_POST as $value_key => $value_value) {
				if (preg_match('/^radioButtonFieldValue'.$number.'Field'.$field_number.'/',$value_key)) {
					$field_values[] = $value_value;
				}
			}

			$lookup_table = array(
				'name' => $elements[$number]['fields'][$field_number]['lookup_table'] = $elements[$number]['table_name'].'_'.preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name'])
			);

			$key_name = $lookup_table['name'].'_fk';

			if (strlen($key_name) >64) {
				$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$value);
			}

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table['name']
			);

			$lookup_table['last_modified_user_key'] = $lookup_table['name'] . '_last_modified_user_id_fk';
			$lookup_table['created_user_key'] = $lookup_table['name'] . '_created_user_id_fk';
			$lookup_table['values'] = $field_values;

			if (strlen($lookup_table['last_modified_user_key']) >64 || strlen($lookup_table['created_user_key']) >64) {
				$lookup_table['last_modified_user_key'] = $lookup_table['name'] . '_lmui_fk';
				$lookup_table['created_user_key'] = $lookup_table['name'] . '_cui_fk';
			}

			$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = str_replace(' ','',ucwords(str_replace('_',' ',$lookup_table['name'])));

			$elements[$number]['lookup_tables'][] = $lookup_table;

			$elements[$number]['relations'][] = array(
				'name' => preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name']),
				'class' => $lookup_table['class'],
				'field' => $elements[$number]['fields'][$field_number]['name'],
			);

		} else {
			$elements[$number]['fields'][$field_number]['method'] = 'Table';

			// Point at table

			$lookup_table = $_POST['radioButtonFieldSQLTable'.$number.'Field'.$field_number];

			$elements[$number]['fields'][$field_number]['lookup_table'] = $lookup_table;

			$key_name = $elements[$number]['table_name'].'_'.$elements[$number]['fields'][$field_number]['name'].'_fk';

			if (strlen($key_name) >64) {
				$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$value);
			}

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table,
			);

			$elements[$number]['relations'][] = array(
				'name' => preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name']),
				'class' => $elements[$number]['fields'][$field_number]['lookup_class'] = $this->findModelClassForTable($lookup_table),
				'field' => $elements[$number]['fields'][$field_number]['name'],
			);
		}

		return $elements;
	}

	public function findModelClassForTable($table, $path=false) {
		if (!$path) {
			$path = Yii::app()->basePath.'/models';
		}

		$dh = opendir($path);

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				if (is_dir($path.'/'.$file)) {
					if ($class = $this->findModelClassForTable($table, $path.'/'.$file)) {
						return $class;
					}
				} else {
					if (preg_match('/\.php$/',$file)) {
						$blob = file_get_contents($path.'/'.$file);

						if (preg_match('/public function tableName\(\).*?\{.*?return \'(.*?)\';/s',$blob,$m)) {
							if ($m[1] == $table) {
								return preg_replace('/\.php$/','',$file);
							}
						}
					}
				}
			}
		}

		closedir($dh);

		if ($path == Yii::app()->basePath.'/models') {
			$path = Yii::app()->basePath.'/modules';

			$dh = opendir($path);

			while ($file = readdir($dh)) {
				if (!preg_match('/^\.\.?$/',$file)) {
					if (file_exists($path.'/'.$file.'/models')) {
						if ($class = $this->findModelClassForTable($table, $path.'/'.$file.'/models')) {
							return $class;
						}
					}
				}
			}

			closedir($dh);
		}

		return false;
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
		} elseif ($type == 'Textarea with dropdown') {
			$sql = "'{$name}' => 'text NOT NULL', // {$label}\n";
		} elseif ($type == 'Checkbox') {
			$sql = "'{$name}' => 'tinyint(1) unsigned NOT NULL', // {$label}\n";
		} elseif ($type == 'Radio buttons') {
			$sql = "'{$name}' => 'int(10) unsigned NOT NULL', // {$label}\n";
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
		if (isset($_GET['ajax']) && preg_match('/^[a-zA-Z_]+$/',$_GET['ajax'])) {
			if ($_GET['ajax'] == 'table_fields') {
				EventTypeModuleCode::dump_table_fields($_GET['table']);
			} else if ($_GET['ajax'] == 'field_unique_values') {
				EventTypeModuleCode::dump_field_unique_values($_GET['table'],$_GET['field']);
			} else if ($_GET['ajax'] == 'getEyedrawSize') {
				EventTypeModuleCode::getEyedrawSize($_GET['class']);
			} else {
				Yii::app()->getController()->renderPartial($_GET['ajax'],$_GET);
			}
			exit;
		}

		if (!empty($_POST)) {
			$this->validate_form();
		}

		parent::init();
	}

	static public function dump_table_fields($table, $selected=false) {
		echo '<option value="">- Please select a field -</option>';

		$tableSchema = Yii::app()->getDb()->getSchema()->getTable($table);

		$columns = $tableSchema->getColumnNames();
		sort($columns);

		foreach ($columns as $column) {
			$schema = $tableSchema->getColumn($column);

			if (preg_match('/^varchar/',$schema->dbType) && $column != 'parent_class') {
				echo '<option value="'.$column.'"'.($selected == $column ? ' selected="selected"' : '').'>'.$column.'</option>';
			}
		}
	}

	static public function dump_field_unique_values($table, $field, $selected=false) {
		echo '<option value="">- No default value -</option>';

		foreach (Yii::app()->db->createCommand()
			->selectDistinct("$table.id, $table.$field")
			->from($table)
			->order("$table.$field")
			->queryAll() as $row) {
			echo '<option value="'.$row['id'].'"'.($selected == $row['id'] ? ' selected="selected"' : '').'>'.$row[$field].'</option>';
		}
	}

	static public function getEyedrawSize($class) {
		if (file_exists(Yii::app()->basePath.'/modules/eyedraw/OEEyeDrawWidget'.$class.'.php')) {
			foreach (@file(Yii::app()->basePath.'/modules/eyedraw/OEEyeDrawWidget'.$class.'.php') as $line) {
				if (preg_match('/public[\s\t]+\$size[\s\t]*=[\s\t]*([0-9]+)/',$line,$m)) {
					return $m[1];
				}
			}
		}
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

			if (preg_match('/^elementType([0-9]+)FieldType([0-9]+)$/',$key,$m)) {
				if ($value == 'Dropdown list') {
					if (!isset($_POST['dropDownMethod'.$m[1].'Field'.$m[2]])) {
						$errors['dropDownMethod'.$m[1].'Field'.$m[2]] = "Please select a dropdown list method";
					}
				}
			}

			if (preg_match('/^dropDownMethod([0-9]+)Field([0-9]+)$/',$key,$m)) {
				if ($value == 1) {
					if (!@$_POST['dropDownFieldSQLTable'.$m[1].'Field'.$m[2]]) {
						$errors['dropDownFieldSQLTable'.$m[1].'Field'.$m[2]] = "Please select a table";
					}
					if (!@$_POST['dropDownFieldSQLTableField'.$m[1].'Field'.$m[2]]) {
						$errors['dropDownFieldSQLTableField'.$m[1].'Field'.$m[2]] = "Please select a field";
					}
				}
			}
		}

		Yii::app()->getController()->form_errors = $errors;
	}
}
