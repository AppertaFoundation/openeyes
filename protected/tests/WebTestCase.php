<?php

/**
 * Change the following URL based on your server configuration
 * Make sure the URL ends with a slash so that we can use relative URLs in test cases.
 */
define('TEST_BASE_URL', 'http://dev.openeyesunitest.com/index-test.php/');

/**
 * The base class for functional test cases.
 * In this class, we set the base URL for the test application.
 * We also provide some common methods to be used by concrete test classes.
 */
class WebTestCase extends CWebTestCase
{
    /**
     * Sets up before each test method runs.
     * This mainly sets the base URL for the test application.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->setBrowser('*chrome');
        $this->setBrowserUrl(TEST_BASE_URL);
    }
}
