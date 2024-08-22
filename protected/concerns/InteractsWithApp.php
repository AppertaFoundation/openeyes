<?php

namespace OE\concerns;

trait InteractsWithApp
{
    protected ?\CApplication $app = null;

    protected function getApp()
    {
        if (!$this->app) {
            $this->app = \Yii::app();
        }

        return $this->app;
    }

    public function setApp($app)
    {
        $this->app = $app;
    }
}
