<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
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
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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


}