<?php

/**
 * This trait is intended to provide a location to define the different session state dependencies in
 * a more readable manner within test setup.
 */
trait MocksSession
{
    public function setupMocksSession()
    {
        $this->beginMocksSession();
    }

    /**
     * Removes session state entirely, which is a useful shortcut in some test contexts
     */
    public function stubSession()
    {
        $session = $this->getMockBuilder(\CHttpSession::class)
            ->disableOriginalConstructor()
            ->getMock();

        \Yii::app()->setComponent('session', $session);

        return $session;
    }

    public function mockCurrentInstitution(?Institution $institution = null): Institution
    {
        if ($institution === null) {
            $institution = Institution::model()->findAll()[0];
        }

        $this->mockCurrentContext(null, null, $institution);
        return $institution;
    }

    public function mockCurrentContext($firm = null, $site = null, $institution = null)
    {
        $firm ??= Firm::model()->findAll()[0];
        $site ??= Site::model()->findAll()[0];
        $institution ??= Institution::model()->findAll()[0];

        $session = $this->stubSession();

        $session_values = [
            'selected_firm_id' => $firm->id,
            'selected_site_id' => $site->id,
            'selected_institution_id' => $institution->id
        ];

        $value_map = array_map(function ($key) use ($session_values) {
            return [$key, null, $session_values[$key]];
        }, array_keys($session_values));


        $session->method('get')
            ->will($this->returnValueMap($value_map));
        $session->method('offsetGet')
            ->willReturnCallback(function ($offset) use ($session_values) {
                return $session_values[$offset] ?? null;
            });
    }

    public function mockCurrentUser($user)
    {
        $web_user = $this->getMockBuilder(OEWebUser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $web_user->method('getId')
            ->willReturn($user->id);

        \Yii::app()->setComponent('user', $web_user);

        return $this;
    }

    protected function beginMocksSession()
    {
        // ensure session is undefined
        $this->eraseCurrentSession();

        $this->teardownCallbacks(function () {
            $this->eraseCurrentSession();
        });
    }

    protected function eraseCurrentSession()
    {
        $_SESSION = [];
        Yii::app()->setComponent('session', null);
    }
}
