<?php
/**
 * The following variables are used in this template:
 * - $this: the CrudCode object
 * - $ignore: Array of fields to ignore when generating code.
 */
?>
<?php echo "<?php\n"; ?>
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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
<?php echo "?>\n"; ?>
<p><?php echo $this->elementName; ?></p>
<div class="view">
<?php
foreach ($this->tableSchema->columns as $column) {
    if ($column->isPrimaryKey) {
        continue;
    }
    if (in_array($column->name, $ignore)) {
        continue;
    }
    echo "\t<b><?=\CHtml::encode(\$data->getAttributeLabel('{$column->name}')); ?>:</b>\n";
    echo "\t<?=\CHtml::encode(\$data->{$column->name}); ?>\n\t<br />\n";
}
?>
</div>
