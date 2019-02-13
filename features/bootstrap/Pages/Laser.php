<?php
use Behat\Behat\Exception\BehaviorException;
class Laser extends EventPage {
	protected $path = "/site/OphTrLaser/Default/create?patient_id={patientId}";

    public function __construct(\Behat\Mink\Session $session, \SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface $pageFactory, array $parameters = array())
    {
        parent::__construct($session, $pageFactory, $parameters);
        $this->elements = array_merge($this->elements, self::getPageElements());
    }

    protected static function getPageElements()
    {
        return array(

            'laserSiteID' => array(
                'xpath' => "//*[@id='Element_OphTrLaser_Site_site_id']"
            ),
            'laserID' => array(
                'xpath' => "//*[@id='Element_OphTrLaser_Site_laser_id']"
            ),
            'laserOperator' => array(
                'xpath' => "//*[@id='Element_OphTrLaser_Site_operator_id']"
            ),
            'rightProcedure' => array(
                'xpath' => ".//*[@id='treatment_right_procedures']"
            ),
            'leftProcedure' => array(
                'xpath' => "//*[@id='treatment_left_procedures']"
            ),
            'siteValidationError' => array(
                //'xpath' => "//*[@class='alert-box errorlink with-icon']//*[contains(text(),'Site: Site cannot be blank.')]"
                'xpath' => "//*[contains(text(),'Site cannot be blank.')]"
            ),
            'laserValidationError' => array(
                'xpath' => "//*[contains(text(),'Laser cannot be blank.')]"
            ),
            'treatmentLeftValidationError' => array(
                'xpath' => "//*[contains(text(),'Left Procedures cannot be blank.')]"
            ),
            'treatmentRightValidationError' => array(
                'xpath' => "//*[contains(text(),'Right Procedures cannot be blank.')]"
            ),
            'removeLastProcedure' => array(
                'xpath' => "//a[contains(text(),'Remove')]"
            ),
            'removeRightEye' => array(
                'xpath' => "//*[@class='js-element-eye right-eye column side left']//a[contains(text(),'Remove eye')]"
            ),
            'addRightEye' => array(
                'xpath' => "//*[@class='js-element-eye right-eye column side left inactive']//a[contains(text(),'Add right side')]"
            ),
            'expandComments' => array(
                'xpath' => "//*[@class='optional-elements-list']//a[contains(text(),'Comments')]"
            ),
            'commentsField' => array(
                'xpath' => "//*[@id='Element_OphTrLaser_Comments_comments']"
            ),
            'collapseComments' => array(
                //'xpath' => "//*[@class='icon-button-small-mini-cross']"
                'xpath' => "//*[@class='button button-icon small js-remove-element 1']//*[@class='icon-button-small-mini-cross']"
            ),
            //add for laser test
            'addProcedureRightBtn' => array(
                'xpath' => "//*[@id='add-procedure-btn-right']"
            ),
            'addProcedureLeftBtn' => array(
                'xpath' => "//*[@id='add-procedure-btn-left']"
            ),
            'addProcedureList' => array(
                'css' => ".oe-add-select-search.auto-width"
            ),
            'addProcedureBtn' => array(
                'css' => '.add-icon-btn'
            ),
            'rightEyeColumn' => array(
                'css' => '.js-element-eye.right-eye.column.left.side'
            ),
            'leftEyeColumn' => array(
                'css' => '.js-element-eye.left-eye.column.right.side'
            )
        );
    }

	public function laserSiteID($site) {
		$this->getElement ( 'laserSiteID' )->selectOption ( $site );
	}
	public function laserID($ID) {
		$this->getElement ( 'laserID' )->selectOption ( $ID );
	}
	public function laserOperator($operator) {
		$this->getElement ( 'laserOperator' )->selectOption ( $operator );
	}

	public function rightProcedure($right) {
		//$this->getElement ( 'rightProcedure' )->selectOption ( $right );
        $this->getElement('addProcedureRightBtn')->click();
        $this->addProcedure($right,false);
	}
    public function leftProcedure($left) {
//		$this->getElement ( 'leftProcedure' )->selectOption ( $left );
        $this->getElement('addProcedureLeftBtn')->click();
        $this->addProcedure($left,true);
    }
    // add for laser test
	public function addProcedure($procedure,$left_indicator){
	    $procedure = str_replace(' '," ",$procedure);
        $this->elements['Laser_val'] = array(
            'css'=> 'li[data-label=\''.$procedure.'\']',
        );
        if ($left_indicator){
            $this->getElement('leftEyeColumn')->find('xpath',$this->getElement('addProcedureList')->getXpath())->find('xpath',$this->getElement('Laser_val')->getXpath())->click();
            $this->getElement('leftEyeColumn')->find('xpath',$this->getElement('addProcedureList')->getXpath())->find('xpath',$this->getElement('addProcedureBtn')->getXpath())->click();
        }else{
            $this->getElement('rightEyeColumn')->find('xpath',$this->getElement('addProcedureList')->getXpath())->find('xpath',$this->getElement('Laser_val')->getXpath())->click();
            $this->getElement('rightEyeColumn')->find('xpath',$this->getElement('addProcedureList')->getXpath())->find('xpath',$this->getElement('addProcedureBtn')->getXpath())->click();

        }
    }


	public function laserValidationError() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'siteValidationError' )->getXpath () ) && ( bool ) $this->find ( 'xpath', $this->getElement ( 'laserValidationError' )->getXpath () ) && ( bool ) $this->find ( 'xpath', $this->getElement ( 'treatmentLeftValidationError' )->getXpath () ) && ( bool ) $this->find ( 'xpath', $this->getElement ( 'treatmentRightValidationError' )->getXpath () );
	}
	
	public function laserValidationCheck() {
		if (!$this->laserValidationError ()) {
			throw new BehaviorException ( "LASER VALIDATION ERRORS HAVE NOT BEEN DISPLAYED CORRECTLY" );
		}
	}
	public function removeLastProcedure() {
		$this->getElement ( 'removeLastProcedure' )->click ();
	}
	public function removeRightEye() {
		$this->getElement ( 'removeRightEye' )->click ();
	}
	public function addRightEye() {
		$this->getElement ( 'addRightEye' )->click ();
	}
	public function expandComments() {
		$element = $this->getElement ( 'expandComments' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
	public function addComments($comments) {
		$this->getElement ( 'commentsField' )->setValue ( $comments );
	}
	public function removeComments() {
		$element = $this->getElement ( 'collapseComments' );
		//$this->scrollWindowToElement ( $element );
		$element->click();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
	}
}
