<?php

/**
 * Interface DBProviderInterface
 */
interface DBProviderInterface
{
    /**
     * Generate a SQL fragment representing a self-contained query. This fragment can be used in a subquery or standalone.
     * @return string The constructed query string.
     */
    public function query();

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues();
}
