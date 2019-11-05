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

<div class="oe-full-header flex-layout">
  <div class="title wordcaps">Theatre Diaries</div>
    <?php if ($this->checkAccess('OprnPrint')) { ?>
      <div>
        <button id="btn_print_diary" class="button header-tab">Print</button>
        <button id="btn_print_diary_list" class="button header-tab">Print list</button>
      </div>
    <?php } ?>
</div>

<div class="oe-full-content oe-theatre-diaries flex-layout flex-top">

    <?php $this->beginWidget('CActiveForm', array(
        'id' => 'theatre-filter',
        'htmlOptions' => array(
            'class' => 'data-group',
        ),
        'enableAjaxValidation' => false,
    )) ?>

  <!-- side panel -->
  <nav class="oe-full-side-panel diaries-search">

    <p>Use the filters below to view Theatre schedules</p>

    <h3>Search schedules by</h3>

    <!-- search options -->
    <table class="search-options">
      <tbody>
      <tr>
        <td>Site:</td>
        <td>
            <?=\CHtml::dropDownList('site-id', @$_POST['site-id'],
                Site::model()->getListForCurrentInstitution(), array(
                    'empty' => 'All sites',
                    'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : ''),
                )) ?>
        </td>
      </tr>
      <tr>
        <td>Theatre:</td>
        <td>
            <?=\CHtml::dropDownList('theatre-id', @$_POST['theatre-id'], $theatres, array(
                'empty' => 'All theatres',
                'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : ''),
            )) ?>
        </td>
      </tr>
      <tr>
        <td>Subspeciality:</td>
        <td>
            <?=\CHtml::dropDownList('subspecialty-id', @$_POST['subspecialty-id'],
                Subspecialty::model()->getList(), array(
                    'empty' => 'All specialties',
                    'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : ''),
                )) ?>
        </td>
      </tr>
      <tr>
        <td><?= Firm::contextLabel() ?>:</td>
        <td>
            <?php if (!@$_POST['subspecialty-id']) { ?>
                <?=\CHtml::dropDownList('firm-id', '', array(),
                    array('empty' => 'All '.Firm::model()->contextLabel().'s', 'disabled' => 'disabled')) ?>
            <?php } else { ?>
                <?=\CHtml::dropDownList('firm-id', @$_POST['firm-id'],
                    Firm::model()->getList(@$_POST['subspecialty-id']), array(
                        'empty' => 'All '.Firm::model()->contextLabel().'s',
                        'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : ''),
                    )) ?>
            <?php } ?>
        </td>
      </tr>
      <tr>
        <td>Ward:</td>
        <td>
            <?=\CHtml::dropDownList('ward-id', @$_POST['ward-id'], $wards, array(
                'empty' => 'All wards',
                'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : ''),
            )) ?>
        </td>
      </tr>
      <tr>
        <td>Emergency list</td>
        <td>
            <?=\CHtml::checkBox('emergency_list', (@$_POST['emergency_list'] == 1)) ?>
        </td>
      </tr>
      </tbody>
    </table>


    <h3>Select days</h3>
    <div class="search-date-filters">
      <div class="common-date-ranges flex-layout">

        <label class="inline highlight">
          <input type="radio" name="date-filter" id="date-filter_0"
                 value="today"<?php if (@$_POST['date-filter'] == 'today') {
                        ?> checked="checked"<?php
                              } ?>>
          Today
        </label>
        <label class="inline highlight">
          <input type="radio" name="date-filter" id="date-filter_1"
                 value="week"<?php if (@$_POST['date-filter'] == 'week') {
                        ?> checked="checked"<?php
                             } ?>>
          Next 7 days
        </label>
        <label class="inline highlight">
          <input type="radio" name="date-filter" id="date-filter_2"
                 value="month"<?php if (@$_POST['date-filter'] == 'month') {
                        ?> checked="checked"<?php
                              } ?>>
          Next 30 days
        </label>

      </div><!-- .common-date-ranges -->


      <h3>Filter by Date</h3>

      <div class="flex-layout">
            <?=\CHtml::textField('date-start', @$_POST['date-start'], array('class' => 'cols-5', 'placeholder'=> 'from')) ?>
            <?=\CHtml::textField('date-end', @$_POST['date-end'], array('class' => 'cols-5', 'placeholder' => 'to')) ?>
      </div>

      <div class="flex-layout v-pad">
        <button id="last_week" class="button">Last week</button>
        <button id="next_week" class="button">Next week</button>
      </div>


    </div><!-- search-date-filters -->

    <i class="spinner" style="display:none"></i>
    <button class="green hint cols-full" id="search_button" type="submit">Search</button>

  </nav>

    <?php $this->endWidget() ?>

  <main class="oe-full-main">
        <?php $this->renderPartial('//base/_messages'); ?>

    <!--<div class="alert-box info"><strong><span id="result_count">99</span> Results</strong></div>-->
    <div id="theatreList" class="theatres-list"></div>
    <div class="printable" id="printable"></div>
    <div id="iframeprintholder" style="display: none;"></div>

  </main>

</div>

<style>
  .printable {
    display: none;
  }

  .printable * {
    font-size: 9pt;
  }

  @media print {
    .printable {
      display: block !important;
      width: 1050px !important;
    }
  }
</style>

<script type="text/javascript">
  $(document).ready(function () {
    pickmeup('#date-start', {
      format: 'Y-m-d'
    });
    pickmeup('#date-end', {
      format: 'Y-m-d'
    });

    return getDiary();
  });
</script>
