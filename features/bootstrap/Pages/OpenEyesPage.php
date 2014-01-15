<?php
use \SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use \Behat\Mink\Exception\ElementTextException;

abstract class OpenEyesPage extends Page{

	/**
	 * @description checks that the title is equal to the expected value
	 * @param string $expectedTitle - the string value
	 * @return bool
	 * @throws Behat\Mink\Exception\ElementTextException
	 */

	public function checkOpenEyesTitle($expectedTitle){
		$titleElement = $this->find('css', 'h1.badge');
		$title = trim( $titleElement->getHtml() );
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

	/**
	 * Clicks link with specified locator.
	 *
	 * @param string $locator link id, title, text or image alt
	 *
	 * @throws ElementNotFoundException
	 */
	public function clickLink($locator){
		$this->scrollWindowToLink($locator);
		parent::clickLink($locator);
	}

	/**
	 * Scrolls the window to ensure the element is within the viewport.
	 * @param  string $xpath The element xpath string.
	 */
	public function scrollWindowTo($xpath) {
		$element = new Behat\Mink\Element\NodeElement($xpath, $this->getSession());
		$this->scrollWindowToElement($element);
	}

	/**
	 * Scrolls the window to ensure the element is within the viewport.
	 *
	 * @param string $locator link id, title, text or image alt
	 *
	 * @throws ElementNotFoundException
	 */
	public function scrollWindowToLink($locator) {

		$element = $this->findLink($locator);

		if ($element === null) {
			throw new ElementNotFoundException(
				$this->getSession(), 'element', 'id|title|alt|text', $locator
			);
		}

		$this->scrollWindowToElement($element);
	}

	/**
	 * Scrolls the window to ensure the element is within the viewport.
	 * @param  Behat\Mink\Element\NodeElement $element The element to scroll to.
	 */
	public function scrollWindowToElement(Behat\Mink\Element\NodeElement $element) {
		$wdSession = $this->getSession()->getDriver()->getWebDriverSession();
		$element = $wdSession->element('xpath', $element->getXpath());
		$elementID = $element->getID();
		$script = <<<JS
var element = $(arguments[0]);
var t = element.offset().top - (element.height() / 2);

// First we scroll the element to view and trigger the scroll event so that
// the sticky elements are initiated.
$(window).scrollTop(t).trigger('scroll');

// Now we offset the height of the sticky elements.
$('.stuck').not('.watermark').each(function() { t -= $(this).outerHeight(true, true); });
$(window).scrollTop(t);

JS;
		$wdSession->execute(array(
			'script' => $script,
			'args'   => array(array('ELEMENT' => $elementID))
		));
	}

	private function getWaitTime($waitTime){
		return $waitTime = $waitTime != null ? (int) $waitTime : 15000;
	}
}