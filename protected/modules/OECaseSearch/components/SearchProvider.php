<?php

/**
 * Class SearchProvider
 *
 * @property-read string|int $providerID
 */
abstract class SearchProvider
{
    /**
     * @var string|int $_providerID Unique provider ID
     */
    private $_providerID;

    /**
     * SearchProvider constructor.
     * @param $id mixed An identifier uniquely identifying the search provider.
     */
    public function __construct($id)
    {
        $this->_providerID = $id;
    }

    /**
     * Magic get method to get the provider ID without a getter while also preventing setting it directly.
     * @param $name string The property name
     * @return string|int The value of the given property (if it exists).
     */
    final public function __get($name)
    {
        if ($name === 'providerID') {
            return $this->_providerID;
        }
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line']
        );

        return null;
    }

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
     * @param $variables CaseSearchVariable[] A list of variables
     * @param null|DateTime $start_date
     * @param null|DateTime $end_date
     * @return array An array of arrays of variable data for each variable.
     */
    abstract public function getVariableData($variables, $start_date = null, $end_date = null);
}
