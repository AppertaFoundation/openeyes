<?php

/**
 * Class HieIntegrationTest
 */
class HieIntegrationTest extends CTestCase
{
    protected $testJson = '{
        "USR_NAME":"Admin Admin",
        "USR_POSITION":"Level 1 - Default View",
        "USR_DSPLYNM":"Admin, Admin",
        "USR_ORG":"2.16.841.X.X.X",
        "USR_FAC":"2.16.841.X.X.X.1",
        "PAT_CMRN":"96203444722",
        "PAT_FNAME":"DOROTHY",
        "PAT_LNAME":"MORRISON",
        "PAT_DOB":"19710210",
        "PERMISSION":"Yes",
        "EXTERNAL":"both",
        "ORG_USER":"test",
        "ORG_PASS":"testpass"
    }';

    /**
     * @var Patient
     */
    protected $patient;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var hieIntegration component
     */
    protected $instance;

    /**
     * @var Array
     */
    protected $test_data = [];

    public function setUp()
    {
        $this->test_data = json_decode($this->testJson, true);

        $user_data = [
            'first_name' => 'Admin',
            'last_name' => 'Admin',
        ];

        $patient_data = [
            'id' => 11,
            'dob' => $this->test_data['PAT_DOB'],
            'first_name' => $this->test_data['PAT_FNAME'],
            'last_name' => $this->test_data['PAT_LNAME'],
        ];

        $this->user = new User;
        $this->user->setAttributes($user_data);

        $this->patient = new Patient;
        $this->patient->setAttributes($patient_data);
        $this->patient->contact = new Contact();
        $this->patient->contact->first_name = $patient_data['first_name'];
        $this->patient->contact->last_name = $patient_data['last_name'];

        $app = \Yii::app();

        $app->session['user'] = $this->user;

        // Because of the exceptions
        $app->params['hie_usr_org'] = ' ';
        $app->params['hie_usr_fac'] = ' ';
        $app->params['hie_external'] = ' ';
        $app->params['hie_org_user'] = ' ';
        $app->params['hie_org_pass'] = ' ';
        $app->params['hie_remote_url'] = ' ';
        $app->params['hie_aes_encryption_password'] = ' ';

        $this->instance = Yii::app()->hieIntegration;

        $this->instance->hie_usr_org = $this->test_data['USR_ORG'];
        $this->instance->hie_usr_fac = $this->test_data['USR_FAC'];
        $this->instance->hie_external = $this->test_data['EXTERNAL'];
        $this->instance->hie_org_user = $this->test_data['ORG_USER'];
        $this->instance->hie_org_pass = $this->test_data['ORG_PASS'];
    }

    protected function callMethod($object, string $method, array $parameters = [])
    {
        try {
            $className = get_class($object);
            $reflection = new \ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new \Exception($e->getMessage());
        }

        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Test Collect and Generate data structure to Url encrypt
     *
     * @throws ReflectionException
     */
    public function testGenerateDataToEncrypt()
    {
        $nhs_number = $this->test_data['PAT_CMRN'];
        $this->callMethod($this->instance, "generateDataToEncryptedUrl", [$this->patient,$this->user,$nhs_number]);
        $data = $this->instance->getData();
        $data['USR_POSITION'] = 'Level 1 - Default View';

        unset($data['EXPIRATION']);
        unset($this->test_data['AES_ENCRYPTION_PASSWORD']);
        unset($this->test_data['REMOTE_URL']);

        // Test normal normal data -- exclude EXPIRATION
        $this->assertEquals($this->test_data, $data);

        // Timezone test
        if (date_default_timezone_get() !== 'UTC') {
            throw new \ReflectionException('Timezone error.');
        }
    }

    /*
     * testGetUrl()
     *
     * The AES 256 encrypt doesn't generate the same code in all cases
     * Because of the initialization vector (IV).
     *
     * Test case skipped.
    */
}
