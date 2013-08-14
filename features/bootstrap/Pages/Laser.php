<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Laser extends Page
{
    protected $path ="/site/OphTrLaser/Default/create?patient_id=19434"; //TO CODE - default view and patient ID

    protected $elements = array(

        'laserSiteID' => array ('xpath' => "//*[@id='Element_OphTrLaser_Site_site_id']"),
        'laserID' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_laser_id']"),
        'laserSurgeon' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_surgeon_id']"),
        'rightProcedure' => array('xpath' => "//*[@id='div_Element_OphTrLaser_Treatment_Procedures']/div[2]/select"),
        'leftProcedure' => array('xpath' => "//*[@id='div_Element_OphTrLaser_Treatment_Procedures']/div[2]/select")
);

    public function laserSiteID ($site)
    {
        $this->getElement('laserSiteID')->selectOption($site);
    }

    public function laserID ($ID)
    {
        $this->getElement('laserID')->selectOption($ID);
    }

    public function laserSurgeon ($surgeon)
    {
        $this->getElement('laserSurgeon')->selectOption($surgeon);

    }

    public function rightProcedure ($right)
    {
        $this->getElement('rightProcedure')->selectOption($right);
    }

    public function leftProcedure ($left)
    {
        $this->getElement('leftProcedure')->selectOption($left);
    }


}
