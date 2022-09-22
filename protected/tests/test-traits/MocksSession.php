<?php

use PHPUnit\Framework\MockObject\MockObject;

/**
 * This trait is intended to provide a location to define the different session state dependencies in
 * a more readable manner within test setup.
 */
trait MocksSession
{
    protected array $session_values = [];

    public function setupMocksSession()
    {
        $this->beginMocksSession();
    }

    /**
     * Removes session state entirely, which is a useful shortcut in some test contexts
     */
    public function stubSession()
    {
        $session = \Yii::app()->getComponent('session', false);

        if (!$session instanceof MockObject) {
            $session = $this->getMockBuilder(\CHttpSession::class)
            ->disableOriginalConstructor()
            ->getMock();

            $session->method('get')
                ->willReturnCallback(function ($attr) {
                    return $this->session_values[$attr] ?? null;
                });
            $session->method('offsetGet')
                ->willReturnCallback(function ($attr) {
                    return $this->session_values[$attr] ?? null;
                });

            $session->method('offsetExists')
                ->willReturnCallback(function ($attr) {
                    return isset($this->session_values[$attr]);
                });

            \Yii::app()->setComponent('session', $session);
        }

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

        $this->setSessionValues([
            'selected_firm_id' => $firm->id,
            'selected_site_id' => $site->id,
            'selected_institution_id' => $institution->id
        ]);
    }

    public function mockCurrentUser($user)
    {
        $web_user = $this->getMockBuilder(OEWebUser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getIsGuest', 'init'])
            ->getMock();

        $web_user->method('getId')
            ->willReturn($user->id);

        $web_user->method('getIsGuest')
            ->willReturn(false);

        // stub state values from user object
        foreach ($user->getAttributes() as $attr => $value) {
            $web_user->setState($attr, $value);
        }

        \Yii::app()->setComponent('user', $web_user);

        $session = $this->stubSession();
        $this->setSessionValues(['user' => $web_user]);

        return $this;
    }

    protected function setSessionValues(array $session_values = [], $merge = true)
    {
        $this->session_values = $merge ? array_merge($this->session_values, $session_values) : $session_values;

        return $this;
    }

    protected function beginMocksSession()
    {
        // ensure session is freshly stubbed
        $this->eraseCurrentSession();
        $this->stubSession();

        $this->teardownCallbacks(function () {
            $this->eraseCurrentSession();
        });
    }

    protected function eraseCurrentSession()
    {
        $_SESSION = [];
        Yii::app()->setComponent('session', null);
        $this->setSessionValues([], false);
    }
}
