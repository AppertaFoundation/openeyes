<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DefaultController extends BaseEventTypeController
{
    protected static $action_types = array(
        'previewApplication' => self::ACTION_TYPE_PRINT,
        'processApplication' => self::ACTION_TYPE_EDIT,
        'downloadFileCollection' => self::ACTION_TYPE_VIEW,
        'getDecisionTree' => self::ACTION_TYPE_FORM,
    );

    // TODO: check this is in line with Jamie's change circa 3rd April 2013
    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerScriptFile('js/spliteventtype.js', null, null, AssetManager::OUTPUT_SCREEN);

        return parent::beforeAction($action);
    }

    /**
     * function to define the js vars needed in editing a therapy application.
     */
    public function addEditJSVars()
    {
        $this->jsVars['decisiontree_url'] = Yii::app()->createUrl('OphCoTherapyapplication/default/getDecisionTree/');
        $this->jsVars['nhs_date_format'] = Helper::NHS_DATE_FORMAT_JS;
    }

    /**
     * ensure js vars are set before carrying out standard functionality.
     */
    public function initActionCreate()
    {
        $this->addEditJSVars();
        parent::initActionCreate();
    }

    /**
     * ensure js vars are set before carrying out standard functionality.
     */
    public function initActionUpdate()
    {
        $this->addEditJSVars();
        parent::initActionUpdate();
    }

    public function initActionPreviewApplication()
    {
        $this->initWithEventId(@$_REQUEST['event_id']);
    }

    /**
     * preview of the application - will generate both left and right forms into one PDF.
     *
     * @throws CHttpException
     */
    public function actionPreviewApplication()
    {
        $this->assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->getModule()->name . '.assets'), true, -1);

        $service = new OphCoTherapyapplication_Processor($this->event);
        $service->generatePreviewPdf($this);
    }

    public function initActionProcessApplication()
    {
        $this->initWithEventId(@$_REQUEST['event_id']);
    }

    /**
     * actually generates and submits the therapy application.
     *
     * @throws CHttpException
     */
    public function actionProcessApplication()
    {
        $service = new OphCoTherapyapplication_Processor($this->event);
        $user = null;
        if (@Yii::app()->params['OphCoTherapyapplication_cc_applicant']) {
            $user = User::model()->findByPk(Yii::app()->user->id);
        }
        if ($service->processEvent($this, $user)) {
            Yii::app()->user->setFlash('success', 'Application processed.');
        } else {
            Yii::app()->user->setFlash('error', 'Unable to process the application at this time.');
        }
        $this->redirect(array($this->successUri));
    }

    public function actionDownloadFileCollection($id)
    {
        if ($collection = OphCoTherapyapplication_FileCollection::model()->findByPk((int) $id)) {
            $pf = $collection->getZipFile();
            if ($pf) {
                $this->redirect($pf->getDownloadURL());
            }
        }
        throw new CHttpException('400', 'File Collection does not exist');
    }

    /**
     * ajax action to retrieve a specific decision tree (which can then be populated with appropriate default values.
     *
     * @throws CHttpException
     */
    public function actionGetDecisionTree()
    {
        if (!$this->patient = Patient::model()->findByPk((int) @$_GET['patient_id'])) {
            throw new CHttpException(403, 'Invalid patient_id.');
        }
        if (!$treatment = OphCoTherapyapplication_Treatment::model()->findByPk((int) @$_GET['treatment_id'])) {
            throw new CHttpException(403, 'Invalid treatment_id.');
        }

        $element = new Element_OphCoTherapyapplication_PatientSuitability();

        $side = @$_GET['side'];
        if (!in_array($side, array('left', 'right'))) {
            throw Exception('Invalid side argument');
        }

        $element->{$side.'_treatment'} = $treatment;

        $form = Yii::app()->getWidgetFactory()->createWidget($this, 'BaseEventTypeCActiveForm', array(
                'id' => 'clinical-create',
                'enableAjaxValidation' => false,
                'htmlOptions' => array('class' => 'sliding'),
        ));

        $this->renderPartial(
            'form_OphCoTherapyapplication_DecisionTree',
            array('element' => $element, 'form' => $form, 'side' => $side),
            false,
            false
        );
    }

    /**
     * extends the base function to set various defaults that depend on other events etc.
     *
     * @param BaseElement $element
     * @param string      $action
     */
    public function setElementDefaultOptions($element, $action)
    {
        parent::setElementDefaultOptions($element, $action);

        if ($action == 'create' && empty($_POST)) {
            switch (get_class($element)) {
                case 'Element_OphCoTherapyapplication_Therapydiagnosis':
                    $this->setDiagnosisDefaults($element);
                    break;
                case 'Element_OphCoTherapyapplication_MrServiceInformation':
                    $element->consultant_id = Yii::app()->session['selected_firm_id'];
                    $element->site_id = Yii::app()->session['selected_site_id'];
                    break;
            }
        }
    }

    /**
     * works out the node response value for the given node id on the element. Basically allows us to check for
     * submitted values, values stored against the element from being saved, or working out a default value if applicable.
     *
     * @param Element_OphCoTherapyapplication_PatientSuitability $element
     * @param string                                             $side
     * @param int                                                $node_id
     */
    public function getNodeResponseValue($element, $side, $node_id)
    {
        if (isset($_POST['Element_OphCoTherapyapplication_PatientSuitability'][$side.'_DecisionTreeResponse'])) {
            // responses have been posted, so should operate off the value for this node.
            return @$_POST['Element_OphCoTherapyapplication_PatientSuitability'][$side.'_DecisionTreeResponse'][$node_id];
        }
        foreach ($element->{$side.'_responses'} as $response) {
            if ($response->node_id == $node_id) {
                return $response->value;
            }
        }
        $node = OphCoTherapyapplication_DecisionTreeNode::model()->findByPk($node_id);

        return $node->getDefaultValue($side, $this->patient, $this->episode);
    }

    /**
     * @param $data
     * (non-phpdoc)
     *
     * @see parent::saveEventComplexAttributesFromData($data)
     */
    public function saveEventComplexAttributesFromData($data)
    {
        foreach ($this->open_elements as $el) {
            if (get_class($el) == 'Element_OphCoTherapyapplication_PatientSuitability') {
                // note we don't do this in POST Validation as we don't need to validate the values of the decision tree selection
                // this is really just for record keeping - we are mainly interested in whether or not it's got compliance value
                $el->updateDecisionTreeResponses(
                    Element_OphCoTherapyapplication_PatientSuitability::LEFT,
                    isset($data['Element_OphCoTherapyapplication_PatientSuitability']['left_DecisionTreeResponse']) ?
                        $data['Element_OphCoTherapyapplication_PatientSuitability']['left_DecisionTreeResponse'] :
                    array()
                );
                $el->updateDecisionTreeResponses(
                    Element_OphCoTherapyapplication_PatientSuitability::RIGHT,
                    isset($data['Element_OphCoTherapyapplication_PatientSuitability']['right_DecisionTreeResponse']) ?
                        $data['Element_OphCoTherapyapplication_PatientSuitability']['right_DecisionTreeResponse'] :
                    array()
                );
            }
        }
    }

    /**
     * After an update, mark any existing emails as archived.
     */
    protected function afterUpdateElements($event)
    {
        OphCoTherapyapplication_Email::model()->archiveForEvent($event);
    }

    /**
     * Set default values for the diagnosis element.
     *
     * This can't be done using setDefaultOptions on the element class because it needs to know about the episode
     *
     * @param Element_OphCoTherapyapplication_Therapydiagnosis $element
     */
    private function setDiagnosisDefaults(Element_OphCoTherapyapplication_Therapydiagnosis $element)
    {
        $episode = $this->episode;

        if ($episode) {
            // get the list of valid diagnosis codes
            $valid_disorders = OphCoTherapyapplication_TherapyDisorder::model()->findAll();
            $vd_ids = array();
            foreach ($valid_disorders as $vd) {
                $vd_ids[] = $vd->disorder_id;
            }

            // foreach eye
            $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
            foreach (array(Eye::LEFT, Eye::RIGHT) as $eye_id) {
                $prefix = $eye_id == Eye::LEFT ? 'left' : 'right';
                // get specific disorder from injection management

                if ($exam_api && $exam_imc = $exam_api->getInjectionManagementComplexInEpisodeForSide($this->patient, $prefix)) {
                    $element->{$prefix.'_diagnosis1_id'} = $exam_imc->{$prefix.'_diagnosis1_id'};
                    $element->{$prefix.'_diagnosis2_id'} = $exam_imc->{$prefix.'_diagnosis2_id'};
                }
                // check if the episode diagnosis applies
                elseif (($episode->eye_id == $eye_id || $episode->eye_id == Eye::BOTH)
                && in_array($episode->disorder_id, $vd_ids)) {
                    $element->{$prefix.'_diagnosis1_id'} = $episode->disorder_id;
                }
                // otherwise get ordered list of diagnoses for the eye in this episode, and check
                else {
                    if ($exam_api) {
                        $disorders = $exam_api->getOrderedDisorders($this->patient);
                        foreach ($disorders as $disorder) {
                            if (($disorder['eye_id'] == $eye_id || $disorder['eye_id'] == Eye::BOTH) && in_array($disorder['disorder_id'], $vd_ids)) {
                                $element->{$prefix.'_diagnosis1_id'} = $disorder['disorder_id'];
                                break;
                            }
                        }
                    }
                }
            }

            // set the correct eye_id on the element for rendering
            if (isset($element->left_diagnosis1_id) && isset($element->right_diagnosis1_id)) {
                $element->eye_id = Eye::BOTH;
            } elseif (isset($element->left_diagnosis1_id)) {
                $element->eye_id = Eye::LEFT;
            } elseif (isset($element->right_diagnosis1_id)) {
                $element->eye_id = Eye::RIGHT;
            }
        }
    }

    public function actionView($id)
    {
        $service = new OphCoTherapyapplication_Processor($this->event);
        $status = $service->getApplicationStatus();

        $this->title = $this->event_type->name.' ('.$status.')';

        return parent::actionView($id);
    }

    /**
     * Ensures all the missing element types are set on open_elements for editing.
     */
    protected function setRequiredEventElements()
    {
        $curr = $this->event->getElements();
        $all = $this->event_type->getDefaultElements();
        foreach ($all as $del) {
            if (count($curr) && get_class($curr[0]) == get_class($del)) {
                $this->open_elements[] = array_shift($curr);
            } else {
                $this->open_elements[] = $del;
            }
        }
    }

    /**
     * Extend base function to ensure there is always an exceptional circumstances for updates.
     */
    protected function setOpenElementsFromCurrentEvent($action)
    {
        if ($action == 'update') {
            $this->setRequiredEventElements();
            $this->setElementOptions($action);
        } else {
            parent::setOpenElementsFromCurrentEvent($action);
        }
    }

    /**
     * If a partially completed form is submitted, some of the required elements might not be present in the submission
     * this extension resolves this.
     *
     * @param array $data
     *
     * @return array
     */
    protected function setAndValidateElementsFromData($data)
    {
        $errors = parent::setAndValidateElementsFromData($data);
        if (!empty($errors)) {
            $all = $this->event_type->getDefaultElements();
            $curr = $this->open_elements;
            $update = array();
            foreach ($all as $del) {
                if (count($curr) && get_class($curr[0]) == get_class($del)) {
                    $update[] = array_shift($curr);
                } else {
                    $update[] = $del;
                }
            }
            $this->open_elements = $update;
        }

        return $errors;
    }
}
