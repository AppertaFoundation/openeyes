<?php
/**
 * The following variables are used in this template:
 * - $this: the CrudCode object
 * - $ignore: Array of fields to ignore when generating code
 */
?>
<?php echo "<?php\n"; ?>
/**
 * ____________________________________________________________________________
 *
 * This file is part of OpenEyes.
 *
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file
 * titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * _____________________________________________________________________________
 * http://www.openeyes.org.uk   info@openeyes.org.uk
 *
 * @author Bill Aylward <bill.aylward@openeyes.org.uk>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3.0
 * @license http://www.openeyes.org.uk/licenses/oepl-1.0.html OEPLv1.0
 * @version 0.9
 * Creation date: 27 December 2011
 * @copyright Copyright (c) 2012 OpenEyes Foundation, Moorfields Eye hospital
 * @package Clinical
 */
<?php echo "?>\n"; ?>
<p><?php echo $this->elementName; ?></p>
<div class="view">
<?php
foreach ($this->tableSchema->columns as $column) {
	if($column->isPrimaryKey)
		continue;
	if(in_array($column->name, $ignore))
		continue;
	echo "\t<b><?php echo CHtml::encode(\$data->getAttributeLabel('{$column->name}')); ?>:</b>\n";
	echo "\t<?php echo CHtml::encode(\$data->{$column->name}); ?>\n\t<br />\n";
}
?>
</div>
