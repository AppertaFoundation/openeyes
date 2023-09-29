<?php

use Xthiago\PDFVersionConverter\Guesser\RegexGuesser;

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class OphCoDocument_API extends BaseAPI
{
    /**
     * @param string $type The size of the icon
     * @param Event $event The event to get the icon for
     * @return string The icon HTML element
     */
    public function getEventIcon($type, Event $event)
    {
        $element = Element_OphCoDocument_Document::model()->findByAttributes(array('event_id' => $event->id));
        $event_icon = 'i-CoDocument';

        if ($element && isset($element->sub_type->sub_type_event_icon_id)) {
            $event_icon = EventIcon::model()->findByPk($element->sub_type->sub_type_event_icon_id)->name;
        }

        return '<i class="oe-i-e ' . ($type === 'small' ? 'small ' : 'large ') . $event_icon.'"></i>';
    }

    public function getEventName($event)
    {
        $element = Element_OphCoDocument_Document::model()->findByAttributes(array('event_id' => $event->id));

        return $element->sub_type->name;
    }

    private function getPDFVersion($pdf_path)
    {
        $guesser = new RegexGuesser();
        return $guesser->guess($pdf_path);
    }

    private function convertPDF($pdf_path, $version = '1.4')
    {
        $tmpfname = tempnam("/tmp", "OE");
        exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel='.$version.' -dNOPAUSE -dBATCH -sOutputFile='.$tmpfname.' '.$pdf_path);
        return $tmpfname;
    }

    public function getDocumentAttachments($event)
    {
        $document_attachments = [];

        $element = Element_OphCoDocument_Document::model()->findByAttributes(['event_id' => $event->id]);

        foreach (['single_document_id', 'left_document_id', 'right_document_id'] as $index) {
            if ($element->$index) {
                $pf = ProtectedFile::model()->findByPk($element->$index);
                // Return no path for image types since it can be printed and attached
                if (strpos($pf->mimetype, 'image') === 0) {
                    $document_attachments['image'] = '';
                } elseif ($pf->mimetype === 'application/pdf') {
                    $pdf_path = $pf->getPath();
                    // Only PDF version 1.4 and below can be printed
                    if ((float)$this->getPDFVersion($pdf_path) > 1.4) {
                        $pdf_path = $this->convertPDF($pdf_path);
                    }
                    $document_attachments['application'][] = $pdf_path;
                } else {
                    $document_attachments[] = ['other' => null];
                }
            }
        }
        return $document_attachments;
    }
}
