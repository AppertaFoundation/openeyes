<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
	$based_on = array();
	if ($search_terms['last_name']) {
		$based_on[] = 'LAST NAME: <strong>"'.$search_terms['last_name'].'"</strong>';
	}
	if ($search_terms['first_name']) {
		$based_on[] = 'FIRST NAME: <strong>"'.$search_terms['first_name'].'"</strong>';
	}
	if ($search_terms['hos_num']) {
		$based_on[] = 'HOSPITAL NUMBER: <strong>'.$search_terms['hos_num']."</strong>";
	}
	$based_on = implode(', ', $based_on);
	?>
<h2>Search Results</h2>
<div class="wrapTwo clearfix">
	<div class="wideColumn">
		<p>
			<strong><?php echo $total_items?> patients found</strong>, based on
			<?php echo $based_on?>
		</p>

		<?php $this->renderPartial('//base/_messages'); ?>

		<div class="whiteBox">
			<?php
				$from =($page_num * $items_per_page) + 1;
				$to = ($page_num + 1) * $items_per_page;
				if ($to > $total_items) {
					$to = $total_items;
				}
			?>
			<h3>
				Results. You are viewing patients <?php echo $from ?> - <?php echo $to ?> of <?php echo $total_items?>
			</h3>

			<div id="patient-grid" class="grid-view">
				<table class="items">
					<thead>
						<tr>
							<?php foreach (array('Hospital Number','Title','First name','Last name','Date of birth','Gender','NHS number') as $i => $field) {?>
							<th id="patient-grid_c<?php echo $i; ?>">
								<?php
									$new_sort_dir = ($i == $sort_by) ? 1 - $sort_dir: 0;
									echo CHtml::link($field,Yii::app()->createUrl('patient/search', $search_terms + array('sort_by' => $i, 'sort_dir' => $new_sort_dir, 'page_num' => $page_num)));
								?>
							</th>
							<?php }?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data_provider->getData() as $i => $result) {?>
						<tr id="r<?php echo $result->id?>" class="<?php if ($i%2 == 0) {?>even<?php }else{?>odd<?php }?>">
							<td><?php echo $result->hos_num?></td>
							<td><?php echo $result->title?></td>
							<td><?php echo $result->first_name?></td>
							<td><?php echo $result->last_name?></td>
							<td><?php echo $result->dob?></td>
							<td><?php echo $result->gender?></td>
							<td><?php echo $result->nhsnum?></td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
			<div class="resultsPagination">
				Viewing patients:
				<?php for ($i=0; $i < $pages; $i++) { ?>
				<?php
					$current_page = ($i == $page_num);
					$from = ($i * $items_per_page) + 1;
					$to = ($i + 1) * $items_per_page;
					if ($to > $total_items) {
						$to = $total_items;
					}
				?>
				<span class="<?php if($i > 0) { ?>notFirst <?php } ?><?php if($current_page) { ?>showingPage<?php } else { ?>otherPages<?php } ?>">
				<?php if($current_page) { ?>
					<?php echo $from; ?> - <?php echo $to; ?>
				<?php } else { ?>
					<a href="<?php echo Yii::app()->createUrl('patient/search', $search_terms + array('page_num' => $i, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir)); ?>"><?php echo $from; ?> - <?php echo $to; ?></a>
				<?php } ?>
				</span>
				<?php } ?>
			</div>

		</div>
		<!-- .whiteBox -->

	</div>
	<!-- .wideColumn -->

	<div class="narrowColumn">
			<p><?php echo CHtml::link('Clear this search and <span class="aPush">start a new search</span>',Yii::app()->baseUrl.'/')?></p>
	</div> <!-- .narrowColumn -->
	
</div>
<!-- .wrapTwo -->
<script type="text/javascript">
	$('#patient-grid .items tbody tr').click(function() {
		window.location.href = '<?php echo Yii::app()->createUrl('patient/view')?>/'+$(this).attr('id').match(/[0-9]+/);
		return false;
	});
</script>
