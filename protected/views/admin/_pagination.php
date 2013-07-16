<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$pagination_max_links = 22;

$_pages = array();

if ($pages >$pagination_max_links) {
	for ($i=$page;$i<$page+($pagination_max_links/2);$i++) {
		$_pages[] = $i;
	}
	for ($i=$pages-($pagination_max_links/2);$i<=$pages;$i++) {
		$_pages[] = $i;
	}
} else {
	for ($i=0;$i<$pages;$i++) {
		$_pages[] = $i+1;
	}
}
?>
<?php if ($page > 1) {?>
	<?php if (isset($url)) {
		$uri = str_replace('{{PAGE}}',$page-1,$url);
	} else {
		$uri = $prefix.($page-1);
	}?>
	<a href="<?php echo Yii::app()->createUrl($uri)?>">&laquo; back</a>
	&nbsp;
<?php } else {?>
	&laquo; back
	&nbsp;
<?php }?>
<?php foreach ($_pages as $i) {?>
	<?php if ($i == $page) {?>
		<span class="selected"><?php echo $i?></span>
	<?php } else {
		if (isset($url)) {
			$uri = str_replace('{{PAGE}}',$i,$url);
		} else {
			$uri = $prefix.$i;
		}?>
		<a href="<?php echo Yii::app()->createUrl($uri)?>"><?php echo $i?></a>
	<?php }?>
	&nbsp;
<?php }?>
<?php if ($page < $pages) {
	if (isset($url)) {
		$uri = str_replace('{{PAGE}}',$page+1,$url);
	} else {
		$uri = $prefix.($page+1);
	}?>
	<a href="<?php echo Yii::app()->createUrl($uri)?>">next &raquo;</a>
	&nbsp;
<?php } else {?>
	next &raquo;
	&nbsp;
<?php }?>
