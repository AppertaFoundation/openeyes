<?php
/* @var TrialController $this */
/* @var CActiveDataProvider $interventionTrialDataProvider */
/* @var CActiveDataProvider $nonInterventionTrialDataProvider */
/* @var string $sort_by */
/* @var string $sort_dir */

?>
<div class="oe-full-header flex-layout">
  <div class="title wordcaps">Trials</div>
</div>
<div class="oe-full-content subgrid oe-worklists">

    <?php if (Yii::app()->user->hasFlash('success')): ?>
      <div class="alert-box with-icon success">
          <?php echo Yii::app()->user->getFlash('success'); ?>
      </div>
    <?php endif; ?>

  <nav class="oe-full-side-panel">
      <h3>Search</h3>
      <div class="flex-layout">
          <input type="text" class="cols-full search" placeholder="Search" id="trial-search-input-id">
          <button class="blue hint" id="trial-search-btn-id">Search</button>
      </div>
    <h3>Filter by Date</h3>
    <div class="flex-layout">
      <input class="cols-5" placeholder="from" type="text">
      <input class="cols-5" placeholder="to" type="text">
    </div>

    <h3>Actions</h3>
      <?php if (Yii::app()->user->checkAccess('TaskCreateTrial')): ?>
        <ul>
          <li>
              <?= CHtml::link('Create a New Trial', array('create')) ?>
          </li>
            <?php if (\CsvController::uploadAccess()): ?>
              <li>
                  <?= CHtml::link('Upload trials', Yii::app()->createURL('csv/upload', array('context' => 'trials'))) ?>
              </li>
              <li>
                  <?= CHtml::link('Upload trial patients',
                      Yii::app()->createURL('csv/upload', array('context' => 'trialPatients'))) ?>
              </li>
            <?php endif ?>
        </ul>
      <?php endif; ?>
  </nav>

  <main class="oe-full-main">
      <?php
      $this->renderPartial('_trial_list', array(
          'dataProvider' => $interventionTrialDataProvider,
          'title' => 'Intervention Trials',
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      ));
      ?>
      <?php
      $this->renderPartial('_trial_list', array(
          'dataProvider' => $nonInterventionTrialDataProvider,
          'title' => 'Non-Intervention Trials',
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      ));
      ?>
      <?php
      $this->renderPartial('_trial_list_searched', array(
          'dataProvider' => $interventionTrialSearchDataProvider,
          'title' => 'Intervention Trials',
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      ));
      ?>
      <?php
      $this->renderPartial('_trial_list_searched', array(
          'dataProvider' => $nonInterventionTrialSearchDataProvider,
          'title' => 'Non-Intervention Trials',
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      ));
      ?>
  </main>
</div>

<script type="text/javascript">
  $('.js-trial-list .clickable').click(function () {
    window.location.href = '<?= $this->createUrl('view')?>/' + $(this).attr('id').match(/[0-9]+/);
    return false;
  });

  $('#trial-search-btn-id').click(function () {
          search_in_list();
  });

  $('#trial-search-input-id').on('keyup',function (e) {
      if (e.keyCode == 13){
          search_in_list();
      }
  });

  function search_in_list(){
      var $search_content = $('#trial-search-input-id').val();
      if ($search_content) {
          $('.trial-list').hide();
          $('.searched-trial-list').show();
          $('.searched-trial-list').find('.clickable').hide();
          $('.searched-trial-list').find('.clickable').attr("data-hidden-label",'hide');
          $('[data-trial-name*="'+$search_content+ '" i]').show();
          $('[data-trial-name*="'+$search_content+ '" i]').attr("data-hidden-label",'show');
          $('[data-trial-description*="'+$search_content+ '" i]').show();
          $('[data-trial-description*="'+$search_content+ '" i]').attr("data-hidden-label",'show');
          $("#search-table-non-intervention-trials").makePagination(10);
          $("#search-table-intervention-trials").makePagination(10);
      }else {
          $('.trial-list').show();
          $('.searched-trial-list').hide();
      }
  }

  $.fn.makePagination= function(page_size){
      var table = $(this);
      var number_of_items = table.find('tbody').find('[data-hidden-label="show"]').length;
      var number_of_pages = Math.ceil(number_of_items/page_size);
      table.find('tbody').find('[data-hidden-label="show"]').hide();
      table.find('tbody').find('[data-hidden-label="show"]').slice(0,page_size).show();
      var navigation_bar_html = '<div id="pagination-info"></div><a class="oe-i arrow-left-bold medium pad unavailable" id="'+table.attr('id')+'-prev"></a>';
      for (var i = 0; i < number_of_pages; i++) {
          navigation_bar_html += '<span class="pagination-pages"><a class="page page_link " id="'+table.attr('id')+'-page-'+i+'" data-'+table.attr('id')+'-current-page="'+i+'">'+ (i+1) +'</a></span>';
      }
      navigation_bar_html += '<a class="oe-i arrow-right-bold medium pad" id="'+table.attr('id')+'-next"></a><input id="'+table.attr('id')+'-current-page" type="hidden" value="0">';
      table.find('tfoot').find('.pagination').html(navigation_bar_html);

      if (number_of_items > page_size){
          table.find('tfoot').find('#pagination-info').html('1 - '+ page_size +' of '+table.find('tbody').find('[data-hidden-label="show"]').length +' ');
      }else {
          table.find('tfoot').find('#pagination-info').html('1 - '+ number_of_items +' of '+table.find('tbody').find('[data-hidden-label="show"]').length +' ');
      }

      table.find('#'+table.attr('id')+'-next').click(
          function(){
              table.find('tbody').find('tr').hide();
              var current_page = table.find('#'+table.attr('id')+'-current-page').val();
              if (current_page >= (number_of_pages-1)) {
                  current_page -= 1;
              }
              table.find('#'+table.attr('id')+'-current-page').val((parseInt(current_page)+1));
              table.find('tbody').find('[data-hidden-label="show"]').slice((parseInt(current_page)+1)*page_size,(parseInt(current_page)+2)*page_size).show();
              var from = parseInt(current_page+1)*page_size+1;
              var to = parseInt(current_page+2)*page_size;
              if (from < 1){
                  from = 1;
              }
              if (to > number_of_items){
                  to = number_of_items
              }
              table.find('tfoot').find('#pagination-info').html(from +' - '+ to +' of '+table.find('tbody').find('[data-hidden-label="show"]').length +' ');
          }
      );

      table.find('#'+table.attr('id')+'-prev').click(
          function(){
              table.find('tbody').find('[data-hidden-label="show"]').hide();
              var current_page = table.find('#'+table.attr('id')+'-current-page').val();
              if (current_page == 0 ) {
                  current_page = 1;
              }
              table.find('#'+table.attr('id')+'-current-page').val((parseInt(current_page)-1));
              table.find('tbody').find('[data-hidden-label="show"]').slice((parseInt(current_page)-1)*page_size,(parseInt(current_page))*page_size).show();
              var from = parseInt(current_page-1)*page_size+1;
              var to = parseInt(current_page)*page_size;
              if (from < 1){
                  from = 1;
              }
              if (to > number_of_items){
                  to = number_of_items
              }
              table.find('tfoot').find('#pagination-info').html(from +' - '+ to +' of '+table.find('tbody').find('[data-hidden-label="show"]').length +' ');
          });

      table.find('.page_link').click(
          function(){
              table.find('tbody').find('[data-hidden-label="show"]').hide();
              var current_page = $(this).data(table.attr('id')+'-current-page');
              table.find('#'+table.attr('id')+'-current-page').val(current_page);
              table.find('tbody').find('[data-hidden-label="show"]').slice((parseInt(current_page))*page_size,(parseInt(current_page)+1)*page_size).show();
              var from = parseInt(current_page)*page_size+1;
              var to = parseInt(current_page+1)*page_size;
              if (from < 1){
                  from = 1;
              }
              if (to > number_of_items){
                  to = number_of_items
              }
              table.find('tfoot').find('#pagination-info').html(from +' - '+ to +' of '+table.find('tbody').find('[data-hidden-label="show"]').length +' ');
          }
      );
  };

  $.fn.makeTableSortable= function(){
      var table = this;
      var getCellValue = function (row, index){
          return $(row).children('td').eq(index).text();
      };

      table.find('th').click(function(){
          var table = $(this).parents('table').eq(0);

          var compare = function(index,header){
              if (header.toLowerCase().includes('closed')){
                  return function(a, b) {
                      var valA = getCellValue(a, index).toLowerCase(), valB = getCellValue(b, index).toLowerCase();
                      if (valA == "present"){
                          return false;
                      } else if (valA == "undated" && valB != "present"){
                          return false;
                      } else if (!["undated","present"].includes(valB)) {
                          return valA.localeCompare(valB);
                      }else{
                          return true;
                      }
                  };
              }else {
                  return function(a, b) {
                      var valA = getCellValue(a, index), valB = getCellValue(b, index);
                      return $.isNumeric(valA) && $.isNumeric(valB) ?
                          valA - valB : valA.localeCompare(valB);
                  };
              }
          };
          var rows=table.find('tbody').find('tr').toArray().sort(compare($(this).index(),$(this).text()));

          this.asc = !this.asc;

          if (this.asc){
              $(this).parent('tr').find('#trials-search-list-a').each(function () {
                  $(this).html(' ');
              });
              $(this).find('#trials-search-list-a').html(' &#x25B2;');
          }else{
              $(this).parent('tr').find('#trials-search-list-a').each(function () {
                  $(this).html(' ');
              });
              $(this).find('#trials-search-list-a').html(' &#x25BC;');
          }
          if (!this.asc){rows = rows.reverse();}
          table.children('tbody').empty().html(rows);
          (table).makePagination(10);
      });
  };



  $(function () {
      $("#search-table-non-intervention-trials").makeTableSortable();
      $("#search-table-intervention-trials").makeTableSortable();
  });
</script>
