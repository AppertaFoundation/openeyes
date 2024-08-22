<?php

/**
 * Class ActiveRecordTestCase
 * Base class for all unit tests that test active record subclasses. Includes a test function
 * to verify that database mandatory columns without a default value are given a 'required' validator to prevent bad data.
 */
abstract class ActiveRecordTestCase extends OEDbTestCase
{
    /**
     * @return CActiveRecord
     */
    abstract public function getModel();

    protected array $columns_to_skip = [];

    public function testTableExists()
    {
        $connection = $this->getDbConnection();
        $table_name = $this->getModel()->tableName();
        $this->assertNotNull($connection->schema->getTable($table_name), "Table '{$table_name}' not found, have you migrated?");
    }

    /**
     * @covers BaseActiveRecord::rules
     * @throws CException
     */
    public function testRules()
    {
        $rules_original = $this->getModel()->rules();
        $rules = array();

        $rules_original = array_column(
            array_filter(
                $rules_original,
                static function ($item) {
                    // Return only the 'required' rules array that is always validated.
                    // Scenario-based validation is meaningless against database mandatory values.
                    return $item[1] === 'required' && !array_key_exists('on', $item);
                }
            ),
            0
        );

        foreach ($rules_original as $rule) {
            $column_list = explode(', ', $rule);
            $rules = array_merge($rules, $column_list);
        }

        $rules = array_merge($rules, $this->columns_to_skip);

        /**
         * @var $command CDbCommand
         */
        $command = Yii::app()->db->createCommand()
            ->select('c.column_name')
            ->from('information_schema.columns c')
            ->where('c.table_name = :name', array(':name' => $this->getModel()->tableName()))
            ->andWhere('c.is_nullable = "NO"')
            ->andWhere('c.column_default IS NULL')
            ->andWhere('c.extra != "auto_increment"')
            ->andWhere('c.column_key = ""');

        $required_columns = array_column(
            $command->queryAll(),
            'column_name'
        );

        if (!empty($required_columns) && empty($rules) && empty($this->columns_to_skip)) {
            $this->fail('The following mandatory columns do not possess a required validator rule: ' . var_export($required_columns, true));
        } elseif (!empty($rules) && !empty($required_columns)) {
            // We use array_diff here because the rules may not be in the same order between the two arrays.
            $diff = array_diff($required_columns, $rules);
            $this->assertEmpty(
                $diff,
                'The following mandatory columns do not possess a required validator rule: ' . var_export($diff, true)
            );
        } else {
            // No mandatory columns that aren't ignored.
            $this->assertEmpty($required_columns);
        }
    }
}
