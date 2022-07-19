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
    }

    public function mockCurrentInstitution(?Institution $institution = null): Institution
    {
        if ($institution === null) {
            $institution = Institution::model()->findAll()[0];
        }

        Yii::app()->session['selected_institution_id'] = $institution->id;
        
        return $institution;
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
