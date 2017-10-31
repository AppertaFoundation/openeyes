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
$uri_append = $this->getUriAppend();
?>
<ul class="pagination">
	<li class="previous<?php echo $this->page <= 1 ? ' unavailable' : '';?>">
		<?php echo CHtml::link(
            '&lt; Previous',
            Yii::app()->createUrl('/OphTrOperationbooking/transport/index?page='.($this->page - 1).$uri_append),
            array('rel' => 'back')
        )?>
	</li>

	<?php for ($i = 1;$i <= $this->pages;++$i) {?>
		<li class="page<?php echo $i == $this->page ? ' current' : '';?>">
			<?php echo CHtml::link(
                $i,
                Yii::app()->createUrl('/OphTrOperationbooking/transport/index?page='.$i.$uri_append),
                array('rel' => $i)
            )?>
		</li>
	<?php }?>


	<li class="next<?php echo !$this->pages || $this->page == $this->pages ? ' unavailable' : '';?>">
		<?php echo CHtml::link(
            'Next &gt;',
            Yii::app()->createUrl('/OphTrOperationbooking/transport/index?page='.($this->page + 1).$uri_append),
            array('rel' => 'back')
        )?>
	</li>
</div>
