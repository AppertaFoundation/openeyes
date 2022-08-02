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
        $this->eraseCurrentSession();

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

        $session->method('get')
        ->will($this->returnValueMap([
            ['selected_firm_id', null, $firm->id],
            ['selected_site_id', null, $site->id],
            ['selected_institution_id',null, $institution->id]
        ]));
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
