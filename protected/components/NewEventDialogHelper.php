<?php
/**
 * OpenEyes.
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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class NewEventDialogHelper
 *
 * Static wrapper class to encapsulate useful functions to structure data for the New Event Dialog
 */
class NewEventDialogHelper
{
    protected static $support_services_subspecialty = array(
        'id' => 'SS',
        'name' => 'Support Services',
        'shortName' => 'SS',
        'supportServices' => 1
    );

    /**
     * @param Episode $episode
     * @return array
     */
    public static function structureEpisode(Episode $episode)
    {
        $services_available = array();

        if ($subspecialty = $episode->getSubspecialty()) {
            $structured_subspecialty = static::structureSubspecialty($subspecialty);
            $firm = static::structureFirm(\Firm::model()->findByPk($episode->firm_id));
            $criteria = new CDbCriteria();
            $criteria->addCondition('can_own_an_episode=1 AND id<>:firm_id AND service_subspecialty_assignment_id=:ssaid');
            $criteria->params = [
                ':firm_id' => $episode->firm_id,
                ':ssaid' => $episode->firm->service_subspecialty_assignment_id
            ];
            foreach (Firm::model()->findAllAtLevels(ReferenceData::LEVEL_ALL, $criteria) as $service) {
                array_push($services_available, static::structureFirm($service));
            }
        } else {
            $structured_subspecialty = static::$support_services_subspecialty;
            $firm = '';
        }
        return array(
            'id' => $episode->id,
            'service' => $episode->firm ? $episode->firm->name : '',
            'subspecialty' => $structured_subspecialty,
            'firm' => $firm,
            'services_available' => $services_available
        );
    }

    /**
     * @param Episode[] $episodes
     * @return array
     */
    public static function structureEpisodes(array $episodes)
    {
        $res = array();
        foreach ($episodes as $ep) {
            $res[] = static::structureEpisode($ep);
        }
        return $res;
    }

    /**
     * @param Subspecialty $subspecialty
     * @return array
     */
    public static function structureSubspecialty(Subspecialty $subspecialty)
    {
        return array(
            'id' => $subspecialty->id,
            'name' => $subspecialty->name,
            'shortName' => $subspecialty->ref_spec
        );
    }

    /**
     * @param Firm $firm
     * @return array
     */
    public static function structureFirm(Firm $firm)
    {
        return array(
            'id' => $firm->id,
            'name' => $firm->name
        );
    }

    /**
     * @return array|bool
     */
    public static function structureAllSubspecialties()
    {
        $subspecialties = array();
        foreach (Subspecialty::model()->findAll() as $subspecialty) {
            $related_firms = Firm::model()
                ->findAllAtLevels(ReferenceData::LEVEL_ALL, array(
                    'condition' => 'serviceSubspecialtyAssignment.subspecialty_id = :ssid AND active = 1',
                    'with' => 'serviceSubspecialtyAssignment',
                    'params' => array(':ssid' => $subspecialty->id),
                    'order' => 't.name asc'
                ));
            if (count($related_firms)) {
                $structure = static::structureSubspecialty($subspecialty);
                $structure['services'] = array();
                $structure['contexts'] = array();

                foreach ($related_firms as $f) {
                    $structured_firm = static::structureFirm($f);
                    if ($f->can_own_an_episode) {
                        $structure['services'][] = $structured_firm;
                    }
                    if ($f->runtime_selectable) {
                        $structure['contexts'][] = $structured_firm;
                    }
                }
                $subspecialties[] = $structure;
            }
        }

        return $subspecialties;
    }
}