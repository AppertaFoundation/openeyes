<?php
use \SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use \Behat\Mink\Exception\ElementTextException;

abstract class OpenEyesPage extends Page{

	/**
	 * @description checks that the title is equal to the expected value
	 * @param string $expectedTitle - the string value
	 */
	public function checkOpenEyesTitle($expectedTitle){
		$titleElement = $this->find('css', 'h1.badge');
		$title = trim( $titleElement->getHtml() );
		echo "Title found is  " . $title . " and expected " . $expectedTitle . "\n";
		if($expectedTitle != $title){
			throw new ElementTextException("Title was  " . $title . " instead of " . $expectedTitle, $this->getSession(), $titleElement );
		}
		return true;
	}

	/**
	 * @description waits for the title of the page included in the OpenEyes specific class to become equal to the expected value
	 * @param string $title - the string value
	 * @param null|int $waitTime - optional wait time override. Default is $this->defaultWait
	 */
	public function waitForTitle($title, $waitTime = null){
		$condition = 'window.$ && $(\'h1.badge\').html() == \'' .  str_replace("'", "\'", $title) . '\'';
		$this->getSession()->wait($this->getWaitTime($waitTime) , $condition);
	}

	/**
	 * @description Wait for element identified by a css selector to have a display css value of 'block'
	 * @param string $selector -  css selector for the element we need to check for its display property
	 */
	public function waitForElementDisplayBlock($selector, $waitTime = null ){
		$this->getSession()->wait($this->getWaitTime($waitTime) , "window.$ && $('" . $selector . "').css('display') == 'block'");
	}

	/**
	 * @description Wait for element identified by a css selector to have a display css value of 'none'
	 * @param string $selector -  css selector for the element we need to check for its display property
	 */
	public function waitForElementDisplayNone($selector, $waitTime = null ){
		$this->getSession()->wait($this->getWaitTime($waitTime) , "window.$ && $('" . $selector . "').css('display') == 'none'");
	}

	private function getWaitTime($waitTime){
		return $waitTime = $waitTime != null ? (int) $waitTime : 15000;
	}
} 