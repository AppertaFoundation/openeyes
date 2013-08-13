<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Laser extends Page
{
    protected $elements = array(
        'laserSiteID' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_site_id']"),
        'laserID' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_laser_id']"),
        'laserSurgeon' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_surgeon_id']"),

        'rightProcedure' => array('xpath' => "//*[@id='div_Element_OphTrLaser_Treatment_Procedures']/div[2]/select"),
        'leftProcedure' => array('xpath' => "//*[@id='div_Element_OphTrLaser_Treatment_Procedures']/div[2]/select"),
    );

    //Need to identify between Right/Left above
}
