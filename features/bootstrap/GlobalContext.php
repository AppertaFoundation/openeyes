<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException,
	Behat\Behat\Context\Step;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ResponseTextException;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
//require_once 'mink/vendor/autoload.php';

/**
 * Features context.
 */
class GlobalContext extends Behat\MinkExtension\Context\MinkContext {
	
}