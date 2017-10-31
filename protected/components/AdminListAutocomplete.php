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
class AdminListAutocomplete extends Admin
{
    /**
     * @var string
     */
    protected $customDeleteURL;
    /**
     * @var string
     */
    protected $customSetDefaultURL;

    /**
     *
     */
    protected $customRemoveDefaultURL;
    /**
     * @var string
     */
    protected $customSaveURL;

    /**
     * @var array
     */
    protected $autocompleteField = array();

    /**
     * @var array
     */
    protected $filterFields = array();

    /**
     * @param $filters
     */
    public function setFilterFields($filters)
    {
        $this->filterFields = $filters;
    }

    /**
     * @return array
     */
    public function getFilterFields()
    {
        return $this->filterFields;
    }

    /**
     * @param $acdata
     */
    public function setAutocompleteField($acdata)
    {
        $this->autocompleteField = $acdata;
    }

    /**
     * @return array
     */
    public function getAutocompleteField()
    {
        return $this->autocompleteField;
    }

    /**
     * @param $deleteURL
     */
    public function setCustomDeleteURL($deleteURL)
    {
        $this->customDeleteURL = $deleteURL;
    }

    /**
     * @param $setDefaultURL
     */
    public function setCustomSetDefaultURL($setDefaultURL)
    {
        $this->customSetDefaultURL = $setDefaultURL;
    }

    /**
     * @param $removeDefaultURL
     */
    public function setCustomRemoveDefaultURL($removeDefaultURL)
    {
        $this->customRemoveDefaultURL = $removeDefaultURL;
    }

    /**
     * @return string
     */
    public function getCustomDeleteURL()
    {
        return $this->customDeleteURL;
    }

    /**
     * @return string
     */
    public function getCustomSetDefaultURL()
    {
        return $this->customSetDefaultURL;
    }

    /**
     * @return string
     */
    public function getCustomRemoveDefaultURL()
    {
        return $this->customRemoveDefaultURL;
    }

    /**
     * @param $saveURL
     */
    public function setCustomSaveURL($saveURL)
    {
        $this->customSaveURL = $saveURL;
    }

    /**
     * @return string
     */
    public function getCustomSaveURL()
    {
        return $this->customSaveURL;
    }

    /**
     * @param BaseActiveRecord    $model
     * @param BaseAdminController $admin
     */
    public function __construct($model, $admin)
    {
        parent::__construct($model, $admin);
        $this->setListTemplate('//admin/generic/listAutocomplete');
    }
}
