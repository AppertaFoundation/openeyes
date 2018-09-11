<?php

use Behat\Behat\Exception\BehaviorException;

class LaserModule extends OpenEyesPage
{
    protected $path = '/site/OphTrLaser/Default/create?patient_id={patientId}';

    protected $elements = array(

        'laserSiteID' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_site_id']"),
        'laserID' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_laser_id']"),
        'laserSurgeon' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_surgeon_id']"),
        'rightProcedure' => array('xpath' => ".//*[@id='treatment_right_procedures']"),
        'leftProcedure' => array('xpath' => "//*[@id='treatment_left_procedures']"),
        'saveLaser' => array('xpath' => ".//*[@id='et_save']"),
        'siteValidationError' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Site: Site cannot be blank.')]"),
        'laserValidationError' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Site: Laser cannot be blank.')]"),
        'treatmentLeftValidationError' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Treatment: Left Procedures cannot be blank.')]"),
        'treatmentRightValidationError' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Treatment: Right Procedures cannot be blank.')]"),
        'removeLastProcedure' => array('xpath' => "//a[contains(text(),'Remove')]"),
        'removeRightEye' => array('xpath' => "//*[@class='js-element-eye right-eye column side left']//a[contains(text(),'Remove eye')]"),
        'addRightEye' => array('xpath' => "//*[@class='js-element-eye right-eye column side left inactive']//a[contains(text(),'Add right side')]"),
        'expandComments' => array('xpath' => "//*[@class='optional-elements-list']//a[contains(text(),'Comments')]"),
        'commentsField' => array('xpath' => "//*[@id='Element_OphTrLaser_Comments_comments']"),
        'collapseComments' => array('xpath' => "//*[@class='icon-button-small-mini-cross']"),
                'createLaserEvent' => array('css' => '.episode-links a:nth-child(1)'),
    );

    public function createLaserEvent()
    {
        $this->getElement('createLaserEvent')->click();
    }

    public function laserSiteID($site)
    {
        $this->getElement('laserSiteID')->selectOption($site);
    }

    public function laserID($ID)
    {
        $this->getElement('laserID')->selectOption($ID);
    }

    public function laserSurgeon($surgeon)
    {
        $this->getElement('laserSurgeon')->selectOption($surgeon);
    }

    public function rightProcedure($right)
    {
        $this->getElement('rightProcedure')->selectOption($right);
    }

    public function leftProcedure($left)
    {
        $this->getElement('leftProcedure')->selectOption($left);
    }

    public function saveLaser()
    {
        $this->getElement('saveLaser')->click();
    }

    public function laserValidationError()
    {
        return (bool) $this->find('xpath', $this->getElement('siteValidationError')->getXpath()) &&
        (bool) $this->find('xpath', $this->getElement('laserValidationError')->getXpath()) &&
        (bool) $this->find('xpath', $this->getElement('treatmentLeftValidationError')->getXpath()) &&
        (bool) $this->find('xpath', $this->getElement('treatmentRightValidationError')->getXpath());
    }

    public function laserValiditionCheck()
    {
        if ($this->laserValidationError()) {
            echo 'Laser validation errors have been displayed correctly';
        } else {
            throw new BehaviorException('LASER VALIDATION ERRORS HAVE NOT BEEN DISPLAYED CORRECTLY');
        }
    }

    public function removeLastProcedure()
    {
        $this->getElement('removeLastProcedure')->click();
    }

    public function removeRightEye()
    {
        $this->getElement('removeRightEye')->click();
    }

    public function addRightEye()
    {
        $this->getElement('addRightEye')->click();
    }

    public function expandComments()
    {
        $this->getElement('expandComments')->click();
        $this->getSession()->wait(5000, '$.active == 0');
    }

    public function addComments($comments)
    {
        $this->getElement('commentsField')->setValue($comments);
    }

    public function removeComments()
    {
        $this->getElement('collapseComments')->click();
    }
}
