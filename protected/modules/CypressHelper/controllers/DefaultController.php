<?php

namespace OEModule\CypressHelper\controllers;

use CWebLogRoute;
use Event;
use EventType;
use Firm;
use Institution;
use OE\concerns\InteractsWithApp;
use OE\factories\models\EventFactory;
use OE\factories\ModelFactory;
use Patient;
use User;
use CActiveRecord;
use SettingInstallation;
use SettingMetadata;

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
            $this->sendJsonResponse($model->getErrors(), 400);
        }

        $model->login();
        $this->getApp()->session['confirm_site_and_firm'] = false;
        $this->getApp()->session['shown_version_reminder'] = true;

        $this->sendJsonResponse([
            'firm_id' => $this->getApp()->session['selected_firm_id'],
            'subspecialty_id' => Firm::model()->findByPK($this->getApp()->session['selected_firm_id'])->getSubspecialtyID(),
            'institution_id' => $this->getApp()->session['selected_institution_id']
        ]);
    }

    public function actionCreateUser()
    {
        $authitems = $_POST['authitems'] ?? [];
        $institution_id = $_POST['institution_id'] ?? 1;
        $attributes = $_POST['attributes'] ?? [];
        $password = $_POST['password'] ?? 'password';

        $user = User::factory()
            ->withAuthItems($authitems)
            ->withLocalAuthForInstitution(Institution::model()->findByPk($institution_id), $password)
            ->create($attributes);

        $this->sendJsonResponse([
            'user_id' => $user->id,
            'username' => $user->authentications[0]->username,
            'password' => $password
        ]);
    }

    public function actionCreateEvent($moduleName)
    {
        $event_factory = EventFactory::forModule($moduleName);

        $instances = $this->applyStatesTo($event_factory, $_POST['states'] ?? [])
            ->count(isset($_POST['count']) ? (int) $_POST['count'] : 1)
            ->create($_POST['attributes'] ?? []);

        $this->sendJsonResponse(
            count($instances) > 1
            ? [
                array_map(
                    function ($event) {
                        return $this->eventJson($event);
                    },
                    $instances
                )
            ]
            : $this->eventJson($instances[0])
        );
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
        $this->sendJsonResponse($this->patientJson($patient));
    }

    public function actionGetEventCreationUrl($patientId, $moduleName)
    {
        $eventTypeId = EventType::model()->findByAttributes([
            'class_name' => $moduleName
        ])->id;

        $firmId = $this->getApp()->session['selected_firm_id'];
        $this->sendJsonResponse([
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

        $this->sendJsonResponse([
            'model' => array_merge(
                [
                    'id' => $model_instance->id
                ],
                $model_instance->getAttributes()
            )
            ]);
    }

    public function actionCreateModels()
    {
        $model_class = $_POST['model_class'] ?? null;
        if (!$model_class) {
            throw new \CHttpException(400, 'model class must be provided');
        }

        $model_factory = ModelFactory::factoryFor($model_class);

        $this->applyStatesTo($model_factory, $_POST['states'] ?? []);

        $model_factory->count($_POST['count'] ? (int) $_POST['count'] : 1);

        $instances = $model_factory->create($_POST['attributes'] ?? []);

        $this->sendJsonResponse(['models' => $this->modelsToArrays($instances)]);
    }

    public function actionSetSystemSettingValue()
    {
        $system_setting_key = $_POST['system_setting_key'] ?? null;
        $system_setting_value = $_POST['system_setting_value'] ?? null;

        if (!$system_setting_key || !$system_setting_value) {
            throw new \CHttpException(400, 'system setting key and value must both be provided');
        }

        $setting = SettingInstallation::model()->findByAttributes(['key' => $system_setting_key]);
        if (!isset($setting)) {
            $setting = new SettingInstallation();
            $setting->key = $system_setting_key;
        }
        $setting->value = $system_setting_value;
        $setting->save();

        // ensure the change takes immediate effect for further requests
        \Yii::app()->settingCache->flush();

        echo '1';
    }

    public function actionResetSystemSettingValue()
    {
        $system_setting_key = $_POST['system_setting_key'] ?? null;

        if (!$system_setting_key) {
            throw new \CHttpException(400, 'system setting key must be provided');
        }

        // Delete the setting installation key, the setting then falls back to the value specified as default in SettingMetadata
        $setting = SettingInstallation::model()->findByAttributes(['key' => $system_setting_key]);
        if (isset($setting)) {
            $setting->delete();
        }

        // ensure the change takes immediate effect for further requests
        \Yii::app()->settingCache->flush();

        echo '1';
    }

    public function actionRunSeeder()
    {
        $seeder_class_name = $_POST['seeder_class_name'];
        $seeder_module_name = $_POST['seeder_module_name'];
        $additional_data = $_POST['additional_data'];

        $dataContext = new \DataContext(
            \Yii::app(),
            [
                'subspecialties' => \Subspecialty::model()->findByPk(1),
                'additional_data' => $additional_data
            ]
        );

        $fully_qualified_seeder_class_name = '\\OEModule\\' . $seeder_module_name . '\\seeders\\' . $seeder_class_name;

        $seeder = new $fully_qualified_seeder_class_name($dataContext);

        $seeder_result = $seeder();

        $this->sendJsonResponse($seeder_result);
    }

    protected function applyStatesTo(ModelFactory $factory, $states = []): ModelFactory
    {
        if (!is_array($states)) {
            $states = [$states];
        }

        foreach ($states as $state) {
            if (is_array($state)) {
                $factory = $factory->{$state[0]}(...array_slice($state, 1));
            } else {
                $factory = $factory->$state();
            }
        }

        return $factory;
    }
    protected function sendJsonResponse(array $data = [], int $status = 200)
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

    protected function modelsToArrays(array $instances, $exclude = [])
    {
        return array_map(function ($instance) use ($exclude) {
            return $this->modelToArray($instance, $exclude);
        }, $instances);
    }

    protected function modelToArray($instance, $exclude = [])
    {
        return array_merge(
            array_filter(
                $instance->getAttributes(),
                function ($key, $value) use ($exclude) {
                    return !in_array($key, $exclude);
                },
                ARRAY_FILTER_USE_BOTH
            ),
            $this->getRelationsForModel($instance, $exclude)
        );
    }

    protected function getRelationsForModel(CActiveRecord $instance, $exclude = [])
    {
        $relations = [];
        foreach ($instance->relations() as $relation => $definition) {
            if (in_array($relation, array_merge(['user', 'usermodified'], $exclude))) {
                continue;
            }
            if ($relation === 'event') {
                $relations[$relation] = $this->eventJson($instance->$relation, false);
                continue;
            }
            if ($definition[0] === CActiveRecord::BELONGS_TO) {
                $relations[$relation] = $instance->$relation ? $instance->$relation->getAttributes() : null;
            }
            if ($definition[0] === CActiveRecord::HAS_MANY) {
                $relations[$relation] = $this->modelsToArrays($instance->$relation, $exclude);
            }
        }
        return $relations;
    }

    protected function patientJson(Patient $patient): array
    {
        return [
            'id' => $patient->id,
            'title' => $patient->title,
            'surname' => $patient->last_name,
            'first_name' => $patient->first_name,
            'gender' => $patient->getGenderString()
        ];
    }

    protected function eventJson(Event $event, bool $with_elements = true): array
    {
        $data = [
            'id' => $event->id,
            'urls' => [
                'view' => $event->getEventViewPath()
            ],
            'patient' => $this->patientJson($event->episode->patient)
        ];

        if ($with_elements) {
            $data['elements'] = array_map(
                function ($element) {
                    return [
                        'element_type' => $element->getElementTypeName(),
                        'attributes' => array_merge($element->getAttributes(), $this->getRelationsForModel($element, ['event', 'element']))
                    ];
                },
                $event->getElements()
            );
        }

        return $data;
    }
}
