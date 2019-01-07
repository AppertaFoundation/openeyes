<?php

class FlushableLogRouter extends CLogRouter
{
    public $autoFlush = 20;

    public function init()
    {
        parent::init();

        $logger = Yii::getLogger();
        $logger->autoFlush = $this->autoFlush;
        $logger->detachEventHandler('onFlush', array($this, 'collectLogs'));
        $logger->attachEventHandler('onFlush', array($this, 'clearLogs'));
    }

    public function clearLogs($event)
    {
        parent::processLogs($event);
        foreach (parent::getRoutes() as $route) {
            $route->logs = array();
        }
    }
}
