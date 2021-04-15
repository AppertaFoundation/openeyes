<?php

/**
 * A SearchProvider subclass that searches the OpenEyes database using SQL.
 *
 * @property string $driver
 */

class DBProvider extends SearchProvider
{
    /**
     * @var string $driver_ Driver name.
     */
    private string $driver_ = 'mariadb';

    /**
     * Gets the driver name. Used as a magic method.
     * @return string Driver label
     */
    public function getDriver(): string
    {
        return $this->driver_;
    }

    /**
     * Sets the driver name. Used as a magic method.
     * Setter is used implicitly when specifying override values in config files.
     * @param $driver string Driver label.
     */
    public function setDriver(string $driver): void
    {
        $this->driver_ = $driver;
    }

    /**
     * Execute the search using the given parameters.
     * @param CaseSearchParameter[] $criteria The parameters to search with.
     * The parameters must implement the DBProviderInterface interface to be searched using this provider.
     * @return array|CDbDataReader|mixed The returned data from the search.
     */
    protected function executeSearch(array $criteria)
    {
        $groupedBinds = array();
        $queryStr = '';
        $pos = 0;

        // Construct the SQL search string using each parameter as a separate dataset merged using JOINs.
        foreach ($criteria as $param) {
            // Ignore any case search parameters that do not implement DBProviderInterface

            if ($param instanceof DBProviderInterface) {
                // Get the query component of the parameter,
                // append it in the correct manner and augment the list of binds.
                $queryStr .= ($pos === 0) ? "p.id IN ({$param->query()})" : " AND p.id IN ({$param->query()})";
                if (!empty($param->bindValues())) {
                    $groupedBinds[] = $param->bindValues();
                }
                $pos++;
            } else {
                // Log that the parameter does not contain the necessary interface and proceed to the next one.
                OELog::log('Search parameter does not implement DBProviderInterface: ' . $param->label);
            }
        }
        // Do not count the deleted patients.
        $queryStr .= ($pos === 0) ? 'p.deleted != 1' : ' AND p.deleted != 1';

        /// NOTE: Make use of the splat operator (as below) rather than using array_merge in a loop where possible. Much faster!
        /// Here we flatten the 2-dimensional grouped binds array into a 1-dimensional bind array that MySQL will recognise.
        $bindValues = array_merge([], ...$groupedBinds);

        return Yii::app()->db->createCommand()
            ->selectDistinct('p.id')
            ->from('patient p')
            ->where($queryStr)
            ->bindValues($bindValues)
            ->queryAll();
    }

    /**
     * Internal function to retrieve variable data for the selected variable.
     * @param CaseSearchVariable $variable Variable to retrieve data for.
     * @param null|DateTime $start_date Start date
     * @param null|DateTime $end_date End date
     * @param string|null $mode CSV display mode. Pass null if the data should not be in CSV format.
     * @return array The raw variable data for display in plots or CSV files.
     */
    private function getVariableDataInternal(
        CaseSearchVariable $variable,
        ?DateTime $start_date,
        ?DateTime $end_date,
        ?string $mode = null
    ): array {
        $data = array();
        if ($variable instanceof DBProviderInterface) {
            $variable->csv_mode = $mode;
            $data = Yii::app()->db->createCommand($variable->query())
                ->andWhere('p.deleted != 1')
                ->bindValues(
                    array_merge(
                        $variable->bindValues(),
                        array(
                            ':start_date' => $start_date ? $start_date->format('Y-m-d') : $start_date,
                            ':end_date' => $end_date ? $end_date->format('Y-m-d') : $end_date,
                        )
                    )
                )
                ->queryAll();
        }
        return $data;
    }

    /**
     * Get variable data for all selected variables
     * @param CaseSearchVariable|CaseSearchVariable[] $variables List of variables.
     * @param null|DateTime $start_date Start date
     * @param null|DateTime $end_date End date
     * @param bool $return_csv True if rendering a CSV file; otherwise false.
     * @param string $mode CSV render mode ('BASIC' or 'ADVANCED')
     * @return array Variable data for plotting. No data is returned if rendering a CSV.
     * @throws CException
     */
    public function getVariableData(
        $variables,
        ?DateTime $start_date = null,
        ?DateTime $end_date = null,
        bool $return_csv = false,
        string $mode = 'BASIC'
    ): array
    {
        $csv_columns = array();
        $variable_data_list = array();
        if ($return_csv) {
            if (is_array($variables)) {
                throw new CException('Unable to generate single CSV for multiple variables.');
            }
            $variables->csv_mode = $mode;

            if ($mode === 'BASIC') {
                $csv_columns = array($variables->label, 'bin count');
            } elseif ($mode === 'ADVANCED') {
                if ($variables->eye_cardinality) {
                    $csv_columns = array('NHS number', $variables->label, 'eye', 'date', 'time');
                } else {
                    $csv_columns = array('NHS number', $variables->label, 'date', 'time');
                }
            }

            $results = $this->getVariableDataInternal($variables, $start_date, $end_date, $mode);
            $output = fopen('php://output', 'wb') or die('Can\'t open php://output');
            header('Content-Type: application/csv');
            header("Content-Disposition:attachment;filename=search_results_{$variables->field_name}_$mode.csv");

            fputcsv($output, $csv_columns);
            foreach ($results as $result) {
                fputcsv($output, $result);
            }
            fclose($output) or die('Can\'t close php://output');
        }

        if (is_array($variables)) {
            foreach ($variables as $variable) {
                $var_data = $this->getVariableDataInternal($variable, $start_date, $end_date);
                $variable_data_list[$variable->field_name] = $var_data;
            }
        }

        return $variable_data_list;
    }
}
