<?php
use Behat\Behat\Exception\BehaviorException;

class OperationNote extends OpenEyesPage
{
    protected $path = "/site/OphTrOperationbooking/Default/create?patient_id={parentId}";

    protected $elements = array(

        'emergencyBooking' => array('xpath' => "//*[@value='emergency']"),
        'createOperationNote' => array('xpath' => "//*[@form='operation-note-select']"),
        'rightProcedureEye' => array('xpath' => "//*[@id='Element_OphTrOperationnote_ProcedureList_eye_id_2']"),
        'leftProcedureEye' => array('xpath' => "//*[@id='Element_OphTrOperationnote_ProcedureList_eye_id_1']"),
        'commonProcedure' => array('xpath' => "//*[@id='select_procedure_id_procs']"),
        'anaestheticTopical' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_1']"),
        'anaestheticLA' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_3']"),
        'anaestheticLAC' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_2']"),
        'anaestheticLAS' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_4']"),
        'anaestheticGA' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_5']"),
        'givenAnaesthetist' =>array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_1']"),
        'givenSurgeon' =>array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_2']"),
        'givenNurse' =>array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_3']"),
        'givenAnaestheticTechnician' =>array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_4']"),
        'givenAnaestheticOther' =>array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_5']"),
        'deliveryRetrobulbar' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_1']"),
        'deliveryPeribulbar' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_2']"),
        'deliverySubtenon' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_3']"),
        'deliverySubconjunctival' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_4']"),
        'deliveryTopical' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_5']"),
        'deliveryTopicalIntracameral' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_6']"),
        'deliveryOther' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_7']"),
        'anaestheticAgents' => array('xpath' => "//*[@id='AnaestheticAgent']"),
        'complications' => array('xpath' => "//*[@id='OphTrOperationnote_AnaestheticComplications']"),
        'anaestheticComments' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment']"),
        'surgeon' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Surgeon_surgeon_id']"),
        'supervisingSurgeon' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Surgeon_supervising_surgeon_id']"),
        'assistant' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Surgeon_assistant_id']"),
        'perOpDrug' => array('xpath' => "//*[@id='Drug']"),
        'operationComments' => array('xpath' => "//*[@id='Element_OphTrOperationnote_Comments_comments']"),
        'postOpInstructions' => array('xpath' => "//*[@id='dropDownTextSelection_Element_OphTrOperationnote_Comments_postop_instructions']"),
        'saveOpNote' => array('xpath' => "//*[@id='et_save']"),
        'savedOkMessage' => array('xpath' => "//*[@id='flash-success'][@class='alert-box with-icon info'][contains(text(), 'Operation Note created.')]")
    );

    public function emergencyBooking ()
    {
        $this->getElement('emergencyBooking')->check();
    }

    public function createOperationNote ()
    {
        $this->getElement('createOperationNote')->click();
    }

    public function procedureRightEye ()
    {
        $this->getElement('leftProcedureEye')->check();
    }

    public function procedureLeftEye ()
    {
        $this->getElement('rightProcedureEye')->check();
    }

    public function commonProcedure ($common)
    {
        $this->getElement('commonProcedure')->selectOption($common);
    }

    public function typeTopical ()
    {
        $this->getElement('anaestheticTopical')->check();
    }

    public function typeLA ()
    {
        $this->getElement('anaestheticLA')->check();
    }

    public function typeLAC ()
    {
        $this->getElement('anaestheticLAC')->check();
    }

    public function typeLAS ()
    {
        $this->getElement('anaestheticLAS')->check();
    }

    public function typeGA()
    {
        $this->getElement('anaestheticGA')->check();
    }

    public function givenAnaesthetist ()
    {
        $this->getElement('givenAnaesthetist')->check();
    }

    public function givenSurgeon ()
    {
        $this->getElement('givenSurgeon')->check();
    }

    public function givenNurse()
    {
        $this->getElement('givenNurse')->check();
    }

    public function givenAnaesthetistTechnician ()
    {
        $this->getElement('givenAnaestheticTechnician')->check();
    }

    public function givenOther()
    {
        $this->getElement('givenAnaestheticOther')->check();
    }

    public function deliveryRetrobulbar()
    {
        $this->getElement('deliveryRetrobulbar')->check();
    }

    public function deliveryPeribulbar()
    {
        $this->getElement('deliveryPeribulbar')->check();
    }

    public function deliverySubtenon()
    {
        $this->getElement('deliverySubtenon')->check();
    }

    public function deliverySubconjunctival()
    {
        $this->getElement('deliverySubconjunctival')->check();
    }

    public function deliveryTopical()
    {
        $this->getElement('deliveryTopical')->check();
    }

    public function deliveryTopicalIntracameral()
    {
        $this->getElement('deliveryTopicalIntracameral')->check();
    }

    public function deliveryOther()
    {
        $this->getElement('deliveryOther')->check();
    }

    public function anaestheticAgent ($agent)
    {
//        $this->getElement('anaestheticAgents')->selectOption($agent);
        #TODO TEST DATA REQUIRED HERE
    }

    public function complications ($complication)
    {
        $this->getElement('complications')->selectOption($complication);
    }

    public function anaestheticComments ($comments)
    {
        $this->getElement('anaestheticComments')->setValue($comments);
    }

    public function surgeon($surgeon)
    {
        $this->getElement('surgeon')->selectOption($surgeon);
    }

    public function supervisingSurgeon ($super)
    {
        $this->getElement('supervisingSurgeon')->selectOption($super);
    }

    public function assistant ($assistant)
    {
        $this->getElement('assistant')->selectOption($assistant);
    }

    public function perOpDrug ($drug)
    {
//        $this->getElement('perOpDrug')->selectOption($drug);
          #TODO TEST DATA REQUIRED HERE
    }

    public function operationComments ($comments)
    {
        $this->getElement('operationComments')->setValue($comments);
    }

    public function postOpInstructions ($instructions)
    {
//        $this->getElement('postOpInstructions')->selectOption($instructions);
        #TODO TEST DATA REQUIRED HERE
    }

    public function saveOpNote ()
    {
        $this->getElement('saveOpNote')->click();
    }

    protected function hasOpNoteSaved ()
    {
        return (bool) $this->find('xpath', $this->getElement('savedOkMessage')->getXpath());;
    }

    public function saveOpNoteAndConfirm ()
    {
        $this->getElement('saveOpNote')->click();

        if ($this->hasOpNoteSaved()) {
            print "Operation Note has been saved OK";
        }

        else {
            throw new BehaviorException("WARNING!!!  Operation Note has NOT been saved!!  WARNING!!");
        }
    }



}