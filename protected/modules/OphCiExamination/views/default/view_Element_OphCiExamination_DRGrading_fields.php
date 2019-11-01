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
?>
<table>
  <tbody>
  <?php if(!is_null($element->{$side . '_clinicalret'})) { ?>
  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_clinicalret_id') ?>:
    </td>
    <td>
        <?php echo $element->{$side . '_clinicalret'}->name ?>
    </td>
  </tr>
  <?php } ?>
  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_nscretinopathy_id') ?>:
    </td>
    <td>
        <?php echo $element->{$side . '_nscretinopathy'}->name ?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_nscretinopathy_photocoagulation') ?>:
    </td>
    <td>
        <?php echo $element->{$side . '_nscretinopathy_photocoagulation'} ? 'Yes' : 'No' ?>
    </td>
  </tr>
  <?php if ($element->{$side . '_clinicalmac'}) { ?>
    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_clinicalmac_id') ?>:
      </td>
      <td>
          <?php echo $element->{$side . '_clinicalmac'}->name ?>
      </td>
    </tr>
  <?php } ?>
  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_nscmaculopathy_id') ?>:
    </td>
    <td>
        <?php echo $element->{$side . '_nscmaculopathy'}->name ?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_nscmaculopathy_photocoagulation') ?>:
    </td>
    <td>
        <?php echo $element->{$side . '_nscmaculopathy_photocoagulation'} ? 'Yes' : 'No' ?>
    </td>
  </tr>
  </tbody>
</table>

