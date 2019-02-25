<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\OphCoCorrespondence\components;


interface ExternalIntegration
{

    /**
     * Render content to be included in the event view page for the given Event object.
     * Responsible for generating links to the external integration, automatic popups etc.
     *
     * @param \Event $event
     * @return string
     */
    public function renderEventView(\Event $event);

    /**
     * Will be passed the GET or POST array (depending on the nature of the request received
     * by the controller, and should return a status code and a body response.
     *
     * @param array $data
     * @return array(status_code, $response)
     */
    public function processExternalResponse($data = array());
}