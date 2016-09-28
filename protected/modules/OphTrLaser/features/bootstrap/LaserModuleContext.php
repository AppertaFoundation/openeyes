<?php

use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class LaserModuleContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {
    }

    /**
     * @Then /^I create a Laser Event$/
     */
    public function iCreateALaserEvent()
    {
        /*
         * @var laser $laserModulePage
         */
        $laserPage = $this->getPage('laserModule');
        $laserPage->createLaserEvent();
    }
}
