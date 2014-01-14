<?php
use Behat\Behat\Exception\BehaviorException;

class Laser extends OpenEyesPage
{
    protected $path ="/site/OphTrLaser/Default/create?patient_id={patientId}";

    protected $elements = array(

        'laserSiteID' => array ('xpath' => "//*[@id='Element_OphTrLaser_Site_site_id']"),
        'laserID' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_laser_id']"),
        'laserSurgeon' => array('xpath' => "//*[@id='Element_OphTrLaser_Site_surgeon_id']"),
        'rightProcedure' => array('xpath' => ".//*[@id='treatment_right_procedures']"),
        'leftProcedure' => array('xpath' => "//*[@id='treatment_left_procedures']"),
        'saveLaser' => array('xpath' => ".//*[@id='et_save']"),
        'siteValidationError' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Site: Site cannot be blank.')]"),
        'laserValidationError' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Site: Laser cannot be blank.')]"),
        'treatmentLeftValidationError' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Treatment: Left Procedures cannot be blank.')]"),
        'treatmentRightValidationError' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Treatment: Right Procedures cannot be blank.')]")
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

    public function saveLaser ()
    {
        $this->getElement('saveLaser')->click();
    }

    public function laserValidationError ()
    {
        return (bool) $this->find('xpath', $this->getElement('siteValidationError')->getXpath()) &&
        (bool) $this->find('xpath', $this->getElement('laserValidationError')->getXpath()) &&
        (bool) $this->find('xpath', $this->getElement('treatmentLeftValidationError')->getXpath()) &&
        (bool) $this->find('xpath', $this->getElement('treatmentRightValidationError')->getXpath());
    }

    public function laserValiditionCheck ()
    {
        if ($this->laserValidationError()){
            print "Laser validation errors have been displayed correctly";
        }
        else{
            throw new BehaviorException ("LASER VALIDATION ERRORS HAVE NOT BEEN DISPLAYED CORRECTLY");
        }
    }


}
