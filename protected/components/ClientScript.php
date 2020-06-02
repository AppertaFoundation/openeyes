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
class ClientScript extends CClientScript
{
    /**
     * Extending unifyScripts in order to hook the cache buster in at the right
     * point in the render method.
     */
    protected function unifyScripts()
    {
        parent::unifyScripts();

        $cacheBuster = Yii::app()->cacheBuster;

        // JS
        foreach ($this->scriptFiles as $pos => $scriptFiles) {
            foreach ($scriptFiles as $key => $scriptFile) {
                unset($this->scriptFiles[$pos][$key]);
                // Add cache buster string to url.
                $scriptUrl = $cacheBuster->createUrl($scriptFile);
                $this->scriptFiles[$pos][$scriptUrl] = $scriptFile;
            }
        }

        // CSS
        foreach ($this->cssFiles as $cssFile => $media) {
            unset($this->cssFiles[$cssFile]);
            // Add cache buster string to url.
            $cssFile = $cacheBuster->createUrl($cssFile);
            $this->cssFiles[$cssFile] = $media;
        }

        // ICONS
        foreach ($this->linkTags as $index => $attributes) {
            if ($attributes['rel'] === 'icon') {
                unset($this->linkTags[$index]);
                // Add cache buster string to url.
                $attributes['href'] = $cacheBuster->createUrl($attributes['href']);
                $this->linkTags[$index] = $attributes;
            }
        }
    }
}
