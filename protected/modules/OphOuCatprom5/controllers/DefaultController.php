<?php
namespace OEModule\OphOuCatprom5\controllers;

use mysql_xdevapi\Exception;
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
      if (!empty($_POST)){
        $raw_score = $_POST['CatProm5EventResult']['total_raw_score'];
        $rasch_measure = \CatProm5EventResult::model()->rowScoreToRaschMeasure($raw_score);
        $_POST['CatProm5EventResult']['total_rasch_measure'] = $rasch_measure;
      }
      parent::actionCreate();
    }

    public function actionUpdate($id)
    {
      if (!empty($_POST)){
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

}