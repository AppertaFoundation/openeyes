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
$based_on = array();
if ($search_terms['last_name']) {
    $based_on[] = 'LAST NAME: <p>"' . $search_terms['last_name'] . '"</p>';
}
if ($search_terms['first_name']) {
    $based_on[] = 'FIRST NAME: <p>"' . $search_terms['first_name'] . '"</p>';
}
if ($search_terms['hos_num']) {
    $based_on[] = 'HOSPITAL NUMBER: <p>' . $search_terms['hos_num'] . '</p>';
}
$core_api = new CoreAPI();

$based_on = implode(', ', $based_on);
?>

<?php $this->renderPartial('//base/_search_bar', array(
    'callback' => Yii::app()->createUrl('site/search'),
)); ?>
<div class="patient-search-results subgrid">
  <div class="search-details">
    <div class="title">Results</div>
    <div class="found"><?php echo $total_items ?> patients found</div>
    <div class="search-critera">based on <?php echo $based_on ?></div>
    <button id="js-clear-search-btn" class="blue hint cols-full clear-search">Clear search results</button>
  </div>
  <div class="results-all">
        <?php $this->renderPartial('//base/_messages');
        $dataProvided = $data_provider->getData();
        $items_per_page = $data_provider->getPagination()->getPageSize();
        $page_num = $data_provider->getPagination()->getCurrentPage();
        $from = ($page_num * $items_per_page) + 1;
        $to = ($page_num + 1) * $items_per_page;
        if ($to > $total_items) {
            $to = $total_items;
        }
        ?>
    <table class="standard search-results clickable-rows">
        <colgroup>
        </colgroup>
      <thead>
      <tr>
            <?php foreach (array(
                             'No.',
                             'Title',
                             'First name',
                             'Last name',
                             'Born',
                             'Age',
                             'Gender',
                             'NHS',
                         ) as $i => $field) { ?>
              <th id="patient-grid_c<?php echo $i; ?>">
                  <?php
                    $new_sort_dir = 0;
                    if ($i == $sort_by) {
                        $new_sort_dir = 1 - $sort_dir;
                        echo CHtml::link(
                          $field,
                          Yii::app()->createUrl('patient/search',
                              array('term' => $term, 'sort_by' => $i, 'sort_dir' => $new_sort_dir, 'page_num' => $page_num)),
                           array('class' => in_array($i, array(0, 2, 4, 5)) ? 'sortable' : '')
                        );
                        ?>
                      <i class="oe-i <?= ($sort_dir == 0) ? 'arrow-up-bold' : 'arrow-down-bold'; ?> small pad active"></i>
                    <?php } else {
                        echo CHtml::link(
                          $field,
                          Yii::app()->createUrl('patient/search',
                              array('term' => $term, 'sort_by' => $i, 'sort_dir' => $new_sort_dir, 'page_num' => $page_num)),
                           array('class' => in_array($i, array(0, 2, 4, 5)) ? 'sortable' : '')
                        );
                    }
                    ?>
              </th>
            <?php } ?>
      </tr>
      </thead>
      <tbody>
        <?php foreach ($dataProvided as $i => $result) { ?>
        <tr id="r<?php echo $result->id ?>" class="clickable found-patient"
            data-link="<?php echo $core_api->generatePatientLandingPageLink($result); ?>"
            <?php
            echo "data-hos_num='{$result->hos_num}'";
            if ($result->isNewRecord) {
                echo " data-is_new_record='1'";
            }
            ?>
        >
          <td><?php echo $result->hos_num ?></td>
          <td><?php echo $result->title ?></td>
          <td><?php echo $result->first_name ?></td>
          <td><?php echo $result->last_name ?></td>
          <td><?php echo $result->dob ? (date('d/m/Y', strtotime($result->dob))) : ''; ?></td>
          <td><?php echo $result->getAge(); ?></td>
          <td><?php echo $result->gender ?></td>
          <td><?php echo $result->nhsnum ?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="8">
            <?php $this->widget('LinkPager', [ 'pages' => $data_provider->getPagination() ]); ?>
        </td>
      </tr>
      </tfoot>
    </table>
  </div>
</div>

<script type="text/javascript">
  $('.search-results').on('click', 'tr.clickable', function () {
    var url;

    if ($(this).data('is_new_record') === 1 && $(this).data('hos_num') !== undefined) {
      url = '<?php echo Yii::app()->createUrl('patient/search')?>?term=' + $(this).data('hos_num');
    } else {
      url = $(this).attr('data-link');
    }
    window.location.href = url;
    return false;
  });

  $('#js-clear-search-btn').click(function () {
    window.location.assign('<?php echo Yii::app()->baseUrl . '/'; ?>');
  });
</script>
