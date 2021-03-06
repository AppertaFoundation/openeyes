<?php
const _AUTOCOMPLETE_LIMIT = 30;

class AutoCompleteController extends BaseModuleController
{
    /**
     * Ensure that actions in this controller are only executed via AJAX requests.
     * @return array The filters that apply to this controller.
     */
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + commonDiagnoses',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('commonDiagnoses', 'commonMedicines', 'commonAllergies', 'commonProcedures'),
                'users' => array('@'),
            ),
        );
    }

    /**
     * Get the first 30 diagnosis matches for the given text. This is executed through an implicit AJAX request from the CJuiAutoComplete widget.
     * @param $term string The term supplied from the JUI Autocomplete widget.
     */
    public function actionCommonDiagnoses($term)
    {
//        Made disorder lookups include fully specified name and aliases - CERA-527
        $disorders = Disorder::model()->findAllBySql('SELECT * FROM disorder WHERE LOWER(term) LIKE LOWER(:term) OR LOWER(aliases) LIKE LOWER(:term) OR LOWER(fully_specified_name) LIKE LOWER(:term) ORDER BY term LIMIT  ' . _AUTOCOMPLETE_LIMIT,
            array('term' => "%$term%"));
        $values = array();
        foreach ($disorders as $disorder) {
            $values[] = $disorder->term;
        }

        $this->renderJSON($values);
    }

    /**
     * Get the first 30 medicine matches for the given text. This is executed through an implicit AJAX request from the CJuiAutoComplete widget.
     * @param $term string The term supplied from the JUI Autocomplete widget.
     */
    public function actionCommonMedicines($term)
    {
        $drugs = Drug::model()->findAllBySql("
SELECT *
FROM drug d 
WHERE LOWER(d.name) LIKE LOWER(:term) ORDER BY d.name LIMIT 30", array('term' => "$term%"));

        $medicationDrugs = MedicationDrug::model()->findAllBySql('
SELECT *
FROM medication_drug md
WHERE LOWER(md.name) LIKE LOWER(:term) ORDER BY md.name LIMIT ' . _AUTOCOMPLETE_LIMIT, array('term' => "$term%"));

        $values = array();
        foreach ($drugs as $drug) {
            $values[$drug->name] = $drug->name;
        }

        foreach ($medicationDrugs as $medicationDrug) {
            // Filter out any duplicates.
            if (!isset($values[$medicationDrug->name])) {
                $values[$medicationDrug->name] = $medicationDrug->name;
            }
        }

        sort($values);

        $this->renderJSON($values);
    }

    /***
     * Returns a list of procedures given a search term
     *
     * @param $term String to be compared against to find matching procedures
     */
    public function actionCommonProcedures($term = '')
    {
        $criteria = new CDbCriteria();
        $criteria->limit = 15;
        $criteria->compare('term', $term, true);
        $procedures = Procedure::model()->findAll($criteria);

        $options = array();
        foreach ($procedures as $procedure) {
            $options[] = $procedure->term;
        }

        $criteria = new CDbCriteria();
        $criteria->limit = 15;
        $criteria->compare('operation', $term, true);
        $criteria->addNotInCondition('operation', $options);
        $past_ops = \OEModule\OphCiExamination\models\PastSurgery_Operation::model()->findAll($criteria);

        foreach ($past_ops as $op) {
            $options[] = $op->operation;
        }

        $this->renderJSON($options);
    }

    /**
     * Get the first 30 allergy matches for the given text. This is executed through an implicit AJAX request from the CJuiAutoComplete widget.
     * @param $term string The term supplied from the JUI Autocomplete widget.
     */
    public function actionCommonAllergies($term)
    {
        $allergies = Allergy::model()->findAllBySql('
SELECT a.*
FROM allergy a 
WHERE LOWER(a.name) LIKE LOWER(:term) ORDER BY a.name LIMIT  ' . _AUTOCOMPLETE_LIMIT, array('term' => "%$term%"));
        $values = array();
        foreach ($allergies as $allergy) {
            $values[] = $allergy->name;
        }

        $this->renderJSON($values);
    }
}