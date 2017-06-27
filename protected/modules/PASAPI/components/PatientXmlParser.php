<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class PatientXmlParser
{
    private $_patients = null;

    private $_xml_reader = null;

    public function __construct($xml = null)
    {
        $this->_xml_reader = new XMLReader();

        if($xml){
            $this->load($xml);
        }
    }

    public function load($xml)
    {
        $this->_xml_reader->open($xml);
    }

    public function parse()
    {
        // move to the first <patient /> node
        while ($this->_xml_reader->read() && $this->_xml_reader->name !== 'patient');

        // now that we're at the right depth, hop to the next <patient/> until the end of the tree
        while ($this->_xml_reader === 'patient')
        {
            $node = new SimpleXMLElement($this->_xml_reader->readOuterXML());

            // now you can use $node without going insane about parsing

            $patient = new Patient();
            $contact = new Contact();

            $contact->first_name = 'Test';
            $contact->last_name = 'Test';
            $patient->dob = '1981-02-24';
            $patient->hos_num = '1234567';
            $patient->gender = 'M';

            $patient->contact = $contact;

            //note, we do not save the objects here
            //PasApiObserver does if it is required

            $this->_patients[] = $patient;

            // go to next <patient />
            $this->_xml_reader->next('patient');
        }

        return $this->_patients;
    }
}