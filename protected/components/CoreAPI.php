<?php
/**
 * OpenEyes
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Class CoreAPI
 *
 * Implemented to operate on the Patient record to retrieve data from core properties in a
 * similar fashion to the module level APIs. With moving various attributes into the
 * examination module, this may not be required in the longer term, but it enables us to
 * move getting the current context outside of the Patient model.
 */
class CoreAPI
{
    /**
     * @var CApplication
     */
    protected $yii;

    /**
     * @var DataContext
     */
    protected $current_context;

    /**
     * BaseAPI constructor.
     * @param DataContext|null $context
     */
    public function __construct(CApplication $yii = null, DataContext $context = null)
    {
        if ($yii === null) {
            $yii = Yii::app();
        }
        if ($context === null) {
            $context = new DataContext($yii);
        }
        $this->yii = $yii;
        $this->current_context = $context;
    }

    /**
     * @param Patient $patient
     * @return Episode|null
     * @throws SystemException
     */
    public function getEpisodeForCurrentContext(Patient $patient)
    {
        $subspecialty_id = null;
        if (!$this->current_context->support_services) {
            $subspecialties = $this->current_context->getSubspecialties();
            if (count($subspecialties) !== 1) {
                throw new \SystemException('Cannot get Episode for invalid current context');
            }
            $subspecialty_id = $subspecialties[0]->id;
        }

        return Episode::model()->getCurrentEpisodeBySubspecialtyId($patient->id, $subspecialty_id, true);
    }

    /**
     * Principal Diagnosis For current context
     *
     * @param Patient $patient
     * @return string
     */
    public function getEpd(Patient $patient)
    {
        $episode = $this->getEpisodeForCurrentContext($patient);

        if ($episode && $disorder = $episode->diagnosis) {
            if ($episode->eye) {
                return $episode->eye->getAdjective().' '.strtolower($disorder->term);
            } else {
                return strtolower($disorder->term);
            }
        }
    }

    /**
     * Current Context Left Eye Diagnosis
     *
     * @param Patient $patient
     * @return string
     */
    public function getEdl(Patient $patient)
    {
        $episode = $this->getEpisodeForCurrentContext($patient);

        if ($episode && $disorder = $episode->diagnosis) {
            if ($episode->eye->id == Eye::BOTH || $episode->eye->id == Eye::LEFT) {
                return ucfirst(strtolower($disorder->term));
            }

            return 'No diagnosis';
        }
    }

    /**
     * Current Context right eye Diagnosis
     *
     * @param Patient $patient
     * @return string
     */
    public function getEdr(Patient $patient)
    {
        $episode = $this->getEpisodeForCurrentContext($patient);

        if ($episode && $disorder = $episode->diagnosis) {
            if ($episode->eye->id == Eye::BOTH || $episode->eye->id == Eye::RIGHT) {
                return ucfirst(strtolower($disorder->term));
            }

            return 'No diagnosis';
        }
    }

    /**
     * Get the principal side for the current context
     *
     * @param Patient $patient
     * @return string
     */
    public function getEps(Patient $patient)
    {
        $episode = $this->getEpisodeForCurrentContext($patient);

        if ($episode && $eye = $episode->eye) {
            return strtolower($eye->adjective);
        }
    }

    /**
     * Get the name of the consultant for the current context
     *
     * @param Patient $patient
     * @return mixed
     */
    public function getEpc(Patient $patient)
    {
        if ($episode = $this->getEpisodeForCurrentContext($patient)) {
            if ($user = $episode->firm->consultant) {
                return $user->fullName;
            }
        }
    }

    /**
     * Get the name of the service for the current context
     * 
     * @param Patient $patient
     * @return string|null
     */
    public function getEpv(Patient $patient)
    {
        if ($episode = $this->getEpisodeForCurrentContext($patient)) {
            if ($episode->firm) {
                return $episode->firm->getServiceText();
            }
        }
    }

    /*
     * Generate episode link for patient
     * @param Patient $patient
     * @return string
     */
    public function generateEpisodeLink(Patient $patient, $params = [])
    {
        $episode = $this->getEpisodeForCurrentContext($patient);
        if( $episode !== null){
            return $this->yii->createURL("/patient/episode/", array("id" => $episode->id) + $params );
        } else {
            return $this->yii->createURL("/patient/episodes/", array("id" => $patient->id) + $params);
        }
    }

    public function generateLatestEventLink(Patient $patient) {
        $latest_event = $patient->getLatestEvent();
        if ($latest_event) {
            return $this->yii->createUrl($latest_event->eventType->class_name.'/default/view/'.$latest_event->id);
        }
        else
            return null;
    }

}