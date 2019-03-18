<?php

class OETrial_API extends BaseAPI
{
    /**
     * @param $type
     * @param $partial
     *
     * @return bool|string
     */
    public function findViewFile($type, $partial)
    {
        $viewFile = Yii::getPathOfAlias('OETrial.views.' . $type . '.' . $partial) . '.php';

        if (file_exists($viewFile)) {
            return $viewFile;
        }

        return false;
    }
}
