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

<?php if ($this->patient->isDeceased()) { ?>
  <div id="deceased-notice" class="alert-box alert with-icon">
    This patient is deceased (<?php echo $this->patient->NHSDate('date_of_death'); ?>)
  </div>
<?php } ?>

<?php $this->renderPartial('//oescape/oescape_header', array(
    'subspecialty' => $subspecialty,
    'header_data' => $header_data
)) ?>
<?php $this->renderPartial('//oescape/oescape_content', array(
    'cssClass' => $cssClass,
    'content' => $content,
)) ?>