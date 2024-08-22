<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\contracts\ProvidesApplicationContext;

/**
 * This provides an HTTP Session independent means of providing application context to areas of OpenEYes
 * that requires this data to function.
 */
final class ApplicationContext implements ProvidesApplicationContext
{
    private Firm $firm;
    private Institution $institution;
    private Site $site;

    public function __construct(Firm $firm, Institution $institution, Site $site)
    {
        $this->firm = $firm;
        $this->institution = $institution;
        $this->site = $site;
    }

    /**
     * Convenience constructor to allow initialisation from an HTTP Session
     *
     * @param OESession|null $session
     * @return ApplicationContext
     */
    public static function fromSession(?OESession $session = null): ApplicationContext
    {
        $session ??= Yii::app()->session;

        return new static(
            $session->getSelectedFirm(),
            $session->getSelectedInstitution(),
            $session->getSelectedSite()
        );
    }

    /**
     * Convenience constructor more simply initialise context from model primary keys
     * that define the standard OpenEyes context
     *
     * @param array - containing firm_id, institution_id and site_id
     * @return ApplicationContext
     */
    public static function fromPrimaryKeys(...$args): ApplicationContext
    {
        return new static(
            Firm::model()->findByPk($args['firm_id']),
            Institution::model()->findByPk($args['institution_id']),
            Site::model()->findByPk($args['site_id'])
        );
    }

    public function getSelectedFirm(): Firm
    {
        return $this->firm;
    }

    public function getSelectedInstitution(): Institution
    {
        return $this->institution;
    }
    public function getSelectedSite(): Site
    {
        return $this->site;
    }
}