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
				if(CFileHelper::getExtension($file)==='php' || CFileHelper::getExtension($file)==='js') {
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
					} elseif (preg_match('/MAPPINGTABLE/',$file)) {
						foreach ($this->getElementsFromPost() as $element) {
							foreach ($element['mapping_tables'] as $mapping_table) {
								$destination_file = preg_replace('/MAPPINGTABLE/',$mapping_table['class'],$file);
								$content = $this->render($file, array('mapping_table'=>$mapping_table));
								$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
							}
						}
					} elseif (preg_match('/DEFAULTSTABLE/',$file)) {
						foreach ($this->getElementsFromPost() as $element) {
							foreach ($element['defaults_tables'] as $defaults_table) {
								$destination_file = preg_replace('/DEFAULTSTABLE/',$defaults_table['class'],$file);
								$content = $this->render($file, array('defaults_table'=>$defaults_table));
								$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
							}
						}
					} elseif (preg_match('/\.js$/',$file)) {
						$content=$this->render($file,array('elements'=>$this->getElementsFromPost()));
						$this->files[]=new CCodeFile($modulePath.substr($destination_file,strlen($templatePath)), $content);
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
				$elements[$number]['class_name'] = 'Element_'.$this->moduleID.'_'.preg_replace("/ /", "", ucwords(strtolower($value)));
				$elements[$number]['table_name'] = 'et_' . strtolower($this->moduleID) . '_' . strtolower(preg_replace("/ /", "", $value));;
				$elements[$number]['number'] = $number;
				$elements[$number]['foreign_keys'] = array();
				$elements[$number]['lookup_tables'] = array();
				$elements[$number]['defaults_tables'] = array();
				$elements[$number]['relations'] = array();
				$elements[$number]['defaults'] = array();
				$elements[$number]['mapping_tables'] = array();
				$elements[$number]['defaults_methods'] = array();
				$elements[$number]['after_save'] = array();

				$elements[$number] = $this->generateKeyNames($elements[$number],array('lmui','cui','ev'));

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

						if ($elements[$number]['fields'][$field_number]['type'] == 'EyeDraw') {
							$elements = $this->extraElementFieldWrangling_EyeDraw($elements, $number, $field_number, $fields_value);
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'Multi select') {
							$elements = $this->extraElementFieldWrangling_MultiSelect($elements, $number, $field_number, $fields_value);
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
				if (preg_match('/^dropDownFieldValue'.$number.'Field'.$field_number.'_/',$value_key)) {
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

			$lookup_table = $this->generateKeyNames($lookup_table,array('lmui','cui'));

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table['name']
			);

			$lookup_table['values'] = $field_values;
			$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = str_replace(' ','',ucwords(str_replace('_',' ',$lookup_table['name'])));

			$elements[$number]['lookup_tables'][] = $lookup_table;

			$elements[$number]['relations'][] = array(
				'type' => 'BELONGS_TO',
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
				'type' => 'BELONGS_TO',
				'name' => preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name']),
				'class' => $elements[$number]['fields'][$field_number]['lookup_class'] = EventTypeModuleCode::findModelClassForTable($lookup_table),
				'field' => $elements[$number]['fields'][$field_number]['name'],
			);
		}

		return $elements;
	}

	public function extraElementFieldWrangling_TextareaWithDropdown($elements, $number, $field_number, $fields_value) {
		// Manually-entered values
		$field_values = array();

		foreach ($_POST as $value_key => $value_value) {
			if (preg_match('/^textAreaDropDownFieldValue'.$number.'Field'.$field_number.'_/',$value_key)) {
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

		$lookup_table = $this->generateKeyNames($lookup_table,array('lmui','cui'));

		$lookup_table['values'] = $field_values;
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
				if (preg_match('/^radioButtonFieldValue'.$number.'Field'.$field_number.'_/',$value_key)) {
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

			$lookup_table = $this->generateKeyNames($lookup_table,array('lmui','cui'));

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table['name']
			);

			$lookup_table['values'] = $field_values;
			$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = str_replace(' ','',ucwords(str_replace('_',' ',$lookup_table['name'])));

			$elements[$number]['lookup_tables'][] = $lookup_table;

			$elements[$number]['relations'][] = array(
				'type' => 'BELONGS_TO',
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
				'type' => 'BELONGS_TO',
				'name' => preg_replace('/_id$/','',$elements[$number]['fields'][$field_number]['name']),
				'class' => $elements[$number]['fields'][$field_number]['lookup_class'] = EventTypeModuleCode::findModelClassForTable($lookup_table),
				'field' => $elements[$number]['fields'][$field_number]['name'],
			);
		}

		return $elements;
	}

	public function extraElementFieldWrangling_EyeDraw($elements, $number, $field_number, $fields_value) {
		$elements[$number]['fields'][$field_number]['eyedraw_class'] = @$_POST['eyedrawClass'.$number.'Field'.$field_number];
		$elements[$number]['fields'][$field_number]['eyedraw_size'] = @$_POST['eyedrawSize'.$number.'Field'.$field_number];
		$elements[$number]['add_selected_eye'] = true;

		if (@$_POST['eyedrawExtraReport'.$number.'Field'.$field_number]) {
			$elements[$number]['fields'][$field_number]['extra_report'] = true;
		}

		return $elements;
	}

	public function extraElementFieldWrangling_MultiSelect($elements, $number, $field_number, $fields_value) {
		if (@$_POST['multiSelectMethod'.$number.'Field'.$field_number] == 0) {
			$elements[$number]['fields'][$field_number]['method'] = 'Manual';

			// Manually-entered values
			$field_values = array();

			foreach ($_POST as $value_key => $value_value) {
				if (preg_match('/^multiSelectFieldValue'.$number.'Field'.$field_number.'_/',$value_key)) {
					$field_values[] = $value_value;
				}
			}

			$lookup_table = array(
				'name' => $elements[$number]['fields'][$field_number]['lookup_table'] = $elements[$number]['table_name'].'_'.$elements[$number]['fields'][$field_number]['name']
			);

			$lookup_table['values'] = $field_values;
			$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = str_replace(' ','',ucwords(str_replace('_',' ',$lookup_table['name'])));
			$lookup_table['defaults'] = array();

			$lookup_table = $this->generateKeyNames($lookup_table,array('lmui','cui'));

			foreach ($_POST as $key => $value) {
				if (preg_match('/^multiSelectFieldValueTextInputDefault([0-9]+)Field([0-9]+)_([0-9]+)$/',$key,$m) && $m[1] == $number && $m[2] == $field_number && $value == 1) {
					$lookup_table['defaults'][] = $m[3];
				}
			}

			$elements[$number]['lookup_tables'][] = $lookup_table;
			$elements[$number]['defaults_methods'][] = array(
				'method' => $lookup_table['name'].'_defaults',
				'class' => $lookup_table['class'],
			);

			$mapping_table = array(
				'name' => $elements[$number]['table_name'].'_'.$elements[$number]['fields'][$field_number]['name'].'_'.$elements[$number]['fields'][$field_number]['name'],
				'lookup_table' => $lookup_table['name'],
				'lookup_class' => $lookup_table['class'],
				'element_class' => $elements[$number]['class_name'],
			);

			$mapping_table['class'] = str_replace(' ','',ucwords(str_replace('_',' ',$mapping_table['name'])));

			$mapping_table = $this->generateKeyNames($mapping_table,array('lmui','cui','ele','lku'));

			$elements[$number]['mapping_tables'][] = $mapping_table;

			$elements[$number]['relations'][] = array(
				'type' => 'HAS_MANY',
				'name' => $elements[$number]['fields'][$field_number]['name'].'s',
				'class' => str_replace(' ','',ucwords(str_replace('_',' ',$mapping_table['name']))),
				'field' => 'element_id',
			);

			$elements[$number]['fields'][$field_number]['multiselect_relation'] = $elements[$number]['fields'][$field_number]['name'].'s';
			$elements[$number]['fields'][$field_number]['multiselect_field'] = $lookup_table['name'].'_id';
			$elements[$number]['fields'][$field_number]['multiselect_lookup_class'] = $lookup_table['class'];
			$elements[$number]['fields'][$field_number]['multiselect_lookup_table'] = $lookup_table['name'];
			$elements[$number]['fields'][$field_number]['multiselect_table_field_name'] = 'name';
			$elements[$number]['fields'][$field_number]['multiselect_order_field'] = 'display_order';

			$elements[$number]['after_save'][] = array(
				'type' => 'MultiSelect',
				'post_var' => 'MultiSelect_'.$elements[$number]['fields'][$field_number]['name'],
				'mapping_table_class' => $mapping_table['class'],
				'lookup_table_field_id' => $lookup_table['name'].'_id',
			);

		} else {
			$elements[$number]['fields'][$field_number]['method'] = 'Table';

			$lookup_table = array(
				'name' => $elements[$number]['fields'][$field_number]['lookup_table'] = @$_POST['multiSelectFieldSQLTable'.$number.'Field'.$field_number],
			);

			$lookup_table['class'] = EventTypeModuleCode::findModelClassForTable($lookup_table['name']);

			if (@$_POST['multiSelectFieldValueDefaults'.$number.'Field'.$field_number]) {
				$defaults = @$_POST['multiSelectFieldValueDefaults'.$number.'Field'.$field_number];
			} else {
				$defaults = array();
			}

			$defaults_table = array(
				'name' => $elements[$number]['table_name'].'_'.$lookup_table['name'].'_defaults',
				'method' => $lookup_table['name'].'_defaults',
				'values' => $defaults,
			);

			$defaults_table['class'] = str_replace(' ','',ucwords(str_replace('_',' ',$defaults_table['name'])));

			$defaults_table = $this->generateKeyNames($defaults_table,array('lmui','cui'));

			$elements[$number]['defaults_tables'][] = $defaults_table;

			$elements[$number]['defaults_methods'][] = array(
				'method' => $lookup_table['name'].'_defaults',
				'class' => $defaults_table['class'],
				'is_defaults_table' => true,
			);

			$mapping_table = array(
				'name' => $elements[$number]['table_name'].'_'.$elements[$number]['fields'][$field_number]['name'].'_'.$elements[$number]['fields'][$field_number]['name'],
				'lookup_table' => $lookup_table['name'],
				'lookup_class' => $lookup_table['class'],
				'element_class' => $elements[$number]['class_name'],
			);

			$mapping_table['class'] = str_replace(' ','',ucwords(str_replace('_',' ',$mapping_table['name'])));

			$mapping_table = $this->generateKeyNames($mapping_table,array('lmui','cui','ele','lku'));

			$elements[$number]['mapping_tables'][] = $mapping_table;

			$elements[$number]['relations'][] = array(
				'type' => 'HAS_MANY',
				'name' => $elements[$number]['fields'][$field_number]['name'].'s',
				'class' => str_replace(' ','',ucwords(str_replace('_',' ',$mapping_table['name']))),
				'field' => 'element_id',
			);

			$elements[$number]['fields'][$field_number]['multiselect_relation'] = $elements[$number]['fields'][$field_number]['name'].'s';
			$elements[$number]['fields'][$field_number]['multiselect_field'] = $lookup_table['name'].'_id';
			$elements[$number]['fields'][$field_number]['multiselect_lookup_class'] = $lookup_table['class'];
			$elements[$number]['fields'][$field_number]['multiselect_lookup_table'] = $lookup_table['name'];
			$elements[$number]['fields'][$field_number]['multiselect_table_field_name'] = @$_POST['multiSelectFieldSQLTableField'.$number.'Field'.$field_number];
			$elements[$number]['fields'][$field_number]['multiselect_order_field'] = @$_POST['multiSelectFieldSQLTableField'.$number.'Field'.$field_number];

			$elements[$number]['after_save'][] = array(
				'type' => 'MultiSelect',
				'post_var' => 'MultiSelect_'.$elements[$number]['fields'][$field_number]['name'],
				'mapping_table_class' => $mapping_table['class'],
				'lookup_table_field_id' => $lookup_table['name'].'_id',
			);
		}

		return $elements;
	}

	public function generateKeyNames($table, $keys) {
		foreach ($keys as $key) {
			$table[$key.'_key'] = $table['name'].'_'.$key.'_fk';

			if (strlen($table[$key.'_key']) >64) {
				$ex = explode('_',$table['name']);
				$table[$key.'_key'] = array_shift($ex).'_';

				foreach ($ex as $segment) {
					$table[$key.'_key'] .= $segment[0];
				}

				$table[$key.'_key'] .= '_'.$key.'_fk';
			}
		}

		return $table;
	}

	static public function findModelClassForTable($table, $path=false) {
		if (!$path) {
			$path = Yii::app()->basePath.'/models';
		}

		$dh = opendir($path);

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				if (is_dir($path.'/'.$file)) {
					if ($class = EventTypeModuleCode::findModelClassForTable($table, $path.'/'.$file)) {
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
						if ($class = EventTypeModuleCode::findModelClassForTable($table, $path.'/'.$file.'/models')) {
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

	public function renderDBField($field) {
		$sql = '';
		if ($field['type'] == 'Textbox') {
			$sql = "'{$field['name']}' => 'varchar(255) DEFAULT \'\'', // {$field['label']}\n";
		} elseif ($field['type'] == 'Textarea') {
			$sql = "'{$field['name']}' => 'text DEFAULT \'\'', // {$field['label']}\n";
		} elseif ($field['type'] == 'Date picker') {
			// $sql = "'{$field['name']}' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'', // {$field['label']}\n";
			$sql = "'{$field['name']}' => 'date DEFAULT NULL', // {$field['label']}\n";
		} elseif ($field['type'] == 'Dropdown list') {
			$sql = "'{$field['name']}' => 'int(10) unsigned NOT NULL', // {$field['label']}\n";
		} elseif ($field['type'] == 'Textarea with dropdown') {
			$sql = "'{$field['name']}' => 'text NOT NULL', // {$field['label']}\n";
		} elseif ($field['type'] == 'Checkbox') {
			$sql = "'{$field['name']}' => 'tinyint(1) unsigned NOT NULL', // {$field['label']}\n";
		} elseif ($field['type'] == 'Radio buttons') {
			$sql = "'{$field['name']}' => 'int(10) unsigned NOT NULL', // {$field['label']}\n";
		} elseif ($field['type'] == 'Boolean') {
			$sql = "'{$field['name']}' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // {$field['label']}\n";
		} elseif ($field['type'] == 'EyeDraw') {
			// we create two fields for eyedraw: one for json, and one for the report
			$sql = "'{$field['name']}' => 'varchar(4096) COLLATE utf8_bin NOT NULL',// {$field['label']} (eyedraw)\n";
			if (@$field['extra_report']) {
				$sql .= "'{$field['name']}2' => 'varchar(4096) COLLATE utf8_bin NOT NULL',// {$field['label']} (eyedraw)\n";
			}
		} elseif ($field['type'] == 'Multi select') {
			// Nothing, this is stored in additional tables
		}
		return $sql;
	}

	public function init() {
		if (isset($_GET['ajax']) && preg_match('/^[a-zA-Z_]+$/',$_GET['ajax'])) {
			if ($_GET['ajax'] == 'table_fields') {
				EventTypeModuleCode::dump_table_fields($_GET['table']);
			} else if ($_GET['ajax'] == 'field_unique_values') {
				EventTypeModuleCode::dump_field_unique_values($_GET['table'],$_GET['field']);
			} else if ($_GET['ajax'] == 'dump_field_unique_values_multi') {
				EventTypeModuleCode::dump_field_unique_values_multi($_GET['table'],$_GET['field']);
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

	static public function dump_field_unique_values_multi($table, $field, $selected=false) {
		if (!$selected) $selected = array();

		echo '<option value="">- Select default values -</option>';

		foreach (Yii::app()->db->createCommand()
			->selectDistinct("$table.id, $table.$field")
			->from($table)
			->order("$table.$field")
			->queryAll() as $row) {
			if (!in_array($row['id'],$selected)) {
				echo '<option value="'.$row['id'].'">'.$row[$field].'</option>';
			}
		}
	}

	static public function getEyedrawSize($class) {
		if (file_exists(Yii::app()->basePath.'/modules/eyedraw/OEEyeDrawWidget'.$class.'.php')) {
			foreach (@file(Yii::app()->basePath.'/modules/eyedraw/OEEyeDrawWidget'.$class.'.php') as $line) {
				if (preg_match('/public[\s\t]+\$size[\s\t]*=[\s\t]*([0-9]+)/',$line,$m)) {
					echo $m[1];
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
				if ($value == 'EyeDraw') {
					if (!@$_POST['eyedrawClass'.$m[1].'Field'.$m[2]]) {
						$errors['eyedrawClass'.$m[1].'Field'.$m[2]] = "Please select an eyedraw type";
					}
					if (!@$_POST['eyedrawSize'.$m[1].'Field'.$m[2]]) {
						$errors['eyedrawSize'.$m[1].'Field'.$m[2]] = "Please enter a size (in pixels)";
					} else if (!ctype_digit(@$_POST['eyedrawSize'.$m[1].'Field'.$m[2]])) {
						$errors['eyedrawSize'.$m[1].'Field'.$m[2]] = "Size must be specified as a number of pixels";
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
