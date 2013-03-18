<?php
class EventTypeModuleCode extends BaseModuleCode // CCodeModel
{
	public $moduleID;
	public $moduleShortID;
	public $moduleSuffix;
	public $moduleShortSuffix;
	public $eventGroupName;
	public $template = "default";
	public $form_errors = array();
	public $mode;
	public $moduleTemplateFile;
	public $files_to_process;
	public $event_type;
	public $event_group;
	public $specialty;
	public $cssPath;
	public $jsPath;
	public $imgPath;
	public $validation_rules_path = "protected/gii/EventTypeModule/validation";
	public $validation_rules = array();

	public function __construct() {
		$dh = dir($this->validation_rules_path);

		while ($file = $dh->read()) {
			if (!preg_match('/^\.\.?$/',$file)) {
				$this->validation_rules = array_merge($this->validation_rules,require($this->validation_rules_path."/$file"));
			}
		}
		parent::__construct();
	}

	public function rules() {
		return array(
			array('moduleSuffix', 'required'),
			array('moduleSuffix', 'safe'),
			array('moduleShortSuffix', 'required'),
			array('moduleShortSuffix', 'safe'),
		);
	}

	public function initialise($properties=false) {
		if ($properties) {
			foreach ($properties as $key => $value) {
				$this->{$key} = $value;
			}
		}
		
		parent::prepare();

		$this->files = array();
		$this->moduleTemplateFile = $this->templatePath.DIRECTORY_SEPARATOR.'module.php';
		$this->files[] = new CCodeFile($this->modulePath.'/'.$this->moduleClass.'.php', $this->render($this->moduleTemplateFile));
		$this->files_to_process = CFileHelper::findFiles($this->templatePath,array('exclude'=>array('.svn')));
	}

	public function prepare() {
		if ($this->mode == 'create') {
			$this->initialise(array(
				'moduleID' => ucfirst(strtolower(Specialty::model()->findByPk($_REQUEST['Specialty']['id'])->code)) . 
					ucfirst(strtolower(EventGroup::model()->findByPk($_REQUEST['EventGroup']['id'])->code)) . 
					Yii::app()->request->getQuery('Specialty[id]') . 
					preg_replace("/ /", "", ucfirst(strtolower($this->moduleSuffix))),
				'moduleShortID' => ucfirst(strtolower(Specialty::model()->findByPk($_REQUEST['Specialty']['id'])->code)) .
					ucfirst(strtolower(EventGroup::model()->findByPk($_REQUEST['EventGroup']['id'])->code)) .
					Yii::app()->request->getQuery('Specialty[id]') .
					preg_replace("/ /", "", ucfirst(strtolower($this->moduleShortSuffix))),
				'eventGroupName' => EventGroup::model()->findByPk($_REQUEST['EventGroup']['id'])->name,
			));
		} else if ($this->mode == 'update') {
			$this->event_type = EventType::model()->findByPk(@$_POST['EventTypeModuleEventType']);
			
			$short_suffix = $this->getEventShortName($this->event_type);
			
			$this->initialise(array(
				'moduleID' => $this->event_type->class_name,
				'moduleShortID' => Specialty::model()->findByPk($_REQUEST['Specialty']['id'])->code .
					EventGroup::model()->findByPk($_REQUEST['EventGroup']['id'])->code . 
					$short_suffix,
				'event_group' => EventGroup::model()->findByPk($_REQUEST['EventGroup']['id']),
				'specialty' => Specialty::model()->findByPk($_REQUEST['Specialty']['id']),
				'eventGroupName' => $this->event_type->name,
			));
		}
		
		if ($this->mode == 'update') {
			$current_class = $this->event_type->class_name;
			$target_class = Yii::app()->getController()->target_class = ucfirst(strtolower($this->specialty->code)) . ucfirst(strtolower($this->event_group->code)) . Yii::app()->request->getQuery('Specialty[id]') . preg_replace("/ /", "", ucfirst(strtolower($this->moduleSuffix)));
			if (@$_POST['generate'] == 'Generate') {
				$this->handleViewChanges();

				if ($current_class != $target_class) {
					$this->handleModuleNameChange($current_class, $target_class);
				}
			}
		}

		$elements = $this->getElementsFromPost();

		foreach($this->files_to_process as $file) {
			$destination_file = preg_replace("/EVENTNAME|EVENTTYPENAME|MODULENAME/", $this->moduleID, $file);
			if($file!==$this->moduleTemplateFile) {
				if(CFileHelper::getExtension($file)==='php' || CFileHelper::getExtension($file)==='js') {
					if (preg_match("/\/migrations\//", $file)) {
						if (preg_match('/_create\.php$/',$file) && $this->mode == 'create') {
							# $matches = Array();
							if (file_exists($this->modulePath.'/migrations/') and ($matches = $this->regExpFile("/m([0-9]+)\_([0-9]+)\_event_type_".$this->moduleID."/",$this->modulePath.'/migrations/'))) {
								// migration file exists, so overwrite it rather than creating a new timestamped file
								$migrationid = $matches[1] . '_' . $matches[2];
							} else {
								$migrationid = gmdate('ymd_His');
							}
							$destination_file = preg_replace("/\/migrations\//", '/migrations/m'.$migrationid.'_', preg_replace('/_create/','',$destination_file));
							$content=$this->renderMigrations($file, $migrationid);
							$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
						} else if (preg_match('/_update\.php$/',$file) && $this->mode == 'update') {
							$elements_have_changed = false;
							foreach ($_POST as $key => $value) {
								if (preg_match('/^elementName[0-9]+$/',$key) || preg_match('/^elementId[0-9]+$/',$key)) {
									$elements_have_changed = true;
								}
							}

							if ($elements_have_changed) {
								$migrationid = gmdate('ymd_His');
								$destination_file = preg_replace("/\/migrations\//", '/migrations/m'.$migrationid.'_', preg_replace('/_update/','',$destination_file));
								$content=$this->renderMigrations($file, $migrationid);
								$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
							}
						}
					} elseif (preg_match("/ELEMENTNAME|ELEMENTTYPENAME/", $file)) {
						foreach ($elements as $element) {
							$destination_file = preg_replace("/ELEMENTNAME|ELEMENTTYPENAME/", $element['class_name'], $file);
							$content = $this->render($file, array('element'=>$element));
							$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
						}
					} elseif (preg_match('/LOOKUPTABLE/',$file)) {
						foreach ($elements as $element) {
							foreach ($element['lookup_tables'] as $lookup_table) {
								$destination_file = preg_replace('/LOOKUPTABLE/',$lookup_table['class'],$file);
								$content = $this->render($file, array('lookup_table'=>$lookup_table));
								$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
							}
						}
					} elseif (preg_match('/MAPPINGTABLE/',$file)) {
						foreach ($elements as $element) {
							foreach ($element['mapping_tables'] as $mapping_table) {
								$destination_file = preg_replace('/MAPPINGTABLE/',$mapping_table['class'],$file);
								$content = $this->render($file, array('mapping_table'=>$mapping_table));
								$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
							}
						}
					} elseif (preg_match('/DEFAULTSTABLE/',$file)) {
						foreach ($elements as $element) {
							foreach ($element['defaults_tables'] as $defaults_table) {
								$destination_file = preg_replace('/DEFAULTSTABLE/',$defaults_table['class'],$file);
								$content = $this->render($file, array('defaults_table'=>$defaults_table));
								$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
							}
						}
					} elseif (preg_match('/\.js$/',$file)) {
						$content=$this->render($file,array('elements'=>$elements));
						$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
					} else {
						$content=$this->render($file);
						$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
					}
				// an empty directory
				} else if(basename($file)==='.yii') {
					$file=dirname($file);
					$content=null;
				} else {
					$content=file_get_contents($file);
					$this->files[]=new CCodeFile($this->modulePath.substr($destination_file,strlen($this->templatePath)), $content);
				}
			}
		}
	}	

	function changeAllInstancesOfString($path, $from, $to) {
		$dh = opendir($path);

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				if (strstr($file,$from)) {
					$target = str_replace($from,$to,$file);
					@rename($path.'/'.$file,$path.'/'.$target);
					$file = $target;
				}

				if (is_file($path.'/'.$file)) {
					file_put_contents($path.'/'.$file,str_replace($from,$to,file_get_contents($path.'/'.$file)));
				} else if (is_dir($path.'/'.$file)) {
					$this->changeAllInstancesOfString($path.'/'.$file,$from,$to);
				}
			}
		}

		closedir($dh);
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
			if (preg_match('/^elementName([0-9]+)$/',$key, $matches) || preg_match('/^elementId([0-9]+)$/',$key,$matches)) {
				$field = $matches[0]; $number = $matches[1]; $name = $value;

				if (preg_match('/^elementName([0-9]+)$/',$key, $matches)) {
					$elements[$number]['mode'] = 'create';
					$elements[$number]['name'] = $value;
				} else {
					$elements[$number]['mode'] = 'update';
					$elements[$number]['id'] = $value;

					$element_type = ElementType::model()->findByPk($value);
					
					$element_class = $element_type->class_name;
					
					$elements[$number]['name'] = $value = $element_type->name;
					$elements[$number]['table_name'] = $element_class::model()->tableName();
					$field = 'elementName'.$number;
				}

				$elements[$number]['class_name'] = 'Element_'.$this->moduleID.'_'.preg_replace("/ /", "", ucwords(strtolower($value)));
				# now using the shortname field attribute for the table name
				if ($elements[$number]['mode'] == 'create') {
					$elements[$number]['table_name'] = 'et_' . strtolower($this->moduleShortID) . '_' . strtolower(preg_replace("/ /", "", $_POST["elementShortName".$number]));
				}
				
				$elements[$number]['number'] = $number;
				$elements[$number]['fields'] = array();
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
							$elements[$number]['fields'][$field_number]['textarea_rows'] = @$_POST['textAreaDropDownRows'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['textarea_cols'] = @$_POST['textAreaDropDownCols'.$number.'Field'.$field_number];
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'Radio buttons') {
							$elements = $this->extraElementFieldWrangling_RadioButtons($elements, $number, $field_number, $fields_value);
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'EyeDraw') {
							$elements = $this->extraElementFieldWrangling_EyeDraw($elements, $number, $field_number, $fields_value);
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'EyeDraw2') {
							$elements = $this->extraElementFieldWrangling_EyeDraw2($elements, $number, $field_number, $fields_value);
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'Multi select') {
							$elements = $this->extraElementFieldWrangling_MultiSelect($elements, $number, $field_number, $fields_value);
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'Slider') {
							$elements[$number]['fields'][$field_number]['slider_min_value'] = @$_POST['sliderMinValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['slider_max_value'] = @$_POST['sliderMaxValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['slider_default_value'] = @$_POST['sliderDefaultValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['default_value'] = @$_POST['sliderDefaultValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['slider_stepping'] = @$_POST['sliderStepping'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['slider_dp'] = @$_POST['sliderForceDP'.$number.'Field'.$field_number];
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'Integer') {
							$elements[$number]['fields'][$field_number]['integer_min_value'] = @$_POST['integerMinValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['integer_max_value'] = @$_POST['integerMaxValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['integer_default_value'] = @$_POST['integerDefaultValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['default_value'] = @$_POST['integerDefaultValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['integer_size'] = @$_POST['integerSize'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['integer_max_length'] = @$_POST['integerMaxLength'.$number.'Field'.$field_number];
						}
						if ($elements[$number]['fields'][$field_number]['type'] == 'Decimal') {
							$elements[$number]['fields'][$field_number]['decimal_min_value'] = @$_POST['decimalMinValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['decimal_max_value'] = @$_POST['decimalMaxValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['default_value'] = @$_POST['decimalDefaultValue'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['decimal_size'] = @$_POST['decimalSize'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['decimal_max_length'] = @$_POST['decimalMaxLength'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['decimal_dp'] = @$_POST['decimalForceDP'.$number.'Field'.$field_number];
						}
						if ($elements[$number]['fields'][$field_number]['type'] == 'Textbox') {
							$elements[$number]['fields'][$field_number]['textbox_size'] = @$_POST['textBoxSize'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['textbox_max_length'] = @$_POST['textBoxMaxLength'.$number.'Field'.$field_number];
						}

						if ($elements[$number]['fields'][$field_number]['type'] == 'Textarea') {
							$elements[$number]['fields'][$field_number]['textarea_rows'] = @$_POST['textAreaRows'.$number.'Field'.$field_number];
							$elements[$number]['fields'][$field_number]['textarea_cols'] = @$_POST['textAreaCols'.$number.'Field'.$field_number];
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
		
		// but for class naming/lookups we don't want the id:
		$root_fields_value = preg_replace('/_id$/', '', $fields_value);

		$elements[$number]['fields'][$field_number]['empty'] = @$_POST['dropDownUseEmpty'.$number.'Field'.$field_number];

		if (@$_POST['dropDownFieldValueTextInputDefault'.$number.'Field'.$field_number]) {
			$elements[$number]['fields'][$field_number]['default_value'] = @$_POST['dropDownFieldValueTextInputDefault'.$number.'Field'.$field_number];
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
				'name' => $elements[$number]['fields'][$field_number]['lookup_table'] = preg_replace('/^et_/','',$elements[$number]['table_name'].'_'.$root_fields_value),
			);

			$key_name = $lookup_table['name'].'_fk';

			if (strlen($key_name) >64) {
				$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$root_fields_value);
			}

			$lookup_table = $this->generateKeyNames($lookup_table,array('lmui','cui'));

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table['name']
			);

			$lookup_table['values'] = $field_values;
			$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = $elements[$number]['class_name'] . '_' . str_replace(' ','', ucwords(str_replace('_', ' ', $root_fields_value) ) );
			$elements[$number]['fields'][$field_number]['lookup_field'] = 'name';
			$elements[$number]['fields'][$field_number]['order_field'] = 'display_order';

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
				$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$fields_value);
			}

			$elements[$number]['fields'][$field_number]['lookup_field'] = $elements[$number]['fields'][$field_number]['order_field'] = @$_POST['dropDownFieldSQLTableField'.$number.'Field'.$field_number];

			if (@$_POST['dropDownFieldValueTextInputDefault'.$number.'Field'.$field_number]) {
				$elements[$number]['fields'][$field_number]['default_value'] = @$_POST['dropDownFieldValueTextInputDefault'.$number.'Field'.$field_number];
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
			$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$fields_value);
		}

		$lookup_table = $this->generateKeyNames($lookup_table,array('lmui','cui'));

		$lookup_table['values'] = $field_values;
		$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = $elements[$number]['class_name'] . '_' . str_replace(' ','',ucwords(str_replace('_',' ', $fields_value)) );
		$elements[$number]['fields'][$field_number]['lookup_field'] = 'name';

		$elements[$number]['lookup_tables'][] = $lookup_table;

		return $elements;
	}

	public function extraElementFieldWrangling_RadioButtons($elements, $number, $field_number, $fields_value) {
		// Radio button fields should end with _id
		if (!preg_match('/_id$/',$fields_value)) {
			$_POST['elementName'.$number.'FieldName'.$field_number] = $elements[$number]['fields'][$field_number]['name'] = $fields_value = $fields_value.'_id';
		}
		// but for class naming/lookups we don't want the id:
		$root_fields_value = preg_replace('/_id$/', '', $fields_value);
		
		if (@$_POST['radioButtonFieldValueTextInputDefault'.$number.'Field'.$field_number]) {
			$elements[$number]['fields'][$field_number]['default_value'] = @$_POST['radioButtonFieldValueTextInputDefault'.$number.'Field'.$field_number];
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
				'name' => $elements[$number]['fields'][$field_number]['lookup_table'] = $elements[$number]['table_name'].'_'.$root_fields_value
			);

			$key_name = $lookup_table['name'].'_fk';

			if (strlen($key_name) >64) {
				$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$root_fields_value);
			}

			$lookup_table = $this->generateKeyNames($lookup_table,array('lmui','cui'));

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table['name']
			);

			$lookup_table['values'] = $field_values;
			$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = $elements[$number]['class_name'] . '_' . str_replace(' ','', ucwords(str_replace('_', ' ', $root_fields_value) ) );

			$elements[$number]['lookup_tables'][] = $lookup_table;

			$elements[$number]['relations'][] = array(
				'type' => 'BELONGS_TO',
				'name' => $root_fields_value,
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
				$key_name = $this->generateKeyName($elements[$number]['fields'][$field_number]['name'],$root_fields_value);
			}

			$elements[$number]['foreign_keys'][] = array(
				'field' => $elements[$number]['fields'][$field_number]['name'],
				'name' => $key_name,
				'table' => $lookup_table,
			);

			$elements[$number]['relations'][] = array(
				'type' => 'BELONGS_TO',
				'name' => $root_fields_value,
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

	public function extraElementFieldWrangling_EyeDraw2($elements, $number, $field_number, $fields_value) {
		$elements[$number]['fields'][$field_number]['eyedraw_class'] = @$_POST['eyedraw2Class'.$number.'Field'.$field_number];
		$elements[$number]['fields'][$field_number]['eyedraw_size'] = @$_POST['eyedraw2Size'.$number.'Field'.$field_number];
		$elements[$number]['add_selected_eye'] = true;

		if (@$_POST['eyedraw2ExtraReport'.$number.'Field'.$field_number]) {
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
				'name' => $elements[$number]['fields'][$field_number]['lookup_table'] = $elements[$number]['table_name'].'_'.$fields_value
			);

			$lookup_table['values'] = $field_values;
			$lookup_table['class'] = $elements[$number]['fields'][$field_number]['lookup_class'] = $elements[$number]['class_name'] . '_' . str_replace(' ','', ucwords(str_replace('_', ' ', $fields_value) ) );
			
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
				'name' => $elements[$number]['table_name'].'_'.$elements[$number]['fields'][$field_number]['name'].'_assignment',
				'lookup_table' => $lookup_table['name'],
				'lookup_class' => $lookup_table['class'],
				'element_class' => $elements[$number]['class_name'],
			);

			$mapping_table['class'] = $elements[$number]['class_name'] . '_' . str_replace(' ','', ucwords(str_replace('_', ' ', $fields_value) ) ) . '_Assignment';

			$mapping_table = $this->generateKeyNames($mapping_table,array('lmui','cui','ele','lku'));

			$elements[$number]['mapping_tables'][] = $mapping_table;

			$elements[$number]['relations'][] = array(
				'type' => 'HAS_MANY',
				'name' => $elements[$number]['fields'][$field_number]['name'].'s',
				#'class' => str_replace(' ','',ucwords(str_replace('_',' ',$mapping_table['name']))),
				'class' => $mapping_table['class'],
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
		if (isset($table['table_name'])) {
			$table_name = $table['table_name'];
		} else {
			$table_name = $table['name'];
		}

		foreach ($keys as $key) {
			$table[$key.'_key'] = $table_name.'_'.$key.'_fk';

			if (strlen($table[$key.'_key']) >64) {
				$ex = explode('_',$table_name);
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

	public function getDBFieldSQLType($field) {
		switch ($field['type']) {
			case 'Textbox':
				$size = $field['textbox_max_length'] ? $field['textbox_max_length'] : '255';
				return "varchar($size) DEFAULT \'\'";
			case 'Textarea':
				return "text DEFAULT \'\'";
			case 'Date picker':
				return "date DEFAULT NULL";
			case 'Dropdown list':
				return isset($field['default_value']) ? "int(10) unsigned NOT NULL DEFAULT {$field['default_value']}" : "int(10) unsigned NOT NULL";
			case 'Textarea with dropdown':
				return "text NOT NULL";
			case 'Checkbox':
				return "tinyint(1) unsigned NOT NULL";
			case 'Radio buttons':
				return isset($field['default_value']) ? "int(10) unsigned NOT NULL DEFAULT {$field['default_value']}" : "int(10) unsigned NOT NULL";
			case 'Boolean':
				return "tinyint(1) unsigned NOT NULL DEFAULT 0";
			case 'Integer':
				$default = strlen($field['integer_default_value'])>0 ? " DEFAULT {$field['integer_default_value']}" : '';
				return "int(10) unsigned NOT NULL$default";
			case 'EyeDraw':
				return "text COLLATE utf8_bin NOT NULL";
			case 'EyeDraw2':
				return "text COLLATE utf8_bin NOT NULL";
			case 'Multi select':
				return false;
			case 'Slider':
				$default = $field['slider_default_value'] ? " DEFAULT \'{$field['slider_default_value']}\'" : '';

				if ($field['slider_dp'] <1) {
					return "int(10) NOT NULL$default";
				}

				$maxlen = strlen(preg_replace('/\..*?$/','',preg_replace('/^\-/','',$field['slider_max_value'])));
				$minlen = strlen(preg_replace('/\..*?$/','',preg_replace('/^\-/','',$field['slider_min_value'])));
				if ($maxlen > $minlen) {
					$size = $maxlen;
				} else {
					$size = $minlen;
				}
				$size += (integer)$field['slider_dp'];

				return "decimal ($size,{$field['slider_dp']}) NOT NULL$default";
			case 'Decimal':
				$default = $field['default_value']>0 ? " DEFAULT \'{$field['default_value']}\'" : '';
				
				$maxlen = strlen(preg_replace('/\..*$/', '', preg_replace('/^[\-\+]/','', $field['decimal_max_value'])));
				$minlen = strlen(preg_replace('/\..*$/', '', preg_replace('/^[\-\+]/','', $field['decimal_min_value'])));
				
				if ($maxlen > $minlen) {
					$size = $maxlen;
				} else {
					$size = $minlen;
				}
				
				$size += (integer)$field['decimal_dp'];
				
				return "decimal ($size, {$field['decimal_dp']}) NOT NULL$default";
		}

		return false;
	}

	public function init() {
		$this->mode = @$_POST['EventTypeModuleMode'] ? 'update' : 'create';

		if (isset($_GET['ajax']) && preg_match('/^[a-zA-Z0-9_]+$/',$_GET['ajax'])) {
			if ($_GET['ajax'] == 'table_fields') {
				EventTypeModuleCode::dump_table_fields($_GET['table']);
			} else if ($_GET['ajax'] == 'field_unique_values') {
				EventTypeModuleCode::dump_field_unique_values($_GET['table'],$_GET['field']);
			} else if ($_GET['ajax'] == 'dump_field_unique_values_multi') {
				EventTypeModuleCode::dump_field_unique_values_multi($_GET['table'],$_GET['field']);
			} else if ($_GET['ajax'] == 'getEyedrawSize') {
				EventTypeModuleCode::getEyedrawSize($_GET['class']);
			} else if ($_GET['ajax'] == 'event_type_properties') {
				EventTypeModuleCode::eventTypeProperties($_GET['event_type_id']);
			} else {
				if (file_exists("protected/gii/EventTypeModule/views/{$_GET['ajax']}.php")) {
					Yii::app()->getController()->renderPartial($_GET['ajax'],$_GET);
				}
			}
			Yii::app()->end();
		}

		if (!empty($_POST)) {
			$this->validate_form();
		}

		parent::init();
	}

	/*
	 * works out the short name for an event type - if the event was generated through this version of gii
	 * then it will use the moduleShortSuffix property on the class. Otherwise it uses the table names 
	 * generated for the event elements.
	 * 
	 * assumes table names of the form et_[specialty_code][group_code][short_name]_[element_short_name]
	 * 
	 * event needs to have had an element defined.
	 * 
	 */
	static public function getEventShortName($event_type) {
		if (isset($event_type->moduleShortSuffix)) {
			return $event_type->moduleShortSuffix;
		}
		else {
			// try to derive the short suffix from the table name of an element in the class
			$el = ElementType::model()->findall('event_type_id=:eventTypeId', array(':eventTypeId' => $event_type->id));
			
			if (count($el)) {
				$code = strtolower(substr($event_type->class_name, 0, 5));
				if (!preg_match('/^et_'.$code.'([a-z0-9]+)_/', $test->tableName(), $m) ) {
					die ("ERROR: cannot determine short name for event type " . $event_type->class_name);
				}
				return $m[1];
			}
			return '';
		}
	}
	
	static public function eventTypeProperties($event_type_id) {
		$event_type = EventType::model()->findByPk($event_type_id);
		$event_type_short_name = EventTypeModuleCode::getEventShortName($event_type);
		
		if (empty($_POST)) {
			if (!preg_match('/^([A-Z][a-z]+)([A-Z][a-z]+)([A-Z][a-zA-Z]+)$/',$event_type->class_name,$m)) {
				die("ERROR: $event_type->class_name");
			}

			$specialty_id = Specialty::model()->find('code=?',array(strtoupper($m[1])))->id;
			$event_group_id = EventGroup::model()->find('code=?',array($m[2]))->id;
			$event_type_name = $event_type->name;
		} else {
			$specialty_id = @$_REQUEST['Specialty']['id'];
			$event_group_id = @$_REQUEST['EventGroup']['id'];
			$event_type_name = @$_REQUEST['EventTypeModuleCode']['moduleSuffix'];
		}
		?>
		<label>Specialty: </label>
		<?php echo CHtml::dropDownList('Specialty[id]',$specialty_id, CHtml::listData(Specialty::model()->findAll(array('order' => 'name')), 'id', 'name'))?><br/>
		<label>Event group: </label><?php echo CHtml::dropDownList('EventGroup[id]', $event_group_id, CHtml::listData(EventGroup::model()->findAll(array('order' => 'name')), 'id', 'name'))?><br />
		<label>Name of event type: </label> <?php echo CHtml::textField('EventTypeModuleCode[moduleSuffix]',$event_type_name,array('size'=>65, 'id'=>'moduleSuffix')); ?><br />
		<label>Event type short name: </label> <?php echo CHtml::textField('EventTypeModuleCode[moduleShortSuffix]',$event_type_short_name,array('size'=>65, 'id'=>'moduleShortSuffix')); ?><br />
		<?php
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

	/**
	 * validation check to determine if an element called $name already exists for the event type
	 * only performs check for updates
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function elementExists($name) {
		if ($this->mode == 'update') {
			// get the id of the event type we are updating, and check if an element with this name exists for that event
			return ( ElementType::model()->find('event_type_id=:eventTypeId and name=:elementName',array('eventTypeId'=>@$_POST['EventTypeModuleEventType'],':elementName'=>$name)) != null);
		}
		return false;
	}
	
	/**
	 * checks if an element short name exists 
	 * 
	 * We use the short name to define table names, so here we check for the table 
	 * being defined in the db based off the current event.
	 * 
	 * Only works for updates.
	 * @since future
	 */
	public function elementShortNameExists($name) {
		if ($this->mode == 'update') {
			// TODO: work out what the table name would be for the element based off the current event
			/* 
			 * get the elements that would be used to create the element table name - speciality, group, and event type
			 * concatanate these, and then try and get the table
			 */
			$tname = strtolower('et_' . EventType::model()->findByPk(@$_POST['EventTypeModuleEventType'])->class_name . '_' . $name);
			return Yii::app()->db->schema->getTable($tname);
		}
		return false;
	}

	public function validate_form() {
		$errors = array();

		foreach ($_POST as $key => $value) {
			foreach ($this->validation_rules as $regex => $rule) {
				if (@preg_match($regex, $key, $m)) {
					if ($error = $this->validateRule($regex, @$m[1], @$m[2])) {
						$errors = array_merge($errors,$error);
					}
				}
			}

			if (preg_match('/^elementType([0-9]+)FieldType([0-9]+)$/',$key,$m)) {
				if ($error = $this->validateRule($value,$m[1],$m[2])) {
					$errors = array_merge($errors,$error);
				}
			}
		}

		Yii::app()->getController()->form_errors = $errors;
	}

	public function validateRule($field_type,$element_num,$field_num) {
		$errors = array();

		if (!isset($this->validation_rules[$field_type])) {
			return $errors;
		}

		foreach ($this->validation_rules[$field_type] as $field => $rules) {
			foreach ($rules as $rule) {
				if (isset($rule['field_property'])) {
					$key = $field.'['.$rule['field_property'].']';
					$value = @$_POST[$field][$rule['field_property']];
				} else {
					$key = $this->substitutePostValue($field,$element_num,$field_num);
					$value = @$_POST[$key];
				}
				
				if (isset($errors[$key])) continue;

				if (isset($rule['condition'])) {
					$condition_key = $this->substitutePostValue($rule['condition']['field'],$element_num,$field_num);
					if (isset($rule['condition']['value_list'])) {
						if (!in_array(@$_POST[$condition_key], $rule['condition']['value_list'])) continue;
					} else {
						if (@$_POST[$condition_key] != $rule['condition']['value']) continue;
					}
				}
				
				if ($rule['type'] == 'required') {
					if (strlen($value) <1) {
						$errors[$key] = isset($rule['message']) ? $rule['message'] : 'This field is required';
						$errors[$key] .= ' ('.$value.') ['.$key.']';
						continue;
					}
				} else if (strlen($value) <1) continue;
				
				switch ($rule['type']) {
					case 'length':
						if (isset($rule['regstrip'])) {
							$checkval = preg_replace($rule['regstrip'], '', $value);
						}
						else {
							$checkval = $value;
						}
						if (isset($rule['max']) && strlen($checkval) > $rule['max']) {
							$errors[$key] = isset($rule['message']) ? $rule['message'] : "Cannot be longer than " . $rule['max']. " characters";
						}
						if (isset($rule['min']) && strlen($checkval) < $rule['min']) {
							$errors[$key] = isset($rule['message']) ? $rule['message'] : "Must be at least " . $rule['min'] . " characters";
						}
						break;
					case 'integer':
						if (!preg_match('/^\-?[0-9]+$/',$value)) {
							$errors[$key] = isset($rule['message']) ? $rule['message'] : 'Must be an integer';
						}
						break;
					case 'integer_positive':
						if (!ctype_digit($value)) {
							$errors[$key] = isset($rule['message']) ? $rule['message'] : 'Must be a positive integer';
						}
						break;
					case 'number':
						if (!preg_match('/^\-?[0-9\.]+$/',$value)) {
							$errors[$key] = isset($rule['message']) ? $rule['message'] : 'Must be a number';
						}
						break;
					case 'number_positive':
						if (!preg_match('/^[0-9\.]+$/',$value)) {
							$errors[$key] = isset($rule['message']) ? $rule['message'] : 'Must be a positive number';
						}
						break;
					case 'compare':
						if (isset($rule['compare_field'])) {
							$compare = $this->substitutePostValue($rule['compare_field'],$element_num,$field_num);
							$compare = $_POST[$compare];
						} else {
							$compare = $rule['compare_value'];
						}

						switch ($rule['operator']) {
							case 'greater_equal':
								if ($value < $compare) {
									echo "[$value][$compare]";
									$errors[$key] = isset($rule['message']) ? $rule['message'] : 'Must be '.$compare.' or greater';
								}
								break;
							case 'lesser_equal':
								if ($value > $compare) {
									$errors[$key] = isset($rule['message']) ? $rule['message'] : 'Must be '.$compare.' or lower';
								}
								break;
						}
						break;
					case 'regex':
						if (!preg_match($rule['regex'],$value)) {
							$errors[$key] = isset($rule['message']) ? $rule['message'] : 'Invalid characters in input';
						}
						break;
					case 'exists':
						if (isset($rule['exists_method'])) {
							$method = $rule['exists_method'];
							if ($this->{$method}($value)) {
								$errors[$key] = isset($rule['message']) ? $rule['message'] : 'Already exists';
							}
						}
						break;
				}
			}
		}

		return $errors;
	}

	public function substitutePostValue($field,$element_num,$field_num) {
		return str_replace('{$element_num}',$element_num,str_replace('{$field_num}',$field_num,$field));
	}

	// the <?'s here are deliberately broken apart to prevent annoying syntax highlighting issues with vim
	public function getHTMLField($field, $mode) {
		if ($mode == 'view') {
			return $this->getHTMLFieldView($field);
		}

		switch ($field['type']) {
			case 'Textbox':
				return '<?php echo $form->textField($element, \''.$field['name'].'\', array(\'size\' => \''.$field['textbox_size'].'\''.($field['textbox_max_length'] ? ',\'maxlength\' => \''.$field['textbox_max_length'].'\'' : '').'))?'.'>';
			case 'Decimal':
				return '<?php echo $form->textField($element, \''.$field['name'].'\', array(\'size\' => \''.$field['decimal_size'].'\''.($field['decimal_max_length'] ? ',\'maxlength\' => \''.$field['decimal_max_length'].'\'' : '').'))?'.'>';
			case 'Integer':
				return '<?php echo $form->textField($element, \''.$field['name'].'\', array(\'size\' => \''.$field['integer_size'].'\''.($field['integer_max_length'] ? ',\'maxlength\' => \''.$field['integer_max_length'].'\'' : '').'))?'.'>';
			case 'Textarea':
				return '<?php echo $form->textArea($element, \''.$field['name'].'\', array(\'rows\' => '.$field['textarea_rows'].', \'cols\' => '.$field['textarea_cols'].'))?'.'>';
			case 'Date picker':
				return '<?php echo $form->datePicker($element, \''.$field['name'].'\', array(\'maxDate\' => \'today\'), array(\'style\'=>\'width: 110px;\'))?'.'>';
			case 'Dropdown list':
				return '<?php echo $form->dropDownList($element, \''.$field['name'].'\', CHtml::listData('.$field['lookup_class'].'::model()->findAll(array(\'order\'=> \''.$field['order_field'].' asc\')),\'id\',\''.$field['lookup_field'].'\')'.(@$field['empty'] ? ',array(\'empty\'=>\'- Please select -\')' : '').')?'.'>';
			case 'Textarea with dropdown':
				return '<?php echo $form->dropDownListNoPost(\''.$field['name'].'\', CHtml::listData('.$field['lookup_class'].'::model()->findAll(),\'id\',\''.$field['lookup_field'].'\'),\'\',array(\'empty\'=>\'- '.ucfirst($field['label']).' -\',\'class\'=>\'populate_textarea\'))?'.'>'."\n".
					'<?php echo $form->textArea($element, \''.$field['name'].'\', array(\'rows\' => '.$field['textarea_rows'].', \'cols\' => '.$field['textarea_cols'].'))?'.'>';
			case 'Checkbox':
				return '<?php echo $form->checkBox($element, \''.$field['name'].'\')?'.'>';
			case 'Radio buttons':
				return '<?php echo $form->radioButtons($element, \''.$field['name'].'\', \''.$field['lookup_table'].'\')?'.'>';
			case 'Boolean':
				return '<?php echo $form->radioBoolean($element, \''.$field['name'].'\')?'.'>';
			case 'EyeDraw':
				return '<div class="clearfix" style="background-color: #DAE6F1;">
		<?php
			$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget'.$field['eyedraw_class'].'\', array(
				\'side\'=>$element->getSelectedEye()->getShortName(),
				\'mode\'=>\'edit\',
				\'size\'=>'.$field['eyedraw_size'].',
				\'model\'=>$element,
				\'attribute\'=>\''.$field['name'].'\',
			));
			'.(@$field['extra_report'] ? 'echo $form->hiddenInput($element, \''.$field['name'].'2\');' : '').'
		?>
	</div>';
			case 'EyeDraw2':
				switch ($field['eyedraw_class']) {
					case 'AnteriorSegment':
						return '<?php'."\n".'		$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget\', array(
		\'doodleToolBarArray\' => array(\'NuclearCataract\',\'CorticalCataract\',\'PostSubcapCataract\',\'PCIOL\',\'ACIOL\',\'Bleb\',\'PI\',\'Fuchs\',\'RK\',\'LasikFlap\',\'CornealScar\'),
		\'onReadyCommandArray\' => array(
			array(\'addDoodle\', array(\'AntSeg\')),
			array(\'deselectDoodles\', array()),
		),
		\'bindingArray\' => array(
		),
		\'listenerArray\' => array(
		),
		\'idSuffix\' => $element->getSelectedEye()->name.\'_\'.$element->elementType->id,
		\'side\' => $element->getSelectedEye()->getShortName(),
		\'mode\' => \'edit\',
		\'width\' => '.$field['eyedraw_size'].',
		\'height\' => '.$field['eyedraw_size'].',
		\'model\' => $element,
		\'attribute\' => \''.$field['name'].'\',
	))?'.'>';
					case 'Buckle':
						return '';
					case 'Cataract':
						return '<?php'."\n".'		$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget\', array(
		\'doodleToolBarArray\' => array(
			0 => array(\'PhakoIncision\',\'SidePort\',\'IrisHook\',\'PCIOL\',\'ACIOL\',\'PI\'),
			1 => array(\'MattressSuture\',\'CapsularTensionRing\',\'CornealSuture\',\'ToricPCIOL\',\'LimbalRelaxingIncision\'),
		),
		\'onReadyCommandArray\' => array(
			array(\'addDoodle\', array(\'AntSeg\')),
			array(\'addDoodle\', array(\'PhakoIncision\')),
			array(\'addDoodle\', array(\'PCIOL\')),
			array(\'deselectDoodles\', array()),
		),
		\'bindingArray\' => array(
		),
		\'listenerArray\' => array(
		),
		\'idSuffix\' => \'Cataract\',
		\'side\' => $element->getSelectedEye()->getShortName(),
		\'mode\' => \'edit\',
		\'width\' => '.$field['eyedraw_size'].',
		\'height\' => '.$field['eyedraw_size'].',
		\'model\' => $element,
		\'attribute\' => \''.$field['name'].'\',
		\'offsetX\' => 10,
		\'offsetY\' => 10,
		\'template\' => \'OEEyeDrawWidgetCataract\',
	))?'.'>';
					case 'Gonioscopy':
						return '<?php'."\n".'		$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget\', array(
		\'doodleToolBarArray\' => array(\'AngleNV\',\'AntSynech\',\'AngleRecession\'),
		\'onReadyCommandArray\' => array(
			array(\'addDoodle\',array(\'Gonioscopy\')),
			array(\'addDoodle\',array(\'AngleGradeNorth\')),
			array(\'addDoodle\',array(\'AngleGradeEast\')),
			array(\'addDoodle\',array(\'AngleGradeSouth\')),
			array(\'addDoodle\',array(\'AngleGradeWest\')),
			array(\'deselectDoodles\',array()),
		),
		\'bindingArray\' => array(
		),
		\'idSuffix\' => $element->getSelectedEye()->name.\'_\'.$element->elementType->id,
		\'side\' => $element->getSelectedEye()->getShortName(),
		\'mode\' => \'edit\',
		\'width\' => \''.$field['eyedraw_size'].'\',
		\'height\' => \''.$field['eyedraw_size'].'\',
		\'model\' => $element,
		\'attribute\' => \''.$field['name'].'\',
))?'.'>';
					case 'OpticDisc':
						return '<?php'."\n".'		$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget\', array(
		\'doodleToolBarArray\' => array(\'PeripapillaryAtrophy\', \'DiscPallor\', \'DiscHaemorrhage\', \'NerveFibreDefect\', \'OpticDiscPit\', \'Papilloedema\'),
		\'onReadyCommandArray\' => array(
			array(\'addDoodle\', array(\'OpticDisc\', array(\'mode\' => \'Basic\'))),
			array(\'deselectDoodles\', array()),
		),
		\'bindingArray\' => array(
		),
		\'idSuffix\' => $element->getSelectedEye()->name.\'_\'.$element->elementType->id,
		\'side\' => $element->getSelectedEye()->getShortName(),
		\'mode\' => \'edit\',
		\'width\' => \''.$field['eyedraw_size'].'\',
		\'height\' => \''.$field['eyedraw_size'].'\',
		\'model\' => $element,
		\'attribute\' => \''.$field['name'].'\',
))?'.'>';
					case 'PosteriorSegment':
						return '<?php'."\n".'		$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget\', array(
		\'doodleToolBarArray\' => array(\'Geographic\',\'VitreousOpacity\',\'DiabeticNV\',\'CNV\',\'Circinate\',\'CystoidMacularOedema\',\'EpiretinalMembrane\',\'HardDrusen\',\'PRPPostPole\',\'MacularHole\'),
		\'onReadyCommandArray\' => array(
			array(\'addDoodle\', array(\'PostPole\')),
			array(\'deselectDoodles\', array()),
		),
		\'bindingArray\' => array(
		),
		\'idSuffix\' => $element->getSelectedEye()->name.\'_\'.$element->elementType->id,
		\'side\' => $element->getSelectedEye()->getShortName(),
		\'mode\' => \'edit\',
		\'width\' => \''.$field['eyedraw_size'].'\',
		\'height\' => \''.$field['eyedraw_size'].'\',
		\'model\' => $element,
		\'attribute\' => \''.$field['name'].'\',
))?'.'>';
					case 'Refraction':
						return '<?php'."\n".'		$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget\', array(
		\'doodleToolBarArray\' => array(),
		\'toolbar\' => false,
		\'onReadyCommandArray\' => array(
			array(\'addDoodle\', array(\'TrialFrame\')),
			array(\'addDoodle\', array(\'TrialLens\')),
			array(\'deselectDoodles\', array()),
		),
		\'bindingArray\' => array(
		),
		\'idSuffix\' => $element->getSelectedEye()->name.\'_\'.$element->elementType->id,
		\'side\' => $element->getSelectedEye()->getShortName(),
		\'mode\' => \'edit\',
		\'width\' => \''.$field['eyedraw_size'].'\',
		\'height\' => \''.$field['eyedraw_size'].'\',
		\'model\' => $element,
		\'attribute\' => \''.$field['name'].'\',
))?'.'>';
					case 'SurgeonPosition':
						return '<?php'."\n".'		$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget\', array(
		\'onReadyCommandArray\' => array(
			array(\'addDoodle\', array(\'OperatingTable\')),
			array(\'addDoodle\', array(\'Surgeon\')),
			array(\'deselectDoodles\', array()),
		),
		\'syncArray\' => array(
		),
		\'idSuffix\' => \'Position\',
		\'side\' => $element->getSelectedEye()->getShortName(),
		\'mode\' => \'edit\',
		\'width\' => '.$field['eyedraw_size'].',
		\'height\' => '.$field['eyedraw_size'].',
		\'model\' => $element,
		\'attribute\' => \''.$field['name'].'\',
		\'offsetX\' => 10,
		\'offsetY\' => 10,
		\'toolbar\' => false,
		\'template\' => \'OEEyeDrawWidgetSurgeonPosition\',
	))?'.'>';
					case 'Vitrectomy':
						return '';
				}
				break;
			case 'Multi select':
				return '<?php echo $form->multiSelectList($element, \'MultiSelect_'.$field['name'].'\', \''.@$field['multiselect_relation'].'\', \''.@$field['multiselect_field'].'\', CHtml::listData('.@$field['multiselect_lookup_class'].'::model()->findAll(array(\'order\'=>\''.$field['multiselect_order_field'].' asc\')),\'id\',\''.$field['multiselect_table_field_name'].'\'), $element->'.@$field['multiselect_lookup_table'].'_defaults, array(\'empty\' => \'- Please select -\', \'label\' => \''.$field['label'].'\'))?'.'>';
			case 'Slider':
				return '<?php echo $form->slider($element, \''.$field['name'].'\', array(\'min\' => '.$field['slider_min_value'].', \'max\' => '.$field['slider_max_value'].', \'step\' => '.$field['slider_stepping'].($field['slider_dp'] ? ', \'force_dp\' => '.$field['slider_dp'] : '').'))?'.'>';
		}
	}

	public function getHTMLFieldView($field) {
		switch ($field['type']) {
			case 'Textbox':
			case 'Textarea':
			case 'Textarea with dropdown':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'></td>
			<td><span class="big"><?php echo CHtml::encode($element->'.$field['name'].')?'.'></span></td>
		</tr>';
			case 'Decimal':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'></td>
			<td><span class="big"><?php echo $element->'.$field['name'].'?'.'></span></td>
		</tr>';
			case 'Integer':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'></td>
			<td><span class="big"><?php echo $element->'.$field['name'].'?'.'></span></td>
		</tr>';
			case 'Date picker':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'></td>
			<td><span class="big"><?php echo CHtml::encode($element->NHSDate(\''.$field['name'].'\'))?'.'></span></td>
		</tr>';
			case 'Dropdown list':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'></td>
			<td><span class="big"><?php echo $element->'.preg_replace('/_id$/','',$field['name']).' ? $element->'.preg_replace('/_id$/','',$field['name']).'->'.$field['lookup_field'].' : \'None\'?'.'></span></td>
		</tr>';
			case 'Checkbox':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'></td>
			<td><span class="big"><?php $element->'.$field['name'].' ? \'Yes\' : \'No\'?'.'></span></td>
		</tr>';
			case 'Radio buttons':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'></td>
			<td><span class="big"><?php echo $element->'.preg_replace('/_id$/','',$field['name']).' ? $element->'.preg_replace('/_id$/','',$field['name']).'->name : \'None\'?'.'></span></td>
		</tr>';
			case 'Boolean':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'>:</td>
			<td><span class="big"><?php echo $element->'.$field['name'].' ? \'Yes\' : \'No\'?'.'></span></td>
		</tr>';
			case 'EyeDraw':
				return '		<tr>
			<td colspan="2">
				<?php
					$this->widget(\'application.modules.eyedraw.OEEyeDrawWidget'.$field['eyedraw_class'].'\', array(
						\'side\'=>$element->eye->getShortName(),
						\'mode\'=>\'view\',
						\'size\'=>'.$field['eyedraw_size'].',
						\'model\'=>$element,
						\'attribute\'=>\''.$field['name'].'\',
					));
				?>
			</td>
		</tr>
		'.(@$field['extra_report'] ? '<tr>
			<td width="30%">Report:</td>
			<td><span class="big"><?php echo CHtml::encode($element->'.$field['name'].'2)?'.'></span></td>
		</tr>' : '');
			case 'Multi select':
				return '		<tr>
			<td colspan="2">
				<div class="colThird">
					<b><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'>:</b>
					<div class="eventHighlight medium">
						<?php if (!$element->'.@$field['multiselect_relation'].') {?'.'>
							<h4>None</h4>
						<?php }else{?'.'>
							<h4>
								<?php foreach ($element->'.@$field['multiselect_relation'].' as $item) {
									echo $item->'.@$field['multiselect_lookup_table'].'->name?'.'><br/>
								<?php }?'.'>
							</h4>
						<?php }?'.'>
					</div>
				</div>
			</td>
		</tr>';
			case 'Slider':
				return '		<tr>
			<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel(\''.$field['name'].'\'))?'.'></td>
			<td><span class="big"><?php echo $element->'.$field['name'].'?'.'></span></td>
		</tr>';
		}
	}

	public function updateModel($model_path, $element) {
		$data = file_get_contents($model_path);

		if (preg_match('/public function rules.*?\}/si',$data,$m)) {
			$replace = '';

			foreach (explode(chr(10),$m[0]) as $line) {
				if (preg_match('/array\(([a-zA-Z0-9_ ,\']+),[\s\t]*\'safe\'\),/',$line,$n)) {
					$fields = preg_replace('/[, \']+$/','',preg_replace('/^[ ,\']+/','',$n[1]));

					foreach ($element['fields'] as $num => $field) {
						if ($field['type'] != 'Multi select') {
							$fields .= ", ".$field['name'];
						}
					}

					$replace .= "\t\t\tarray('$fields', 'safe'),\n";
				} else if (preg_match('/array\(([a-zA-Z0-9_ ,\']+),[\s\t]*\'required\'\),/',$line,$n)) {
					$fields = preg_replace('/[, \']+$/','',preg_replace('/^[ ,\']+/','',$n[1]));

					foreach ($element['fields'] as $num => $field) {
						if ($field['required'] && $field['type'] != 'Multi select') {
							$fields .= ", ".$field['name'];
						}
					}

					$replace .= "\t\t\tarray('$fields', 'required'),\n";
				} else if (preg_match('/array\(([a-zA-Z0-9_ ,\']+),[\s\t]*\'safe\',[\s\t]*\'on\'[\s\t]*=>[\s\t]*\'search\'\),/',$line,$n)) {
					$fields = preg_replace('/[, \']+$/','',preg_replace('/^[ ,\']+/','',$n[1]));

					foreach ($element['fields'] as $num => $field) {
						if ($field['type'] != 'Multi select') {
							$fields .= ", ".$field['name'];
						}
					}

					$replace .= "\t\t\tarray('$fields', 'safe', 'on' => 'search'),\n";
				} else {
					$replace .= $line."\n";
				}
			}

			$data = str_replace($m[0],$replace,$data);
		}

		if (preg_match('/public function relations.*?\}/si',$data,$m)) {
			$relations = "public function relations()\n\t{\n\t\t// NOTE: you may need to adjust the relation name and the related\n\t\t// class name for the relations automatically generated below.\n\t\treturn array(\n";

			foreach (explode(chr(10),$m[0]) as $line) {
				if (preg_match('/\(self::/',$line)) {
					$relations .= $line."\n";
				}
			}

			foreach ($element['relations'] as $relation) {
				$relations .= "\t\t\t'{$relation['name']}' => array(self::{$relation['type']}, '{$relation['class']}', '{$relation['field']}'),\n";
			}

			$relations .= "\t\t);\n\t}";

			$data = str_replace($m[0],$relations,$data);
		}

		if (preg_match('/public function attributeLabels.*?\}/si',$data,$m)) {
			$labels = "public function attributeLabels()\n\t{\n\t\treturn array(\n";

			foreach (explode(chr(10),$m[0]) as $line) {
				if (preg_match('/=>/',$line)) {
					$labels .= "\t\t\t".preg_replace('/^[\s\t]+/','',$line)."\n";
				}
			}

			foreach ($element['fields'] as $field) {
				$labels .= "\t\t\t'{$field['name']}' => '{$field['label']}',\n";
			}

			$labels .= "\t\t);\n\t}";

			$data = str_replace($m[0],$labels,$data);
		}

		file_put_contents($model_path, $data);
	}

	public function handleViewChanges() {
		foreach ($this->getElementsFromPost() as $num => $element) {
			$model = "modules/$this->moduleID/models/{$element['class_name']}.php";

			if ($this->shouldUpdateFile($model)) {
				$this->updateModel(Yii::app()->basePath.'/'.$model, $element);
			}

			$create = "modules/$this->moduleID/views/default/create_{$element['class_name']}.php";

			if ($this->shouldUpdateFile($create)) {
				$this->updateFormView(Yii::app()->basePath.'/'.$create, $element, 'create');
			}

			$update = "modules/$this->moduleID/views/default/update_{$element['class_name']}.php";

			if ($this->shouldUpdateFile($update)) {
				$this->updateFormView(Yii::app()->basePath.'/'.$update, $element, 'update');
			}

			$form = "modules/$this->moduleID/views/default/form_{$element['class_name']}.php";

			if ($this->shouldUpdateFile($form)) {
				$this->updateFormView(Yii::app()->basePath.'/'.$form, $element, 'form');
			}

			$view = "modules/$this->moduleID/views/default/view_{$element['class_name']}.php";

			if ($this->shouldUpdateFile($update)) {
				$this->updateViewView(Yii::app()->basePath.'/'.$view, $element, 'view');
			}
		}
	}

	public function shouldUpdateFile($model) {
		if (isset($_POST['updatefile'])) {
			foreach ($_POST['updatefile'] as $hash => $value) {
				if ($_POST['filename'][$hash] == $model) {
					return true;
				}
			}
		}

		return false;
	}

	public function updateFormView($view_path, $element, $mode) {
		$data = file_get_contents($view_path);

		if (preg_match('/<div.*<\/div>/si',$data,$m)) {
			$lines = explode(chr(10),$m[0]);

			$open_div = array_shift($lines);
			$close_div = array_pop($lines);

			$replace = $open_div."\n";

			foreach ($lines as $line) {
				if (trim($line)) {
					$replace .= $line."\n";
				}
			}

			foreach ($element['fields'] as $field) {
				$replace .= "\t\t".$this->getHTMLField($field, $mode)."\n";
			}

			$replace .= $close_div."\n";

			file_put_contents($view_path, str_replace($m[0],$replace,$data));
		}
	}

	public function updateViewView($view_path, $element) {
		$data = file_get_contents($view_path);

		if (preg_match('/<tbody.*<\/tbody>/si',$data,$m)) {
			$lines = explode(chr(10),$m[0]);

			$open_tbody = array_shift($lines);
			$close_tbody = array_pop($lines);

			$replace = $open_tbody."\n";

			foreach ($lines as $line) {
				if (trim($line)) {
					$replace .= $line."\n";
				}
			}

			foreach ($element['fields'] as $field) {
				$replace .= "\t\t".$this->getHTMLField($field, 'view')."\n";
			}

			$replace .= $close_tbody."\n";

			file_put_contents($view_path, str_replace($m[0],$replace,$data));
		}
	}

	public function getTableForeignKeys($table) {
		$foreign_keys = array();

		foreach (Yii::app()->db->createCommand("show create table `$table`")->queryAll() as $row) {
			foreach (explode(chr(10),$row['Create Table']) as $line) {
				if (preg_match('/CONSTRAINT `(.*?)` FOREIGN KEY \(`(.*?)`\) REFERENCES `(.*?)` \(`(.*?)`\)/',$line,$m)) {
					$foreign_keys[] = array(
						'name' => $m[1],
						'field' => $m[2],
						'remote_table' => $m[3],
						'remote_field' => $m[4],
					);
				}
			}
		}

		return $foreign_keys;
	}

	public function rawSQLQuery($sql) {
		Yii::app()->db->createCommand($sql)->query();
	}

	public function handleModuleNameChange($current_class, $target_class) {
		@rename(Yii::app()->basePath.'/modules/'.$current_class,Yii::app()->basePath.'/modules/'.$target_class);

		$this->event_type->name = $_POST['EventTypeModuleCode']['moduleSuffix'];
		$this->event_type->class_name = $target_class;

		$this->rawSQLQuery("UPDATE event_type SET name = '{$this->event_type->name}',class_name = '$target_class',event_group_id={$this->event_group->id} WHERE id = {$this->event_type->id}");

		foreach (ElementType::model()->findAll('event_type_id=:eventTypeId',array(':eventTypeId'=>$this->event_type->id)) as $element_type) {
			$element_class_name = 'Element_'.$target_class.'_'.preg_replace("/ /", "", ucwords(strtolower($element_type->name)));
			$this->rawSQLQuery("UPDATE element_type SET class_name = '$element_class_name' WHERE id = $element_type->id");
		}

		foreach (Yii::app()->db->createCommand()->select('version')->from('tbl_migration')->where("version like '%_$current_class'")->queryAll() as $tbl_migration) {
			$version = str_replace($current_class,$target_class,$tbl_migration['version']);
			$this->rawSQLQuery("UPDATE tbl_migration SET version='$version' where version='{$tbl_migration['version']}'");
		}

		$this->changeAllInstancesOfString(Yii::app()->basePath.'/modules/'.$target_class,$current_class,$target_class);

		$from_table_prefix = 'et_'.strtolower($current_class).'_';
		$to_table_prefix = 'et_'.strtolower($target_class).'_';

		$this->changeAllInstancesOfString(Yii::app()->basePath.'/modules/'.$target_class,$from_table_prefix,$to_table_prefix);

		// introspect the database and fix all table, index and foreign key names
		foreach (Yii::app()->getDb()->getSchema()->getTables() as $table_name => $table) {
			if (strncmp($table_name,$from_table_prefix,strlen($from_table_prefix)) == 0) {
				$foreign_keys = $this->getTableForeignKeys($table_name);

				foreach ($foreign_keys as $foreign_key) {
					if (strncmp($foreign_key['name'],$from_table_prefix,strlen($from_table_prefix)) == 0) {
						$new_key_name = str_replace($from_table_prefix,$to_table_prefix,$foreign_key['name']);

						$this->rawSQLQuery("ALTER TABLE `$table_name` DROP FOREIGN KEY `{$foreign_key['name']}`;");
						$this->rawSQLQuery("DROP INDEX `{$foreign_key['name']}` ON `$table_name`;");
						$this->rawSQLQuery("CREATE INDEX `$new_key_name` ON `$table_name` (`{$foreign_key['field']}`);");
						$this->rawSQLQuery("ALTER TABLE `$table_name` ADD FOREIGN KEY `$new_key_name` (`{$foreign_key['field']}`) REFERENCES `{$foreign_key['remote_table']}` (`{$foreign_key['remote_field']}`);");
					}
				}

				$new_table_name = str_replace($from_table_prefix,$to_table_prefix,$table_name);

				$this->rawSQLQuery("RENAME TABLE `$table_name` TO `$new_table_name`");
			}
		}

		// update the event_type name in the migrations
		$path = Yii::app()->basePath.'/modules/'.$target_class.'/migrations';

		$dh = opendir($path);

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				$data = file_get_contents($path."/".$file);

				if (preg_match_all('/\$event_type[\s\t]*=[\s\t]*.*?->queryRow\(\);/',$data,$m)) {
					foreach ($m[0] as $blob) {
						if (preg_match('/\(\':name\'[\s\t]*=>[\s\t]*\'.*?\'\)/',$blob,$b)) {
							$newblob = str_replace($b[0],"(':name'=>'{$this->event_type->name}')",$blob);
							$data = str_replace($blob,$newblob,$data);
							file_put_contents($path."/".$file,$data);
						}
					}
				}
			}
		}

		closedir($dh);

		$this->initialise(array(
			'moduleID' => $target_class,
			'eventGroupName' => $this->event_group->name,
		));
	}
}
