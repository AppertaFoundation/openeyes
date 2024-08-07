<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\widgets\VisualAcuity;

?>

<?php
/**
 * @var OphCiExamination_VisualAcuity_Reading $reading
 * @var VisualAcuity $this
 */

$test_prefix = $this->isForNear() ? 'near-visual-acuity-reading' : 'visual-acuity-reading';
?>

<tr>
    <?php if ($reading->isBeo()) { ?>
        <td><i class="oe-i small beo"></i></td>
    <?php } ?>
    <td data-test="<?= $test_prefix ?>-method"><?= $reading->method ?></td>
    <td data-test="<?= $test_prefix ?>-unit"><?= $reading->unit ?></td>
    <td data-test="<?= $test_prefix ?>-value"><?= $reading->display_value ?></td>
    <td><?= $reading->source ?></td>
    <?php if ($this->readingsHaveFixation()) { ?>
        <td><?= $reading->fixation ?? '-' ?></td>
    <?php } ?>
    <td><?= $reading->occluder ?? '-' ?></td>
    <td><?= $reading->display_with_head_posture ?></td>
</tr>
