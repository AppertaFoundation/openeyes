<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(

    'params' => array(
        'OphCoDocument' => array(
            'allowed_file_types' => array(
                'pdf'   => 'application/pdf',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'png'   => 'image/png',
                'gif'   => 'image/gif',
                'mp4'   => 'video/mp4',
                'mpeg4' => 'video/mp4',
                'ogg'   => 'video/ogg',
                'webm'  => 'video/webm',
                'mov'   => 'video/quicktime'
            )
        ),
        'OphCoDocument_Sub_Types' => array(
            'allowed_file_types' => array(
                'pdf'   => 'application/pdf',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'png'   => 'image/png',
            )
        ),
        'admin_structure' => array(
            'Document' => array(
                'Document sub type settings' => array(
                    'module' => 'OphCoDocument',
                    'uri' => '/OphCoDocument/oeadmin/DocumentSubTypesSettings',
                    'restricted' => array('admin'),
                ),
            ),
        )
    )
);
