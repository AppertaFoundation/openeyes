<?php
/* @var TrialController $this */
/* @var CActiveDataProvider $trialDataProvider */
/* @var string $sort_by */
/* @var string $sort_dir */

?>
<div class="oe-full-header flex-layout">
  <div class="title wordcaps">Trials</div>
</div>
<div class="oe-full-content subgrid oe-worklists">

    <?php if (Yii::app()->user->hasFlash('success')) : ?>
      <div class="alert-box with-icon success">
          <?php echo Yii::app()->user->getFlash('success'); ?>
      </div>
    <?php endif; ?>

  <nav class="oe-full-side-panel">
      <h3>Search</h3>
      <div class="flex-layout">
          <input type="text" class="cols-full search js-trial-search-input" placeholder="Search" id="trial-search-input-id">
          <button class="blue hint" id="trial-search-btn-id">Search</button>
      </div>
    <h3>Filter by Date <i class="oe-i info small pad js-has-tooltip" data-tooltip-content="Use 'dd Mon yyyy' format (e.g. 1 Jan 2021) for from / to dates"></i></h3>
    <div class="flex-layout">
      <input id="js-trial-search-from-date" class="cols-5 js-trial-search-input js-trial-search-date" placeholder="from" type="text">
      <input id="js-trial-search-to-date" class="cols-5 js-trial-search-input  js-trial-search-date" placeholder="to" type="text">
    </div>

    <h3>Actions</h3>
        <?php if (Yii::app()->user->checkAccess('TaskCreateTrial')) : ?>
        <ul>
          <li>
              <?= CHtml::link('Create a New Trial', array('create')) ?>
          </li>
            <?php if (\CsvController::uploadAccess()) : ?>
              <li>
                  <?= CHtml::link(
                      'Upload trials',
                      Yii::app()->createURL(
                          'csv/upload',
                          array('context' => 'trials', 'backuri' => '/OETrial/trial')
                      )
                  ) ?>
              </li>
              <li>
                  <?= CHtml::link(
                      'Upload trial patients',
                      Yii::app()->createURL(
                          'csv/upload',
                          array('context' => 'trialPatients', 'backuri' => '/OETrial/trial' )
                      )
                  ) ?>
              </li>
            <?php endif ?>
        </ul>
        <?php endif; ?>
  </nav>

  <main class="oe-full-main">
        <?php
        $this->renderPartial('_trial_list', array(
          'dataProvider' => $trialDataProvider,
          'title' => 'Trials',
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
        ));
        ?>
        <?php
        $this->renderPartial('_trial_list_searched', array(
          'dataProvider' => $trialSearchDataProvider,
          'title' => 'Trials',
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
        ));
        ?>
  </main>
</div>

<script type="text/javascript">
  $(document).ready(function() {
      $('.js-trial-list .clickable').click(function () {
        window.location.href = '<?= $this->createUrl('view')?>/' + $(this).attr('id').match(/[0-9]+/);
        return false;
      });

      $('#trial-search-btn-id').click(function () {
          search_in_list();
      });

      $('.js-trial-search-input').on('keyup',function (e) {
          if (e.keyCode == 13){
              search_in_list();
          }
      });

      pickmeup('#js-trial-search-from-date', {
          format: 'd b Y',
          hide_on_select: true,
          date: $('#js-trial-search-from-date').val().trim(),
          default_date: false,
      });

      pickmeup('#js-trial-search-to-date', {
          format: 'd b Y',
          hide_on_select: true,
          date: $('#js-trial-search-to-date').val().trim(),
          default_date: false,
      });

      $('.js-trial-search-date').on('pickmeup-change', function() {
        search_in_list();
      });

      function getValidDateFrom(selector) {
        const raw = $(selector).val();

        $(selector).removeClass('error');

        if (raw) {
          try {
            return $.datepicker.parseDate('d M yy', raw);
          } catch (e) {
            $(selector).addClass('error');
          }
        }

        return null;
      }

      function search_in_list(){
          const searchContent = $('#trial-search-input-id').val();
          let searchFromDate = getValidDateFrom('#js-trial-search-from-date');
          let searchToDate = getValidDateFrom('#js-trial-search-to-date');

          if (searchContent || searchFromDate || searchToDate) {
              $('.trial-list').hide();
              $('.searched-trial-list').show();

              $('.searched-trial-list').find('.clickable').hide();
              $('.searched-trial-list').find('.clickable').attr("data-hidden-label",'hide');

              if (searchContent) {
                $(`[data-trial-name*="${searchContent}" i]`).show();
                $(`[data-trial-name*="${searchContent}" i]`).attr("data-hidden-label",'show');
                $(`[data-trial-description*="${searchContent}" i]`).show();
                $(`[data-trial-description*="${searchContent}" i]`).attr("data-hidden-label",'show');
              }

              if (searchFromDate || searchToDate) {
                if (searchFromDate && searchToDate && searchToDate < searchFromDate) {
                  const temp = searchToDate;

                  searchToDate = searchFromDate;
                  searchFromDate = temp;
                }

                $('.searched-trial-list tr[data-trial-start]').not('[data-trial-start=""]').each(function() {
                  const row = $(this);

                  const start = $.datepicker.parseDate('yy-mm-dd', row.data('trial-start'));
                  let closed = row.data('trial-closed');

                  const startInside = (!searchToDate || start <= searchToDate);
                  let closedInside = true;

                  if (closed) {
                    closed = $.datepicker.parseDate('yy-mm-dd', closed);

                    closedInside = (!searchFromDate || closed >= searchFromDate);
                  }

                  if (startInside && closedInside) {
                    row.show();
                    row.attr('data-hidden-label', 'show');
                  }
                });
              }

              $("#search-table-trials").makePagination(10);
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
                  $(this).parent('tr').find('.trials-search-list-a').each(function () {
                      $(this).html(' ');
                  });
                  $(this).find('.trials-search-list-a').html(' &#x25B2;');
              }else{
                  $(this).parent('tr').find('.trials-search-list-a').each(function () {
                      $(this).html(' ');
                  });
                  $(this).find('.trials-search-list-a').html(' &#x25BC;');
              }
              if (!this.asc){rows = rows.reverse();}
              table.children('tbody').empty().html(rows);
              (table).makePagination(10);
          });
      };



      $(function () {
          $("#search-table-trials").makeTableSortable();
      });
  });
</script>
