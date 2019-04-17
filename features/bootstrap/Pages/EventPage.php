<?php
/**
 * Created by PhpStorm.
 * User: fivium-isaac
 * Date: 13/02/19
 * Time: 12:36 PM
 */

use Behat\Behat\Exception\BehaviorException;

class EventPage extends OpenEyesPage
{
    public function __construct(\Behat\Mink\Session $session, \SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface $pageFactory, array $parameters = array())
    {
        parent::__construct($session, $pageFactory, $parameters);
        $this->elements = array_merge($this->elements, self::getPageElements());
    }

    /**
     * @return array of all elements
     */
    protected static function getPageElements()
    {
        return array(
            'saveBtn' => array(
                'xpath' => "//*[@id='et_save']"
            ),
            'saveOK' => array(
                'xpath' => "//*[@id='flash-success']"
            ),
        );
    }

    protected $elements = array();

    /**
     * @throws BehaviorException when save fails
     */
    public function saveAndConfirm()
    {
        $this->getSession()->wait(5000, 'window.$ && $.active ==0');
        $this->getSession()->executeScript('window.stop()');
        $this->saveEvent();
        if (!$this->eventSaved()) {
            throw new BehaviorException('Event has not been saved!');
        }
    }

    protected function eventSaved()
    {
        return ( bool )$this->find('xpath', $this->getElement('saveOK')->getXpath());
    }

    public function saveEvent()
    {
        $this->getElement('saveBtn')->click();
    }

}