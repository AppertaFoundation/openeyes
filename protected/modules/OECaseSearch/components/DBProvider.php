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
     * @param $variables CaseSearchVariable[]
     * @param null|DateTime $start_date
     * @param null|DateTime $end_date
     * @return array
     * @throws CException
     */
    public function getVariableData($variables, $start_date = null, $end_date = null)
    {
        $variable_data_list = array();
        foreach ($variables as $variable) {
            if ($variable instanceof DBProviderInterface) {
                $variable_data_list[$variable->field_name] = Yii::app()->db->createCommand($variable->query($this))
                    ->andWhere(':start_date IS NULL OR created_date > :start_date')
                    ->andWhere(':end_date IS NULL OR created_date < :end_date')
                    ->bindValues(
                        array_merge(
                            $variable->bindValues(),
                            array(
                                ':start_date' => !$start_date ? $start_date : $start_date->format('Y-m-d'),
                                ':end_date' => !$end_date ? $end_date : $end_date->format('Y-m-d')
                            )
                        )
                    )
                    ->queryAll();
            }
        }
        return $variable_data_list;
    }
}
