<?php

class AdminPhraseBySpecialtyController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		// nobody can change sections
		// anyone can add, edit, delete phrases within a section
		// phrases by firm is the one that also wants to be on the user side
		return array(
			array('allow',	// allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','sectionindex', 'view', 'phraseindex', 'specialtyIndex'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny', // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$sectionId = $_GET['section_id'];
		$specialtyId = $_GET['specialty_id'];
		$sectionName = Section::model()->findByPk($sectionId)->name;
		$specialtyName = Specialty::model()->findByPk($specialtyId)->name;
		$model=new PhraseBySpecialty;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PhraseBySpecialty'])) {
			$model->attributes=$_POST['PhraseBySpecialty'];
			if ($model->attributes['phrase_name_id']) {
				// We are overriding an existing phrase name - so as long as it hasn't been overridden already we should just save it
				// Standard validation will handle checking that
			} else {
				// We are creating a new phrase name - so we need to check if it already exists, if so create a reference to it, and if not create it and then the reference
				// manually check whether a phrase of this name already exists
				if ($phraseName = PhraseName::model()->findByAttributes(array('name' => $_POST['PhraseName']))) {
					$model->phrase_name_id = $phraseName->id;
				} else {
					$newPhraseName = new PhraseName;
					$newPhraseName->name = $_POST['PhraseName'];
					$newPhraseName->save();
					$model->phrase_name_id = PhraseName::model()->findByAttributes(array('name' => $_POST['PhraseName']))->id;
				}
			}
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));

			if($model->save()) $this->redirect(array('view','id'=>$model->id));
		} 

		$this->render('create',array(
			'model'=>$model,
			'sectionId'=>$sectionId,
			'sectionName'=>$sectionName,
			'specialtyId'=>$specialtyId,
			'specialtyName'=>$specialtyName,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PhraseBySpecialty']))
		{
			$model->attributes=$_POST['PhraseBySpecialty'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('phraseIndex', 'section_id'=>$_REQUEST['section_id'], 'specialty_id'=>$_REQUEST['specialty_id']));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all section models.
	 */
	public function actionIndex()
	{
		$criteria = new CDbCriteria;
		$relevantSectionTypes = PhraseBySpecialty::model()->relevantSectionTypes();
		foreach ($relevantSectionTypes as $relevantSection) {
			$sectionType = SectionType::model()->findByAttributes(array('name' => $relevantSection));
			$criteria->compare('section_type_id',$sectionType->id,false,'OR');
		}

		$dataProvider=new CActiveDataProvider('Section', array('criteria'=>$criteria));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * List all models for the given section + specialty
	 *
	 */
	public function actionPhraseIndex()
	{
		$sectionId = $_GET['section_id'];
		$specialtyId = $_GET['specialty_id'];
		$sectionName = Section::model()->findByPk($sectionId)->name;
		$specialtyName = Specialty::model()->findByPk($specialtyId)->name;

		$criteria=new CDbCriteria;
		$criteria->compare('section_id',$sectionId,false);
		$criteria->compare('specialty_id',$specialtyId,false);
	
	
		$dataProvider=new CActiveDataProvider('PhraseBySpecialty', array(
			'criteria'=>$criteria,
		));

		$this->render('phraseindex',array(
			'dataProvider'=>$dataProvider,
			'sectionId'=>$sectionId,
			'sectionName'=>$sectionName,
			'specialtyId'=>$specialtyId,
			'specialtyName'=>$specialtyName,
		));
	}

	/**
	 * List all specialties for the given section
	 *
	 */
	public function actionSpecialtyIndex()
	{
		$sectionId = $_GET['section_id'];
		$sectionName = Section::model()->findByPk($sectionId)->name;

		$dataProvider=new CActiveDataProvider('Specialty', array());

		$this->render('specialtyindex',array(
			'dataProvider'=>$dataProvider,
			'sectionId'=>$sectionId,
			'sectionName'=>$sectionName,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new PhraseBySpecialty('search');
		$model->unsetAttributes(); // clear any default values
		if(isset($_GET['PhraseBySpecialty']))
			$model->attributes=$_GET['PhraseBySpecialty'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=PhraseBySpecialty::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='phrase-by-specialty-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
