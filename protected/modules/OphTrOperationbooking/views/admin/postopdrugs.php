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
<?php
/**
 * @todo : refactor the html
 */
?>
<div class="report curvybox white">
    <div class="reportInputs">
        <h3 class="georgia">Post-operative drugs</h3>
        <div>
            <form id="drugs">
                <ul class="standard reduceheight">
                    <li class="header">
                        <span class="column_name">Name</span>
                    </li>
                    <div class="sortable">
                        <?php
                        $criteria = new CDbCriteria();
                        $criteria->compare('deleted', 0);
                        $criteria->order = 'display_order asc';
                        foreach (PostopDrug::model()->findAll($criteria) as $i => $drug) {?>
                            <li class="<?php if ($i % 2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $drug->id?>">
                                <span class="column_name"><a class="drugItem" href="#" rel="<?php echo $drug->id?>"><?php echo $drug->name?></a></span>
                                <span class="column_deleted"><a class="deleteDrugItem" href="#" rel="<?php echo $drug->id?>">delete</a></span>
                            </li>
                        <?php }?>
                    </div>
                </ul>
            </form>
        </div>
    </div>
</div>
<div>
    <?php echo EventAction::button('Add', 'add', array('colour' => 'blue'))->toHtml()?>
</div>
