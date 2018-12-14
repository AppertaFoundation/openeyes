<?php
/**
 * Created by PhpStorm.
 * User: fivium
 * Date: 12/12/18
 * Time: 10:42 AM
 */
use Behat\Behat\Exception\BehaviorException;

class DeleteEvent extends OpenEyesPage {
    protected $path = "OphCoDocument/Default/create?patient_id={patientId}";
    protected $elements = array(
        'DeleteBtn'=>array(
            'xpath' => "//*[@id='js-delete-event-btn']"
        ),
        'EditBtn'=>array(
            'css' => ".button.header-tab"
        ),
    );

    public function selectEvent($event_id){

        $this->elements['SelectedEvent'] = array(
            'xpath'=>"//*[@id='js-sideEvent$event_id']",
        );;
        $this->getElement('SelectedEvent')->click();


    }

    public function selectDelete(){
        $this->getElement('DeleteBtn')->click();
        $this->elements['DeleteReason'] = array(
            'xpath'=>"//*[@id='js-text-area']"
        );
        $this->getElement('DeleteReason')->setValue('delete testing');
        $this->elements['FinalDeleteBtn'] = array(
            'xpath'=>"//*[@id='et_deleteevent']"
        );
        $this->getElement('FinalDeleteBtn')->click();
    }

    public function selectEdit(){
        $this->getElement('EditBtn')->click();
    }


}