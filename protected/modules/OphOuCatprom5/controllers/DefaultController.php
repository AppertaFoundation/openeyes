<?php
namespace OEModule\OphOuCatprom5\controllers;

use OEModule\OphOuCatprom5\components;
use OEModule\OphOuCatprom5\models;

class DefaultController extends \BaseEventTypeController
{

    public function actionView($id)
    {
        parent::actionView($id);
    }

    public function actionCreate()
    {
        if (!empty($_POST)) {
            $raw_score = $_POST['CatProm5EventResult']['total_raw_score'];
            $rasch_measure = \CatProm5EventResult::model()->rowScoreToRaschMeasure($raw_score);
            $_POST['CatProm5EventResult']['total_rasch_measure'] = $rasch_measure;
        }
        parent::actionCreate();
    }

    public function actionUpdate($id)
    {
        if (!empty($_POST)) {
            $raw_score = $_POST['CatProm5EventResult']['total_raw_score'];
            $rasch_measure = \CatProm5EventResult::model()->rowScoreToRaschMeasure($raw_score);
            $_POST['CatProm5EventResult']['total_rasch_measure'] = $rasch_measure;
        }
        parent::actionUpdate($id);
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function setComplexAttributes_CatProm5EventResult($element, $data, $index)
    {
        $model_name = \CHtml::modelName($element);
        $answer_records = array();
        if (isset($data[$model_name]['catProm5AnswerResults'])) {
            foreach ($data[$model_name]['catProm5AnswerResults'] as $idx => $answer_item) {
                $ans_item = null;
                if (@$answer_item['id']) {
                    $ans_item = \CatProm5AnswerResult::model()->findByPk($answer_item['id']);
                }
                if ($ans_item == null) {
                    $ans_item = new \CatProm5AnswerResult();
                }

                $ans_item->attributes = $answer_item;
                $answer_records[] = $ans_item;
            }
        }
        $element->catProm5AnswerResults = $answer_records;
    }

}