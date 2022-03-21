<?php

/**
 * Base class for search engine hooks to use when performing an advanced search.
 */

abstract class SearchProvider extends CApplicationComponent
{
    /**
     * Perform a search using the specified parameters. Call this function to run the search rather than executeSearch.
     * @param $parameters array A list of CaseSearchParameter objects representing a search parameter.
     * @return mixed Search results. This will take the form specified within the subclass' executeSearch implementation
     * @throws Exception
     */
    final public function search(array $parameters)
    {
        $this->beforeSearch($parameters);
        $results = $this->executeSearch($parameters);
        $this->afterSearch($parameters, count($results));
        return $results;
    }

    /**
     * Override this function to customise the behaviour of the search provider before performing the search.
     * @param $parameters CaseSearchParameter[]
     */
    protected function beforeSearch(array $parameters) : void
    {
    }

    /**
     * Override this function to customise the behaviour of the search provider after performing the search.
     * This function can be used to clean up resources
     * and is currently used to record audit data if the search returns no results.
     * @param $parameters CaseSearchParameter[] List of parameters
     * @param $result_count int Number of results
     * @throws Exception
     */
    protected function afterSearch(array $parameters, int $result_count) : void
    {
        $auditValues = array();
        if ($result_count === 0) {
            foreach ($parameters as $param) {
                $auditValues[] = $param->getAuditData();
            }
            Audit::add(
                'case-search',
                'search-results',
                implode(' AND ', $auditValues) . '. No results',
                null,
                array('module' => 'OECaseSearch')
            );
        }
    }

    /**
     * Search delegate function. Implement this function to specify how the search will be executed.
     * @param $criteria CaseSearchParameter[] A list of search parameters.
     * @return mixed Search results.
     */
    abstract protected function executeSearch(array $criteria);

    /**
     * Delegate function for retrieving variable data.
     * Implement this function to specify how to retrieve data for selected variables.
     * @param $variables CaseSearchVariable|CaseSearchVariable[] A list of variables
     * @param null|DateTime $start_date
     * @param null|DateTime $end_date
     * @param bool $return_csv
     * @param string $mode
     * @return array An array of arrays of variable data for each variable.
     */
    abstract public function getVariableData(
        $variables,
        ?DateTime $start_date = null,
        ?DateTime $end_date = null,
        bool $return_csv = false,
        string $mode = 'BASIC'
    ): array;
}
