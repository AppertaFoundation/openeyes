<?php
/**
 * ____________________________________________________________________________.
 *
 * This file is part of OpenEyes.
 *
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file
 * titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * _____________________________________________________________________________
 * http://www.openeyes.org.uk   info@openeyes.org.uk
 *
 * @author Bill Aylward <bill.aylward@openeyes.org.uk>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3.0
 * @license http://www.openeyes.org.uk/licenses/oepl-1.0.html OEPLv1.0
 *
 * @version 0.9
 * Creation date: 27 December 2011
 *
 * @copyright Copyright (c) 2012 OpenEyes Foundation, Moorfields Eye hospital
 */

/**
 * Element Code.
 *
 * @property string $elementName Name of element
 * @property string $tableName Name of element table
 * @property string $elementFields Text block containing field specification
 * @property string $modelClass Name of element model class
 * @property string $modelpath Path where new models are saved
 * @property CDbTableSchema $tableSchema Schema of element table
 * @property string $controllerClass Name of controller class
 * @property string $baseControllerClass Name of superclass of controller class
 * @property string $baseClass Name of superclass of element model classes
 * @property array $ignore Array of fields to be ignored in generation of view files
 * @property string $migrationPath Path to directory where scripts are stored
 * @property string $authorname Name of author
 * @property string $authorEmail email of author
 * @property int $level Flag indicating progress through multilevel generation process
 */
class ElementCode extends CCodeModel
{
    // Properties
    public $elementName;
    public $tableName;
    public $elementFields;
    public $modelClass;
    public $modelPath;
    public $tableSchema;
    public $controllerClass;
    public $baseControllerClass;
    public $baseClass;
    public $ignore;
    public $migrationPath;
    public $authorName;
    public $authorEmail;
    public $level;

    // Constants representing levels through generation process
    const CREATE_MIGRATION = 1;
    const CREATE_FILES = 2;

    /**
     * Runs on object initiation.
     */
    public function init()
    {
        // Check database connection
        if (Yii::app()->db === null) {
            throw new CHttpException(500, 'An active "db" connection is required to run this generator.');
        }

        // Check that reset button has been pressed
        if (isset($_POST['reset'])) {
            $this->resetGenerator();
        }

        // Refresh element name from session variable if on second pass
        if (isset(Yii::app()->session['elementName'])) {
            $this->elementName = Yii::app()->session['elementName'];
        } else {
            $this->elementName = '';
        }

        // Refresh table name from session variable if on second pass
        if (isset(Yii::app()->session['tableName'])) {
            $this->tableName = Yii::app()->session['tableName'];
            $this->modelClass = $this->generateClassName($this->tableName);
        } else {
            $this->tableName = '';
        }

        // Set default variables
        $this->elementFields = "'value' => 'string',";
        $this->controllerClass = $this->generateControllerName($this->modelClass);
        $this->baseControllerClass = 'Controller';
        $this->modelPath = 'application.models.elements';
        $this->baseClass = 'BaseElement';
        $this->ignore = array(
            'id',
            'event_id',
            'created_user_id',
            'created_date',
            'last_modified_user_id',
            'last_modified_date',
            );
        $this->migrationPath = 'application.migrations';
        $this->authorName = Yii::app()->params['authorName'];
        $this->authorEmail = Yii::app()->params['authorEmail'];

        // Set progress level
        if (isset(Yii::app()->session['level'])) {
            $this->level = Yii::app()->session['level'];
        } else {
            $this->level = self::CREATE_MIGRATION;
        }

        parent::init();
    }

    /**
     * Validation rules.
     */
    public function rules()
    {
        // Migration scripts
        if ($this->level == $this::CREATE_MIGRATION) {
            return array_merge(parent::rules(), array(
                array('elementName, migrationPath', 'filter', 'filter' => 'trim'),
                array('elementName, migrationPath', 'required'),
                array('migrationPath', 'validateModelPath', 'skipOnError' => true),
                array('tableName, modelPath, baseClass', 'required'),
                array('elementName, tableName, modelPath', 'match', 'pattern' => '/^(\w+[\w\.]*|\*?|\w+\.\*)$/',
                    'message' => '{attribute} should only contain word characters, dots, and an optional ending asterisk.', ),
                array('elementFields', 'validateElementFields', 'skipOnError' => true),
            ));
        } elseif ($this->level == $this::CREATE_FILES) {
            return array_merge(parent::rules(), array(
                array('modelClass, baseClass', 'match', 'pattern' => '/^[a-zA-Z_]\w*$/', 'message' => '{attribute} should only contain word characters.'),
                array('modelClass', 'validateModelClass'),
                array('modelPath', 'validateModelPath', 'skipOnError' => true),
                array('baseClass, tableName, modelClass, modelPath', 'filter', 'filter' => 'trim'),
                array('baseClass, modelClass', 'validateReservedWord', 'skipOnError' => true),
                array('baseClass', 'validateBaseClass', 'skipOnError' => true),
                array('controllerClass', 'match', 'pattern' => '/^\w+[\w+\\/]*$/', 'message' => '{attribute} should only contain word characters and slashes.'),
                array('baseControllerClass', 'match', 'pattern' => '/^[a-zA-Z_]\w*$/', 'message' => '{attribute} should only contain word characters.'),
                array('baseControllerClass', 'validateReservedWord', 'skipOnError' => true),
            ));
        }
    }

    /**
     * Attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'tableName' => 'Table Name',
            'migrationPath' => 'Migration Path',
            'modelClass' => 'Class Name',
            'modelPath' => 'Model Path',
            'baseClass' => 'Base Class',
            'controllerClass' => 'Controller Name',
            'baseControllerClass' => 'Base Controller Class',
        ));
    }

    /**
     * List of templates that must be present.
     *
     * @return array
     */
    public function requiredTemplates()
    {
        return array(
            'migrationCreate.php',
            'migrationEnable.php',
            'model.php',
            'controller.php',
            '_form.php',
            '_view.php',
        );
    }

    /*
     * Run on form submission by preview or generate buttons. Generates the list of files and code
     */
    public function prepare()
    {
        // Array of files to be generated
        $this->files = array();

        // Migration scripts
        if ($this->level == $this::CREATE_MIGRATION) {
            // Conditionally reset time stamps (otherwise different between preview and generation)
            $this->resetTimeStamps();

            // Create a table object (For other modules this is normally derived from the table schema)
            $table = new CMysqlTableSchema();
            $table->name = $this->tableName;
            $table->rawName = '`'.$this->tableName.'`';
            $table->primaryKey = 'id';
            $table->sequenceName = '';
            $table->foreignKeys = array();
            $table->columns = array();

            // Create class name from the table name
            $this->modelClass = $this->generateClassName($this->tableName);

            // Migration to create table
            $migrationName = $this->generateMigrationName('create', $this->tableName);

            // Array of parameters to pass to template
            $params = array(
                'elementName' => $this->elementName,
                'tableName' => $this->tableName,
                'className' => $this->modelClass,
                'migrationName' => $migrationName,
                'elementFields' => $this->elementFields,
                'eventName' => Yii::app()->params['eventName'],
                'subSubspecialtyName' => Yii::app()->params['subSubspecialtyName'],
                'authorName' => $this->authorName,
                'authorEmail' => $this->authorEmail,
            );

            // Generate code
            $this->files[] = new CCodeFile(
                Yii::getPathOfAlias($this->migrationPath).'/'.$migrationName.'.php',
                $this->render($this->templatePath.'/migrationCreate.php', $params)
            );

            // Migration to enable table
            $migrationName = $this->generateMigrationName('create_site_element_types_for', $this->tableName);

            // Update migration name
            $params['migrationName'] = $migrationName;

            // Generate code
            $this->files[] = new CCodeFile(
                Yii::getPathOfAlias($this->migrationPath).'/'.$migrationName.'.php',
                $this->render($this->templatePath.'/migrationEnable.php', $params)
            );
        }

        // Model and view files
        elseif ($this->level == $this::CREATE_FILES) {
            // Get metadata for element table
            $this->tableSchema = $this->getTableSchema($this->tableName);

            // Array of parameters to pass to template
            $params = array(
                'elementName' => $this->elementName,
                'tableName' => $this->tableName,
                'modelClass' => $this->modelClass,
                'baseClass' => $this->baseClass,
                'columns' => $this->tableSchema->columns,
                'labels' => $this->generateLabels($this->tableSchema),
                'rules' => $this->generateRules($this->tableSchema),
                'ignore' => $this->ignore,
                'authorName' => $this->authorName,
                'authorEmail' => $this->authorEmail,
            );

            // Create path for model file
            $modelFilePath = Yii::getPathOfAlias($this->modelPath).'/'.$this->modelClass.'.php';

            // Generator box ticked by default
            $this->answers[md5($modelFilePath)] = '1';

            // Generate code
            $this->files[] = new CCodeFile(
                $modelFilePath,
                $this->render($this->templatePath.'/model.php', $params)
            );

            // Generate controller name
            $this->controllerClass = $this->generateControllerName($this->modelClass);

            // Paths for files to be stored
            $controllerTemplateFile = $this->templatePath.DIRECTORY_SEPARATOR.'controller.php';
            $viewPath = Yii::app()->getViewPath().'/elements/'.$this->modelClass;
            $controllerFilePath = Yii::app()->getControllerPath().DIRECTORY_SEPARATOR.$this->controllerClass.'.php';

            // Add optional controller file
            $this->files[] = new CCodeFile(
                $controllerFilePath,
                $this->render($controllerTemplateFile)
            );

            // Create paths for view files
            $_formFilePath = $viewPath.DIRECTORY_SEPARATOR.'_form/1.php';
            $_viewFilePath = $viewPath.DIRECTORY_SEPARATOR.'_view/1.php';

            // Generator boxes ticked by default
            $this->answers[md5($_formFilePath)] = '1';
            $this->answers[md5($_viewFilePath)] = '1';

            // Add view files
            $this->files[] = new CCodeFile(
                $_formFilePath, $this->render($this->templatePath.'/_form.php', $params)
            );
            $this->files[] = new CCodeFile(
                $_viewFilePath, $this->render($this->templatePath.'/_view.php', $params)
            );
        }
    }

    /*
     * Check that all database field names conform to PHP variable naming rules
     * For example mysql allows field name like "2011aa", but PHP does not allow variables like "$model->2011aa"
     *
     * @param CDbTableSchema $table the table schema object
     * @return string the invalid table column name. Null if no error.
     */
    public function checkColumns($table)
    {
        foreach ($table->columns as $column) {
            if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $column->name)) {
                return $table->name.'.'.$column->name;
            }
        }
    }

    /**
     * Validation method for table name and model class.
     *
     * @param string $attributes Name of attribute to be validated
     * @param array  $params     Array of parameters
     */
    public function validateTableName($attribute, $params)
    {
        $invalidColumns = array();

        if (($table = $this->getTableSchema($this->tableName)) === null) {
            $this->addError('tableName', "Table '{$this->tableName}' does not exist.");
        }
        if ($this->modelClass === '') {
            $this->addError('modelClass', 'Model Class cannot be blank.');
        }

        if (!$this->hasErrors($attribute) && ($invalidColumn = $this->checkColumns($table)) !== null) {
            $invalidColumns[] = $invalidColumn;
        }

        if ($invalidColumns != array()) {
            $this->addError('tableName', 'Column names that do not follow PHP variable naming convention: '.implode(', ', $invalidColumns).'.');
        }
    }

    /**
     * Validation method for elementFields.
     *
     * @param string $attributes Name of attribute to be validated
     * @param array  $params     Array of parameters
     */
    public function validateElementFields($attribute, $params)
    {
        $allowedValues = array('string', 'text', 'integer', 'float', 'decimal', 'datetime', 'timestamp', 'time', 'date', 'binary', 'boolean');
        $existingKeys = array();

        // Split into lines
        $lines = explode("\n", $this->elementFields);

        // Iterate through lines checking each
        foreach ($lines as $line) {
            // Trim white space from ends of line
            $line = trim($line);

            // Check that line contains exactly one instance of =>, 4 of ', and one comma
            if (substr_count($line, '=>') != 1) {
                $this->addError('elementFields', "Each lines must contain '=>' exactly once");
            } elseif (substr_count($line, "'") != 4) {
                $this->addError('elementFields', 'Each lines must contain exactly 4 apostrophes');
            } elseif ($line[strlen($line) - 1] != ',') {
                $this->addError('elementFields', 'Each lines must end with a comma');
            } else {
                // Split into keys and values
                $keyAndValue = explode('=>', $line);

                // Remove apostrophes and commas
                $key = trim(str_replace("'", '', $keyAndValue[0]));
                $value = trim(str_replace("'", '', $keyAndValue[1]));
                $value = trim(str_replace(',', '', $value));

                // Check keys are unique, and values are allowed
                if (in_array($key, $existingKeys)) {
                    $this->addError('elementFields', 'Field names must be unique');
                }
                if (!in_array($value, $allowedValues)) {
                    $this->addError('elementFields', 'Field types must be in the approved list');
                }

                // Add key to array
                $existingKeys[] = $key;
            }
        }
    }

    /**
     * Validation method for model path.
     *
     * @param string $attributes Name of attribute to be validated
     * @param array  $params     Array of parameters
     */
    public function validateModelPath($attribute, $params)
    {
        if (Yii::getPathOfAlias($this->modelPath) === false) {
            $this->addError('modelPath', 'Model Path must be a valid path alias.');
        }
    }

    /**
     * Validation method for base class.
     *
     * @param string $attributes Name of attribute to be validated
     * @param array  $params     Array of parameters
     */
    public function validateBaseClass($attribute, $params)
    {
        $class = @Yii::import($this->baseClass, true);
        if (!is_string($class) || !$this->classExists($class)) {
            $this->addError('baseClass', "Class '{$this->baseClass}' does not exist or has syntax error.");
        } elseif ($class !== 'CActiveRecord' && !is_subclass_of($class, 'CActiveRecord')) {
            $this->addError('baseClass', "'{$this->model}' must extend from CActiveRecord.");
        }
    }

    /**
     * Validation method for model class and associated table.
     *
     * @param string $attributes Name of attribute to be validated
     * @param array  $params     Array of parameters
     */
    public function validateModelClass($attribute, $params)
    {
        if ($this->hasErrors('modelClass')) {
            return;
        }
    }

    /*
     * Gets schema for passed tableName
     *
     * @param string $tableName Name of table
     * @return CDbTableSchema Schema of table
     */
    public function getTableSchema($tableName)
    {
        return Yii::app()->db->getSchema()->getTable($tableName);
    }

    /*
     * Generates Yii style class name from table name
     *
     * @param string $tableName Name of the table
     * @return string The name of the class
     */
    protected function generateClassName($tableName)
    {
        $className = '';
        foreach (explode('_', $tableName) as $name) {
            if ($name !== '') {
                $className .= ucfirst($name);
            }
        }

        return $className;
    }

    /**
     * Generates labels for fields of the table.
     *
     * @param CDbTableSchema $table A table
     *
     * @return array Array containing the labels
     */
    public function generateLabels($table)
    {
        $labels = array();
        foreach ($table->columns as $column) {
            if (in_array($column->name, $this->ignore)) {
                continue;
            }
            $label = ucwords(trim(strtolower(str_replace(array('-', '_'), ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $column->name)))));
            $label = preg_replace('/\s+/', ' ', $label);
            if (strcasecmp(substr($label, -3), ' id') === 0) {
                $label = substr($label, 0, -3);
            }
            if ($label === 'Id') {
                $label = 'ID';
            }
            $labels[$column->name] = $label;
        }

        return $labels;
    }

    /**
     * Generates rules for the table.
     *
     * @param CDbTableSchema $table A table
     *
     * @return array Array of rules
     */
    public function generateRules($table)
    {
        $rules = array();
        $required = array();
        $integers = array();
        $numerical = array();
        $length = array();
        $safe = array();

        foreach ($table->columns as $column) {
            if (in_array($column->name, $this->ignore)) {
                continue;
            }
            if ($column->autoIncrement) {
                continue;
            }
            $r = !$column->allowNull && $column->defaultValue === null;
            if ($r) {
                $required[] = $column->name;
            }
            if ($column->type === 'integer') {
                $integers[] = $column->name;
            } elseif ($column->type === 'double') {
                $numerical[] = $column->name;
            } elseif ($column->type === 'string' && $column->size > 0) {
                $length[$column->size][] = $column->name;
            } elseif (!$column->isPrimaryKey && !$r) {
                $safe[] = $column->name;
            }
        }
        if ($required !== array()) {
            $rules[] = "array('".implode(', ', $required)."', 'required')";
        }
        if ($integers !== array()) {
            $rules[] = "array('".implode(', ', $integers)."', 'numerical', 'integerOnly'=>true)";
        }
        if ($numerical !== array()) {
            $rules[] = "array('".implode(', ', $numerical)."', 'numerical')";
        }
        if ($length !== array()) {
            foreach ($length as $len => $cols) {
                $rules[] = "array('".implode(', ', $cols)."', 'length', 'max'=>$len)";
            }
        }
        if ($safe !== array()) {
            $rules[] = "array('".implode(', ', $safe)."', 'safe')";
        }

        return $rules;
    }

    /**
     * Generates an appropriate label for a field of the element.
     *
     * @param string $modelClass Name of the element's model class
     * @param string $column     Name of a table colum
     *
     * @return string A label
     */
    public function generateActiveLabel($modelClass, $column)
    {
        return "\$form->labelEx(\$model,'{$column->name}')";
    }

    /**
     * Generates an appropriate input field for _form view.
     *
     * @param string $modelClass Name of the element's model class
     * @param string $column     Name of a table colum
     *
     * @return string A form input element
     */
    public function generateActiveField($modelClass, $column)
    {
        // MySQL specific field generation
        if (get_class($column) === 'CMysqlColumnSchema') {
            // Text area
            if (stripos($column->dbType, 'text') !== false) {
                return "echo \$form->textArea(\$model,'{$column->name}',array('rows'=>6, 'cols'=>50))";
            }
            // Boolean
            elseif ($column->dbType === 'tinyint(1)') {
                return "echo \$form->checkBox(\$model,'{$column->name}')";
            }
            // Date
            elseif ($column->dbType === 'date') {
                return "\$this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'model'=>\$model,
                    'attribute'=>'{$column->name}',
                    'value'=>\$model->{$column->name},
                    // additional javascript options for the date picker plugin
                    'options'=>array(
                                     'showAnim'=>'fold',
                                     'showButtonPanel'=>true,
                                     'autoSize'=>true,
                                     'dateFormat'=>'yy-mm-dd',
                                     'defaultDate'=>\$model->{$column->name},
                                     ),
                    ))";
            }
            // Everything else is a string
            else {
                if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                    $inputField = 'passwordField';
                } else {
                    $inputField = 'textField';
                }

                if ($column->type !== 'string' || $column->size === null) {
                    return "echo \$form->{$inputField}(\$model,'{$column->name}')";
                } else {
                    if (($size = $maxLength = $column->size) > 60) {
                        $size = 60;
                    }

                    return "echo \$form->{$inputField}(\$model,'{$column->name}',array('size'=>$size,'maxlength'=>$maxLength))";
                }
            }
        }
        // Other databases here
        else {
            if ($column->type === 'boolean') {
                return "\$form->checkBox(\$model,'{$column->name}')";
            } elseif (stripos($column->dbType, 'text') !== false) {
                return "\$form->textArea(\$model,'{$column->name}',array('rows'=>6, 'cols'=>50))";
            } else {
                if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                    $inputField = 'passwordField';
                } else {
                    $inputField = 'textField';
                }

                if ($column->type !== 'string' || $column->size === null) {
                    return "\$form->{$inputField}(\$model,'{$column->name}')";
                } else {
                    if (($size = $maxLength = $column->size) > 60) {
                        $size = 60;
                    }

                    return "\$form->{$inputField}(\$model,'{$column->name}',array('size'=>$size,'maxlength'=>$maxLength))";
                }
            }
        }
    }

    /**
     * Reset the time stamps.
     */
    public function resetTimeStamps()
    {
        if ($this->status == $this::STATUS_NEW) {
            unset(Yii::app()->session['create']);
            unset(Yii::app()->session['create_site_element_types_for']);
        }
    }

    /**
     * Creates time stamp in format expected for ./yiic migrate
     * Stores in a session variable, otherwise time stamp changes on each second call.
     */
    protected function createTimeStamp($description)
    {
        if (isset(Yii::app()->session[$description])) {
            $returnStamp = Yii::app()->session[$description];
        } else {
            $returnStamp = date('ymd_His');
            Yii::app()->session[$description] = $returnStamp;
        }

        return $returnStamp;
    }

    /**
     * Generates a time stamped migration file name in format expected by ./yiic migrate.
     */
    protected function generateMigrationName($description, $tableName)
    {
        return 'm'.$this->createTimeStamp($description).'_'.$description.'_'.$tableName;
    }

    /*
     * Generates Yii style controller name from class name
     *
     * @param string $modelClass Name of the table
     * @return string The name of the controller
     */
    protected function generateControllerName($modelClass)
    {
        return ucfirst($modelClass).'Controller';
    }

    /**
     * Checks to determine whether file should be saved.
     *
     * @param CCodeFile $file The file to be saved
     *
     * @return bool Whether the code file should be saved
     */
    public function confirmed($file)
    {
        return $this->answers === null && $file->operation === CCodeFile::OP_NEW
            || is_array($this->answers) && isset($this->answers[md5($file->path)]);
    }

    /**
     * Saves the generated views code into files
     * NB Ensure _www has write permissions for elements directory.
     *
     * @return bool True if save is successful
     */
    public function save()
    {
        // Boolean indicating success
        $result = true;

        // Create a directory structure for the views
        if ($this->level == $this::CREATE_FILES) {
            // Construct path for views
            $viewPath = Yii::app()->getViewPath().'/elements/'.$this->modelClass;

            // Create directory structure for the views
            $dir = new CCodeFile($viewPath, null);
            $result = $dir->save() && $result;
            $dir = new CCodeFile($viewPath.'/_form', null);
            $result = $dir->save() && $result;
            $dir = new CCodeFile($viewPath.'/_view', null);
            $result = $dir->save() && $result;
        }

        // Write files
        foreach ($this->files as $file) {
            if ($this->confirmed($file)) {
                $result = $file->save() && $result;
            }
        }

        return $result;
    }

    /**
     * Write results and run migration scripts.
     *
     * @return string the code generation result log.
     */
    public function renderResults()
    {
        $success = false;

        $output = 'Generating code using template "'.$this->templatePath."\"...\n";
        foreach ($this->files as $file) {
            if ($file->error !== null) {
                $output .= "<span class=\"error\">generating {$file->relativePath}<br/>           {$file->error}</span>\n";
            } elseif ($file->operation === CCodeFile::OP_NEW && $this->confirmed($file)) {
                $output .= ' generated '.$file->relativePath."\n";
                $success = true;
            } elseif ($file->operation === CCodeFile::OP_OVERWRITE && $this->confirmed($file)) {
                $output .= ' overwrote '.$file->relativePath."\n";
                $success = true;
            } else {
                $output .= '   skipped '.$file->relativePath."\n";
                $success = true;
            }
        }
        $output .= "\n";

        // Run migration scripts
        if ($this->level == $this::CREATE_MIGRATION && $success) {
            // Append message to output
            $output .= "Running ./yiic migrate command...\n";

            // Get path for yiic
            $protected = Yii::getPathOfAlias('application');

            // Run migration script, piping "y" to command
            $output .= shell_exec('cd '.$protected.'; echo "y" |./yiic migrate;');

            // Save element name, table name and level as session variables, going to next level
            Yii::app()->session['elementName'] = $this->elementName;
            Yii::app()->session['tableName'] = $this->tableName;
            Yii::app()->session['level'] = $this::CREATE_FILES;
        }

        // File generation complete
        elseif ($this->level == $this::CREATE_FILES && $success) {
            // Append message to output
            $output .= "Done!\n";

            // Reset
            $this->resetGenerator();
        } else {
            $output .= "Error in file generation\n";
        }

        return $output;
    }

    /*
     * Returns a success message
     *
     * @return string Message displayed on successful file generation
     */
    public function successMessage()
    {
        if ($this->level == $this::CREATE_MIGRATION) {
            return "The migration scripts and table have been generated successfully - click 'Preview' to continue";
        } elseif ($this->level == $this::CREATE_FILES) {
            return 'The model and view files have been generated successfully';
        }
    }

    /*
     * Resets all session variables to allow restart in case of error
     */
    public function resetGenerator()
     {
        // Reset session variables
           unset(Yii::app()->session['elementName']);
        unset(Yii::app()->session['tableName']);
        unset(Yii::app()->session['level']);
        unset(Yii::app()->session['create']);
        unset(Yii::app()->session['create_site_element_types_for']);
    }
}
