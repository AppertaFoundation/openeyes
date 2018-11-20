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

$this->layout = '//layouts/error';
?>
<h2>Merged patient record</h2>
<div class="alert-box error">
  <strong>The patient record you requested cannot be accessed due to an inconsistency in the PAS system.</strong>
</div>
<p class="panel">Please contact OpenEyes support for assistance:</p>
<?php echo $this->renderPartial('//error/support') ?>
<p class="panel"><?=\CHtml::link('Click here to go back to the search page', Yii::app()->baseUrl . '/') ?></p>
<p class="date"><?php echo date('r'); ?></p>
