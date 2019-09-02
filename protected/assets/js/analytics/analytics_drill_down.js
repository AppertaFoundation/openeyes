var analytics_drill_down = (function(){
  var start = 0;
  var limit = 300;
  var reachedMax = false;
  var g_data = [];
  // var token = YII_CSRF_TOKEN
  // console.log('analytics_drill_down scope')
  
  // function getPatient(url, data){
  function getPatient(){
    // console.log(YII_CSRF_TOKEN)
    // console.log('fire');
    // console.log(start)
    // console.log(limit)
    // var temp = g_data.splice(start, limit);
    if(reachedMax && g_data.length <= 0){
      // console.log('in getpatient')
      // console.log('reached max')
      // $('main.oe-analytics').off('scroll');
      $('#js-analytics-spinner').hide();
      return;
    }
    // console.log(g_data)
    $.ajax({
      url: '/analytics/getdrilldown',
      type: "POST",
      data: {
        drill: true,
        // ids: g_data.splice(start, limit),
        YII_CSRF_TOKEN: YII_CSRF_TOKEN,
        ids: JSON.stringify(g_data.splice(start, limit)),
        specialty: analytics_toolbox.getCurrentSpecialty(),
        // start: start,
        // limit: limit,
      },
      dataType: 'json',
      success: function (response) {
        // console.log(g_data);
        if(response == "reachedMax"){
          reachedMax = true;
          $('#js-analytics-spinner').hide();
        } else {
          // start += limit;
          $("#p_list").append(response);
        }
      },
      complete: function() {
        $('#p_list tr.analytics-patient-list-row.clickable').click(function(){
          var link = $(this).data('link');
          window.location.href = link;
        })
        $('#js-analytics-spinner').hide();
      }
    });
  }
  function scrollPatientList(){
    // console.log($(this).scrollTop())
    // console.log($(document).height())
    // console.log($(this).height())
    // console.log((document.body.getBoundingClientRect()).top)
    var scroll_h = document.querySelector("main.oe-analytics").scrollHeight;
    // console.log(scroll_h - scroll_h / 3);
    // console.log(scroll_h)
    // console.log($(this).scrollTop())
    if($(this).scrollTop() > scroll_h - scroll_h / 3){
      if($('#js-analytics-spinner').css('display') === 'block'){
        return;
      }
      $('#js-analytics-spinner').show();
      getPatient();
    }
  }
  
  var init = function(ele, clinical_data){
    console.log(clinical_data)
    // console.log(typeof ele);
    // console.log($('.analytics-section.selected'));
    var ele = typeof(ele) === 'undefined' ? $('.analytics-section.selected').data('section') : ele;
    // console.log(typeof(ele) === 'undefined')
    // var selected_tab = $('.analytics-section.selected').data('section');
    // console.log(ele);
    // var plot_patient = document.getElementById('js-hs-chart-analytics-service');
    // var plot_patient = document.getElementById('js-hs-chart-analytics-service');
    var plot_patient = typeof(ele) === 'object' ? ele : document.getElementById(ele.replace('#', ''));

    // avoid multiple binding
    var custom_data = null;
    $(plot_patient).off('plotly_click');
    $(plot_patient).on('plotly_click', function (e, data) {
      // console.log(analytics_toolbox.getCleanDrillDownList())
      // console.log(data);
      custom_data = data.points[0].customdata
      // console.log(custom_data);
      // console.log(custom_data);
      var specialty = analytics_toolbox.getCurrentSpecialty();
      var patient_list_container = $('.analytics-patient-list');
      $(patient_list_container).find('table').html(analytics_toolbox.getCleanDrillDownList());
      var colGroup = $('.analytics-patient-list table colgroup')
      if(Array.isArray(custom_data)){
        $('#js-analytics-spinner').show();
        $('.analytics-charts').hide();
        patient_list_container.show();
        if(specialty === 'Cataract'){
          patient_list_container.addClass('analytics-event-list');
          $('<th class="text-left" style="vertical-align: center;">Eye</th>').insertBefore('.analytics-patient-list .patient_procedures');
          $('<th style="vertical-align: center;">Date</th>').insertAfter('.analytics-patient-list .patient_procedures');
          // $('.analytics-patient-list .patient_procedures').insertBefore('<th class="text-left" style="vertical-align: center;">Eye</th>');
          // $('.analytics-patient-list .patient_procedures').insertAfter('<th style="vertical-align: center;">Date</th>');
          colGroup.append('<col style="width: 350px;"><col style="width: 50px;"><col style="width: 400px;"><col style="width: 100px;">')
        } else {
          colGroup.append('<col style="width: 450px;"><col style="width: 450px;">')
        }
        $('.analytics-patient-list-row').hide();
        // console.log(customdata)
        // console.log(customdata)
        g_data = custom_data.slice();
        // console.log(g_data)
        // console.log(g_data);
        getPatient();
      } else {
        // console.log(clinical_data)
        if(clinical_data){
          analytics_clinical('update', clinical_data, custom_data);
        }
      }
      // console.log(Array.isArray(custom_data))
      // console.log($('#p_list tr'))
      // $('.clickable').click(function () {
      //   var link = $(this).data('link');
      //   window.location.href = link;
      //   console.log(window.location.href)
      // });
      // console.log(data)
    });
    // $('main.oe-analytics').scroll(function(){
    //   // console.log($(this).scrollTop())
    //   // console.log($(document).height())
    //   // console.log($(this).height())
    //   if($(this).scrollTop() > $(document).height()){
    //     $('#js-analytics-spinner').show();
    //     getPatient(url, g_data);
    //   }
    // });
    $('main.oe-analytics').off('scroll')
    $('main.oe-analytics').on('scroll', _.throttle(scrollPatientList, 500))

    $('#js-back-to-chart').off('click')
    $('#js-back-to-chart').on('click', function () {
      reachedMax = false;
      start = 0;
      $('.analytics-charts').show();
      $('.analytics-patient-list').hide();
      $('.analytics-patient-list-row').hide();
    })

  }
  return init;
})();