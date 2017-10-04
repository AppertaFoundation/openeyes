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
    $based_on[] = 'LAST NAME: <strong>"' . $search_terms['last_name'] . '"</strong>';
}
if ($search_terms['first_name']) {
    $based_on[] = 'FIRST NAME: <strong>"' . $search_terms['first_name'] . '"</strong>';
}
if ($search_terms['hos_num']) {
    $based_on[] = 'HOSPITAL NUMBER: <strong>' . $search_terms['hos_num'] . '</strong>';
}
$core_api = new CoreAPI();

$based_on = implode(', ', $based_on);
?>
<div class="row">
    <div class="large-9 column">

        <div class="box generic">
            <p>
                <strong><?php echo $total_items ?> patients found</strong>, based on
                <?php echo $based_on ?>
            </p>
        </div>

        <?php $this->renderPartial('//base/_messages'); ?>

        <div class="box generic">

            <?php
            $dataProvided = $data_provider->getData();
            $items_per_page = $data_provider->getPagination()->getPageSize();
            $page_num = $data_provider->getPagination()->getCurrentPage();
            $from = ($page_num * $items_per_page) + 1;
            $to = ($page_num + 1) * $items_per_page;
            if ($to > $total_items) {
                $to = $total_items;
            }
            ?>
            <h2>
                Results. You are viewing patients <?php echo $from ?> - <?php echo $to ?> of <?php echo $total_items ?>
            </h2>

            <table id="patient-grid" class="grid">
                <thead>
                <tr>
                    <?php foreach (array('Hospital Number', 'Title', 'First name', 'Last name', 'Date of birth', 'Gender', 'NHS number') as $i => $field) { ?>
                        <th id="patient-grid_c<?php echo $i; ?>">
                            <?php
                            $new_sort_dir = ($i == $sort_by) ? 1 - $sort_dir : 0;
                            echo CHtml::link(
                                $field,
                                Yii::app()->createUrl('patient/search', array('term' => $term, 'sort_by' => $i, 'sort_dir' => $new_sort_dir, 'page_num' => $page_num))
                            );
                            ?>
                        </th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dataProvided as $i => $result) { ?>
                    <tr id="r<?php echo $result->id ?>" class="clickable" data-link="<?php echo $core_api->generateEpisodeLink($result); ?>"
                        <?php
                            echo "data-hos_num='{$result->hos_num}'";
                            if($result->isNewRecord){
                                echo " data-is_new_record='1'";
                            }
                        ?>
                    >
                        <td><?php echo $result->hos_num ?></td>
                        <td><?php echo $result->title ?></td>
                        <td><?php echo $result->first_name ?></td>
                        <td><?php echo $result->last_name ?></td>
                        <td><?php echo $result->dob ? ( date('d/m/Y', strtotime($result->dob)) ) : ''; ?></td>
                        <td><?php echo $result->gender ?></td>
                        <td><?php echo $result->nhsnum ?></td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot class="pagination-container">
                <tr>
                    <td colspan="7">
                        <?php
                        $this->widget('LinkPager', array(
                            'pages' => $data_provider->getPagination(),
                            'maxButtonCount' => 15,
                            'cssFile' => false,
                            'selectedPageCssClass' => 'current',
                            'hiddenPageCssClass' => 'unavailable',
                            'htmlOptions' => array(
                                'class' => 'pagination',
                            ),
                        ));
                        ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div><!--- /.box -->

    </div><!-- /.large-9.column -->

    <div class="large-3 column">
        <div class="box generic">
            <p><?php echo CHtml::link('Clear this search and <span class="highlight">start a new search.</span>', Yii::app()->baseUrl . '/') ?></p>
        </div>
    </div>

</div><!-- /.row -->

<script type="text/javascript">
    $('#patient-grid').on('click', 'tr.clickable', function(){
        var url;

        if( $(this).data('is_new_record') === 1 && $(this).data('hos_num') !== undefined ){
            url = '<?php echo Yii::app()->createUrl('patient/search')?>?term=' + $(this).data('hos_num');
        } else {
            url = $(this).attr('data-link');
        }
        window.location.href = url;
        return false;
    });

</script>
