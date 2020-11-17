<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class HttpRequest extends CHttpRequest
{
    public $noCsrfValidationRoutes = array();

    protected function normalizeRequest()
    {
        //attach event handlers for CSRFin the parent
        parent::normalizeRequest();
        //remove the event handler CSRF if this is a route we want skipped
        if ($this->enableCsrfValidation) {
            if (!isset($_SERVER['REQUEST_URI'])) {
                $_SERVER['REQUEST_URI'] = '';
            }

            $url = Yii::app()->getUrlManager()->parseUrl($this);
            foreach ($this->noCsrfValidationRoutes as $route) {
                if (strpos($url, $route) === 0) {
                    Yii::app()->detachEventHandler('onBeginRequest', array($this, 'validateCsrfToken'));
                }
            }
        }
    }
    // Sanitize string
    protected function purify($foo)
    {
        if (is_string($foo)) {
            $purifier = new \CHtmlPurifier();
            return $purifier->purify($foo);
        }
        else{
            return $foo;
        }
    }

    //sanitize user inputs in URL for Delete
    public function getDelete($name,$defaultValue=null)
    {
        return $this->purify(parent::getDelete($name,$defaultValue));
    }

    //sanitize user inputs in URL for one parameter
    public function getParam($name,$defaultValue=null)
    {
        return $this->purify(parent::getParam($name,$defaultValue));
    }

    //sanitize user inputs in URL for PATCH
    public function getPatch($name,$defaultValue=null)
    {
        return $this->purify(parent::getPatch($name,$defaultValue));
    }

    //sanitize user inputs in URL for POST
    public function getPost($name,$defaultValue=null)
    {
        return $this->purify(parent::getPost($name,$defaultValue));
    }

    //sanitize user inputs in URL for PUT
    public function getPut($name,$defaultValue=null)
    {
        return $this->purify(parent::getPut($name,$defaultValue));
    }

    //sanitize user inputs in URL for one parameter
    public function getQuery($name,$defaultValue=null)
    {
        return $this->purify(parent::getQuery($name,$defaultValue));
    }

    //sanitize user inputs in URL for all parameters
    public function getQueryString()
    {
        return $this->purify(parent::getQueryString());
    }
 
}
