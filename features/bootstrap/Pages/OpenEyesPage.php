<?php
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\Mink\Exception\ElementTextException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface;


class OpenEyesPage extends Page {
	public function __construct(Session $session, PageFactoryInterface $pageFactory, array $parameters = array())
    {
        parent::__construct($session, $pageFactory, $parameters);
    }


    /**
	 * ription checks that the title is equal to the expected value
	 * 
	 * @param string $expectedTitle
	 *        	- the string value
	 * @return bool
	 * @throws Behat\Mink\Exception\ElementTextException
	 */
    public function checkOpenEyesTitle($expectedTitle) {
        if (!$titleElement = $this->find ( 'css', 'title' )) {
            throw new ExpectationException("Could not find title element", $this->getDriver());
        }

        $title = trim ( $titleElement->getHtml () );
        if ($expectedTitle != $title) {
            throw new ElementTextException ( "Title was  " . $title . " instead of " . $expectedTitle, $this->getSession (), $titleElement );
        }
        return true;
    }
	
	/**
	 * ription waits for the title of the page included in the OpenEyes specific class to become equal to the expected value
	 * 
	 * @param string $title
	 *        	- the string value
	 * @param null|int $waitTime
	 *        	- optional wait time override. Default is $this->defaultWait
	 */
	public function waitForTitle($title, $waitTime = null) {
		$condition = 'window.$ && $(\'h1.badge\').html() == \'' . str_replace ( "'", "\'", $title ) . '\'';
		echo 'waiting for :'.$condition;
		$this->getSession ()->wait ( $this->getWaitTime ( $waitTime ), $condition );
	}
	
	/**
	 * ription Wait for element identified by a css selector to have a display css value of 'block'
	 * 
	 * @param string $selector
	 *        	- css selector for the element we need to check for its display property
	 */
	public function waitForElementDisplayBlock($selector, $waitTime = null) {
		$this->getDriver()->wait ( $this->getWaitTime ( $waitTime ), "window.$ && ($(\"" . $selector . "\").css('display') == 'block' || $(\"" . $selector . "\").css('display') == 'inline-block' )" );
	}
	
	/**
	 * ription Wait for element identified by a css selector to have a display css value of 'none'
	 * 
	 * @param string $selector
	 *        	- css selector for the element we need to check for its display property
	 */
	public function waitForElementDisplayNone($selector, $waitTime = null) {
		$this->getSession ()->wait ( $this->getWaitTime ( $waitTime ), "window.$ && $('" . $selector . "').css('display') == 'none'" );
	}
	
	/**
	 * Clicks link with specified locator.
	 *
	 * @param string $locator
	 *        	link id, title, text or image alt
	 *        	
	 * @throws ElementNotFoundException
	 */
	public function clickLink($locator) {
		$this->scrollWindowToLink ( $locator );
		parent::clickLink ( $locator );
	}
	
	/**
	 * Scrolls the window to ensure the element is within the viewport.
	 * 
	 * @param string $xpath
	 *        	The element xpath string.
	 */
	public function scrollWindowTo($xpath) {
		$element = new Behat\Mink\Element\NodeElement ( $xpath, $this->getSession () );
		$this->scrollWindowToElement ( $element );
	}
	
	
	
	/**
	 * Scrolls the window to ensure the element is within the viewport.
	 *
	 * @param string $locator
	 *        	link id, title, text or image alt
	 *        	
	 * @throws ElementNotFoundException
	 */
	public function scrollWindowToLink($locator) {
		$element = $this->findLink ( $locator );
		if ($element === null) {
			throw new ElementNotFoundException ( $this->getSession (), 'element', 'id|title|alt|text', $locator );
		}
		
		$this->scrollWindowToElement ( $element );
	}
	public function acceptAlert() {
		$wdSession = $this->getSession ()->getDriver ()->getWebDriverSession ();
		$wdSession->accept_alert();
	}
	/**
	 * Scrolls the window to ensure the element is within the viewport.
	 * 
	 * @param Behat\Mink\Element\NodeElement $element
	 *        	The element to scroll to.
	 */
	public function scrollWindowToElement(Behat\Mink\Element\NodeElement $element) {
		$wdSession = $this->getDriver ()->getWebDriverSession ();
		$element = $wdSession->element ( 'xpath', $element->getXpath () );
		$elementID = $element->getID ();
		$script = <<<JS
var element = $(arguments[0]);
var t = element.offset().top - (element.height() / 2);

// First we scroll the element to view and trigger the scroll event so that
// the sticky elements are initiated.
$(window).scrollTop(t).trigger('scroll');

// Now we offset the height of the sticky elements.
var new_t = t;
$('.stuck').not('.watermark').each(function() {
    if (t - this.getBoundingClientRect().bottom < new_t) { new_t = t - this.getBoundingClientRect().bottom; }});
$(window).scrollTop(new_t);

JS;
		$wdSession->execute ( array (
				'script' => $script,
				'args' => array (
						array (
								'ELEMENT' => $elementID 
						) 
				) 
		) );
	}
	private function getWaitTime($waitTime) {
		return $waitTime = $waitTime != null ? ( int ) $waitTime : 2000;
	}

    public function popupOk($element_name)
    {
        $element = $this->getElement($element_name);
        if (( bool ) $this->find ( 'xpath', $element->getXpath () )) {
            $element->click ();
        }
    }

    public function logout()
    {
        $this->getDriver()->visit($this->getParameter('base_url').'/site/logout');
    }
}