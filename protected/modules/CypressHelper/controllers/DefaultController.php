<?php

namespace OEModule\CypressHelper\controllers;

use CWebLogRoute;
use EventType;
use OE\concerns\InteractsWithApp;
use OE\factories\models\EventFactory;
use OE\factories\ModelFactory;
use Patient;

class DefaultController extends \CController
{
    use InteractsWithApp;

    public function beforeAction($action)
    {
        if ($this->getApp()->params['environment'] === 'live') {
            throw new \CHttpException(403, 'Request unavailable in current configuration');
        }

        return parent::beforeAction($action);
    }

    public function actionLogin()
    {
        // cribbed from the SiteController login process
        // be nice if we could alter this so that a user id / username is passed in
        // and we automatically log them in so we don't need passwords etc.
        $model = new \LoginForm();
        $model->attributes = [
            'username' => $_POST['username'] ?? 'admin',
            'password' => $_POST['password'] ?? 'admin',
            'site_id' => 1
        ];

        if (!$model->validate()) {
            $this->sendJsonResponse(400, $model->getErrors());
        }

        $model->login();
        $this->getApp()->session['confirm_site_and_firm'] = false;
        $this->getApp()->session['shown_version_reminder'] = true;

        $this->sendJsonResponse();
    }

    public function actionCreateEvent($moduleName)
    {
        /** @var \Event */
        $event = EventFactory::forModule($moduleName)->create();

        $this->sendJsonResponse(200, [
            'id' => $event->id,
            'urls' => [
                'view' => $event->getEventViewPath()
            ]
            ]);
    }

    public function actionCreatePatient()
    {
        $states = $_POST['states'] ?? [];
        $attributes = $_POST['attributes'] ?? [];
        /** @var Patient $patient */
        $patient = ModelFactory::factoryFor(Patient::class);
        foreach ($states as $state) {
            $patient = $patient->$state();
        }

        $patient = $patient->create($attributes);

        // TODO: create an appropriate DTO pattern for returning structured
        // data of a patient that can be used in testing.
        $this->sendJsonResponse(200, [
            'id' => $patient->id,
            'title' => $patient->title,
            'surname' => $patient->last_name,
            'first_name' => $patient->first_name,
            'gender' => $patient->getGenderString()
        ]);
    }

    public function actionGetEventCreationUrl($patientId, $moduleName)
    {
        $eventTypeId = EventType::model()->findByAttributes([
            'class_name' => $moduleName
        ])->id;

        $firmId = $this->getApp()->session['selected_firm_id'];
        $this->sendJsonResponse(200, [
            'url' => "/patientEvent/create?patient_id={$patientId}&event_type_id={$eventTypeId}&context_id={$firmId}&service_id={$firmId}"
        ]);
    }

    /**
     * This action relies on a ModelFactory having been defined for the required Model,
     * and it will either find an existing instance of the model with the given attributes
     * or create it.
     *
     * It will not return any relations defined on the model
     */
    public function actionLookupOrCreateModel()
    {
        $model_class = $_POST['model_class'] ?? null;
        $lookup_attributes = $_POST['attributes'] ?? [];

        if (!$model_class) {
            throw new \CHttpException(400, 'model class must be provided');
        }
        $model_instance = ModelFactory::factoryFor($model_class)
            ->useExisting($lookup_attributes)
            ->create();

        $this->sendJsonResponse(200, [
            'model' => array_merge(
                [
                    'id' => $model_instance->id
                ],
                $model_instance->getAttributes()
            )
            ]);
    }

    protected function sendJsonResponse($status = 200, array $data = [])
    {
        header('HTTP/1.1 ' . $status);
        header('Content-type: application/json');
        echo \CJSON::encode($data);

        $this->disableLogRoutes();
        $this->getApp()->end();
    }

    protected function disableLogRoutes()
    {
        foreach ($this->getApp()->log->routes as $route) {
            if ($route instanceof CWebLogRoute) {
                $route->enabled = false;
            }
        }
    }
}
