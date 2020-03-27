<?php

/**
 * Class SearchProvider
 */
abstract class SearchProvider extends CApplicationComponent
{
    /**
     * Perform a search using the specified parameters. Call this function to run the search rather than executeSearch.
     * @param $parameters array A list of CaseSearchParameter objects representing a search parameter.
     * @return mixed Search results. This will take whatever form is specified within the subclass' executeSearch implementation.
     */
    final public function search($parameters)
    {
        return $this->executeSearch($parameters);
    }

    /**
     * Search delegate function. Implement this function to specify how the search will be executed.
     * @param $criteria array A list of search parameters.
     * @return array Search results.
     */
    abstract protected function executeSearch($criteria);

    /**
     * Delegate function for retrieving variable data. Implement this function to specify how to retrieve data for selected variables.
     * @param $variables CaseSearchVariable|CaseSearchVariable[] A list of variables
     * @param null|DateTime $start_date
     * @param null|DateTime $end_date
     * @param bool $return_csv
     * @param string $mode
     * @return array An array of arrays of variable data for each variable.
     */
    abstract public function getVariableData($variables, $start_date = null, $end_date = null, $return_csv = false, $mode = 'BASIC');
}
