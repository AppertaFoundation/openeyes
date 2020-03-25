<?php

/**
 * Class DBProvider
 */
class DBProvider extends SearchProvider
{
    /**
     * @param array $criteria The parameters to search with. The parameters must implement the DBProviderInterface interface.
     * @return array The returned data from the search.
     * @throws CDbException
     * @throws CException
     */
    protected function executeSearch($criteria)
    {
        $bindValues = array();
        $queryStr = 'SELECT DISTINCT p.id FROM patient p ';
        $pos = 0;

        // Construct the SQL search string using each parameter as a separate dataset merged using JOINs.
        foreach ($criteria as $id => $param) {
            // Ignore any case search parameters that do not implement DBProviderInterface
            if ($param instanceof DBProviderInterface) {
                // Get the query component of the parameter, append it in the correct manner and augment the list of binds.
                $from = $param->query($this);
                $queryStr .= ($pos === 0) ? "WHERE p.id IN ($from)" : " AND p.id IN ($from)";
                $bindValues = array_merge($bindValues, $param->bindValues());
                $pos++;
            }
        }
        // Do not count the deleted patients.
        $queryStr .= 'AND p.deleted != 1';

        /**
         * @var CDbCommand $command
         */
        $command = Yii::app()->db->createCommand($queryStr)->bindValues($bindValues);
        $command->prepare();

        return $command->queryAll();
    }

    /**
     * @param CaseSearchVariable $variable
     * @param null|DateTime $start_date
     * @param null|DateTime $end_date
     * @param string $mode
     * @return array|null
     * @throws CException
     */
    private function getVariableDataInternal($variable, $start_date, $end_date, $mode = null)
    {
        $data = null;
        if ($variable instanceof DBProviderInterface) {
            $variable->csv_mode = $mode;
            $data = Yii::app()->db->createCommand($variable->query($this))
                ->andWhere(':start_date IS NULL OR created_date > :start_date')
                ->andWhere(':end_date IS NULL OR created_date < :end_date')
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
     * @param CaseSearchVariable[] $variables
     * @param null|DateTime $start_date
     * @param null|DateTime $end_date
     * @param bool $return_csv
     * @param string $mode
     * @return array
     * @throws CException
     */
    public function getVariableData($variables, $start_date = null, $end_date = null, $return_csv = false, $mode = 'BASIC')
    {
        $variable_data_list = array();
        $csv_columns = array('First Name', 'Surname');
        $select = 'c.first_name, c.last_name';
        $where = 'p_outer.id IN (' . implode(',', $variables[0]->id_list) . ')';
        foreach ($variables as $variable) {
            if ($return_csv) {
                $variable->csv_mode = $mode;
                if ($variable->eye_cardinality) {
                    foreach (array('L' => 'left', 'R' => 'right') as $eye_id => $eye) {
                        $variable->eye = $eye_id;
                        $csv_columns[] = ucfirst($eye) . ' ' . $variable->label;
                        $select .= ", ({$variable->query($this)}) {$eye}_{$variable->field_name}";
                    }
                } else {
                    $csv_columns[] = $variable->label;
                    $select .= ", ({$variable->query($this)}) {$variable->field_name}";
                }
            } else {
                if ($variable->eye_cardinality) {
                    foreach (array('L', 'R') as $eye) {
                        $variable->eye = $eye;
                        $var_data = $this->getVariableDataInternal($variable, $start_date, $end_date);
                        $variable_data_list[$variable->field_name][] = $var_data;
                    }
                } else {
                    $var_data = $this->getVariableDataInternal($variable, $start_date, $end_date);
                    $variable_data_list[$variable->field_name][] = $var_data;
                }
            }
        }
        if ($return_csv) {
            $results = Yii::app()->db->createCommand()
                ->select($select)
                ->from('patient p_outer')
                ->join('contact c', 'c.id = p_outer.contact_id')
                ->where($where)
                ->order('c.first_name ASC, c.last_name DESC')
                ->bindValues(array(
                    ':start_date' => $start_date ? $start_date->format('Y-m-d') : $start_date,
                    ':end_date' => $end_date ? $end_date->format('Y-m-d') : $end_date,
                ))
                ->queryAll();
            $output = fopen('php://output', 'w') or die ('Can\'t open php://output');
            header('Content-Type: application/csv');
            header("Content-Disposition:attachment;filename=search_results_$mode.csv");

            fputcsv($output, $csv_columns);
            foreach ($results as $result) {
                fputcsv($output, $result);
            }
            fclose($output) or die('Can\'t close php://output');
        }

        return $variable_data_list;
    }
}
