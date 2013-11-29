<!doctype html>
<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en">
    <![endif]-->
    <!--[if IE 7]>
    <html class="no-js ie7 oldie" lang="en">
        <![endif]-->
        <!--[if IE 8]>
        <html class="no-js ie8 oldie" lang="en">
            <![endif]-->
            <!--[if gt IE 8]><!-->
            <html class="no-js" lang="en">
                <!--<![endif]-->
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                    <link rel="stylesheet" type="text/css" href="/css/style.css" />
                    <link rel="stylesheet" type="text/css" href="/assets/7eeba703/css/module.css" />
                    <link rel="stylesheet" type="text/css" href="/assets/e41dca1f/jui/css/base/jquery-ui.css" media="screen" />
                    <script type="text/javascript" src="/assets/e41dca1f/jquery.js"></script>
                    <script type="text/javascript" src="/assets/e41dca1f/jui/js/jquery-ui.min.js"></script>
                    <script type="text/javascript" src="/assets/7eeba703/js/TheatreDiaryController.js"></script>
                    <script type="text/javascript" src="/js/jquery.watermark.min.js"></script>
                    <script type="text/javascript" src="/js/mustache.js"></script>
                    <script type="text/javascript" src="/js/libs/uri-1.10.2.js"></script>
                    <script type="text/javascript" src="/js/waypoints.min.js"></script>
                    <script type="text/javascript" src="/js/waypoints-sticky.min.js"></script>
                    <script type="text/javascript" src="/js/libs/modernizr-2.0.6.min.js"></script>
                    <script type="text/javascript" src="/js/jquery.printElement.min.js"></script>
                    <script type="text/javascript" src="/js/jquery.hoverIntent.min.js"></script>
                    <script type="text/javascript" src="/js/print.js"></script>
                    <script type="text/javascript" src="/js/buttons.js"></script>
                    <script type="text/javascript" src="/js/util.js"></script>
                    <script type="text/javascript" src="/js/dialogs.js"></script>
                    <script type="text/javascript" src="/js/script.js"></script>
                    <script type="text/javascript">
                        /*<![CDATA[*/
                        NHSDateFormat = 'j M Y';
                        OE_asset_path = '/assets/7eeba703';
                        OE_subspecialty_id = '2';
                        YII_CSRF_TOKEN = '9cb390ef836625ffd1ece5bba3e426d1bad151f9';
                        /*]]>*/
                    </script>
                    <title>OpenEyes - TheatreDiary</title>
                    <meta name="viewport" content="width=device-width, initial-scale=0.62">
                    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
                    <link rel="shortcut icon" href="/favicon.ico"/>
                    <script type="text/javascript">
                        var baseUrl = '';
                    </script>
                </head>
                <body>
                    <div class="alert_banner_sticky_wrapper" style="height: 30px;">
                        <div id="alert_banner">
                            <div class="banner-watermark admin">You are logged in as admin. So this is OpenEyes Goldenrod Edition</div>
                        </div>
                    </div>
                    <!---
                        Server: precise64
                        Date: 19.08.2013 12:49:04
                        Commit: f0a948b4dc58e51ab193acee1f47eded5ca1ea10
                        User agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36
                        Client IP: 10.0.2.2
                        Username: admin (1)
                        Firm: Abou-Rayyah Yassir (134)
                        -->
                    <div id="container">
                        <div class="sticky-wrapper" style="height: 70px;">
                            <div id="header" class="clearfix">
                                <div id="brand">
                                    <h1><a>OpenEyes</a></h1>
                                </div>
                                <div id="user_panel">
                                    <div id="user_nav" class="clearfix">
                                        <ul>
                                            <li>
                                                <span><a href="/">Home</a></span>
                                            </li>
                                            <li>
                                                <span class="selected">Theatre Diaries</span>
                                            </li>
                                            <li>
                                                <span><a href="/OphTrOperationbooking/waitingList/index">Partial bookings waiting list</a></span>
                                            </li>
                                            <li>
                                                <span><a href="/site/logout">Logout</a></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="user_firm">
                                        <span>Site: </span>
                                        <strong>City Road</strong>,
                                        <span>Firm: </span>
                                        <strong>Abou-Rayyah Yassir (Adnexal)</strong>
                                        <span class="change-firm">(<a href="#">Change</a>)</span>
                                    </div>
                                    <div id="user_id">
                                        <span>You are logged in as:</span>
                                        <a class="profileLink" href="/profile"><strong>Enoch Root</strong></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- #header -->
                        <div id="content">
                            <h2>Theatre Schedules</h2>
                            <div class="fullWidth fullBox clearfix">
                                <div class="theatreDiary_sticky_wrapper" style="height: 30px;">
                                    <div id="whiteBox">
                                        <div style="float: right; margin-top: 4px; margin-right: 3px;">
                                            <button type="submit" class="classy mini diaryViewMode blue" id="btn_print_diary"><span class="button-span button-span-blue">Print</span></button>
                                            <button type="submit" class="classy mini diaryViewMode blue" id="btn_print_diary_list"><span class="button-span button-span-blue">Print list</span></button>
                                        </div>
                                        <p><strong>Use the filters below to view Theatre schedules:</strong></p>
                                    </div>
                                </div>
                                <div id="theatre_display">
                                    <form id="theatre-filter" action="/OphTrOperationbooking/theatre/search" method="post">
                                        <div style="display:none"><input type="hidden" value="9cb390ef836625ffd1ece5bba3e426d1bad151f9" name="YII_CSRF_TOKEN"></div>
                                        <div id="search-options">
                                            <div id="main-search" class="grid-view">
                                                <h3>Search schedules by:</h3>
                                                <table>
                                                    <tbody>
                                                        <tr>
                                                            <th>Site:</th>
                                                            <th>Theatre:</th>
                                                            <th>Subspecialty:</th>
                                                            <th>Firm:</th>
                                                            <th>Ward:</th>
                                                            <th>Emergency List:</th>
                                                            <th>Order by:</th>
                                                        </tr>
                                                        <tr class="even">
                                                            <td>
                                                                <select name="site-id" id="site-id">
                                                                    <option value="">All sites</option>
                                                                    <option value="2">Bedford</option>
                                                                    <option value="11">Boots</option>
                                                                    <option value="10">Bridge lane</option>
                                                                    <option value="1" selected="selected">City Road</option>
                                                                    <option value="17">Croydon</option>
                                                                    <option value="3">Ealing</option>
                                                                    <option value="19">Harlow</option>
                                                                    <option value="18">Homerton</option>
                                                                    <option value="12">Loxford</option>
                                                                    <option value="6">Mile End</option>
                                                                    <option value="4">Northwick Park</option>
                                                                    <option value="7">Potters Bar</option>
                                                                    <option value="8">Queen Mary's</option>
                                                                    <option value="9">St Ann's</option>
                                                                    <option value="5">St George's</option>
                                                                    <option value="14">Teddington</option>
                                                                    <option value="15">Upney lane</option>
                                                                    <option value="16">Visioncare</option>
                                                                    <option value="20">Watford</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="theatre-id" id="theatre-id">
                                                                    <option value="">All theatres</option>
                                                                    <option value="1">Theatre One</option>
                                                                    <option value="20">Refractive Laser 2</option>
                                                                    <option value="11">Refractive Laser 1</option>
                                                                    <option value="10">Emergency Theatre</option>
                                                                    <option value="9">Theatre 9</option>
                                                                    <option value="8">Theatre Eight</option>
                                                                    <option value="7">Theatre Seven</option>
                                                                    <option value="6">Theatre Six</option>
                                                                    <option value="5">Theatre Five</option>
                                                                    <option value="4">Theatre Four</option>
                                                                    <option value="3">Theatre Three</option>
                                                                    <option value="2">Theatre Two</option>
                                                                    <option value="21">City Road Ozurdex</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="subspecialty-id" id="subspecialty-id">
                                                                    <option value="">All specialties</option>
                                                                    <option value="1">Accident &amp; Emergency</option>
                                                                    <option value="2">Adnexal</option>
                                                                    <option value="3">Anaesthetics</option>
                                                                    <option value="4">Cataract</option>
                                                                    <option value="5">Cornea</option>
                                                                    <option value="6">External</option>
                                                                    <option value="7">Glaucoma</option>
                                                                    <option value="8">Medical Retinal</option>
                                                                    <option value="9">Neuro-ophthalmology</option>
                                                                    <option value="10">Oncology</option>
                                                                    <option value="11">Paediatrics</option>
                                                                    <option value="12">General Ophthalmology</option>
                                                                    <option value="13">Refractive</option>
                                                                    <option value="14">Strabismus</option>
                                                                    <option value="15">Uveitis</option>
                                                                    <option value="16">Vitreoretinal</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select disabled="disabled" name="firm-id" id="firm-id">
                                                                    <option value="">All firms</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="ward-id" id="ward-id">
                                                                    <option value="">All wards</option>
                                                                    <option value="3">Childrens</option>
                                                                    <option value="1">Mackellar</option>
                                                                    <option value="4">Minor Ops</option>
                                                                    <option value="5">Refractive laser</option>
                                                                    <option value="2">Sedgwick</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" value="1" name="emergency_list" id="emergency_list">
                                                            </td>
                                                            <td>
                                                                <select>
                                                                    <option selected>Theatre</option>
                                                                    <option>Date</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div id="extra-search" class="eventDetail clearfix">
                                                <div class="data">
                                                    <span class="group">
                                                    <input type="radio" name="date-filter" id="date-filter_0" value="today" checked="checked">
                                                    <label for="date-filter_0">Today</label>
                                                    </span>
                                                    <span class="group">
                                                    <input type="radio" name="date-filter" id="date-filter_1" value="week">
                                                    <label for="date-filter_1">Next 7 days</label>
                                                    </span>
                                                    <span class="group">
                                                    <input type="radio" name="date-filter" id="date-filter_2" value="month">
                                                    <label for="date-filter_2">Next 30 days</label>
                                                    </span>
                                                    <span class="group">
                                                    <input type="radio" name="date-filter" id="date-filter_3" value="custom">
                                                    <label for="date-filter_3">or select date range:</label>
                                                    <input style="width: 110px;" id="date-start" type="text" value="19 Aug 2013" name="date-start" class="hasDatepicker">						to
                                                    <input style="width: 110px;" id="date-end" type="text" value="19 Aug 2013" name="date-end" class="hasDatepicker">					</span>
                                                    <span class="group">
                                                    <a href="" id="last_week">Last week</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" id="next_week">Next week</a>
                                                    </span>
                                                </div>
                                                <div style="float:right;">
                                                    <span style="width: 30px;">
                                                    <img class="loader" src="/img/ajax-loader.gif" alt="loading..." style="display: none;">
                                                    </span>
                                                    &nbsp;&nbsp;
                                                    <button id="search_button" type="submit" class="classy tall green"><span class="button-span button-span-green">Search</span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div id="theatreList">
                                        <h3 class="theatre"><strong>Refractive Laser 2 (City Road)</strong></h3>
                                        <div class="infoBox diaryViewMode" id="infoBox_13520" style="display: none;">
                                            <strong>Session updated!</strong>
                                        </div>
                                        <form id="session_form13520" action="/OphTrOperationbooking/theatreDiary/saveSession" method="post">
                                            <div style="display:none"><input type="hidden" value="9cb390ef836625ffd1ece5bba3e426d1bad151f9" name="YII_CSRF_TOKEN"></div>
                                            <div class="action_options diaryViewMode" data-id="13520" style="float: right;">
                                                <img id="loader_13520" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;">
                                                <div class="session_options diaryViewMode" data-id="13520">
                                                    <span class="aBtn_inactive">View</span>
                                                    <span class="aBtn edit-event">
                                                    <a href="#" rel="13520" class="edit-session">Edit</a>
                                                    </span>
                                                </div>
                                                <div class="session_options diaryEditMode" data-id="13520" style="display: none;">
                                                    <span class="aBtn view-event">
                                                    <a href="#" rel="13520" class="view-session">View</a>
                                                    </span>
                                                    <span class="aBtn_inactive edit-event">Edit</span>
                                                </div>
                                            </div>
                                            <h3 class="sessionDetails">
                                                <span class="date">
                                                <strong>
                                                19 Aug			</strong>
                                                2013		</span>
                                                -
                                                <strong>
                                                <span class="day">
                                                Monday			</span>,
                                                <span class="time">
                                                14:30:00				-
                                                16:00:00			</span>
                                                </strong>
                                                for
                                                Stevens Julian		for (Refractive)
                                            </h3>
                                            <div class="theatre-sessions whiteBox clearfix">
                                                <div style="float: right;">
                                                    <input type="hidden" id="consultant_13520" name="consultant_13520" value="1">
                                                    <input type="hidden" id="paediatric_13520" name="paediatric_13520" value="0">
                                                    <input type="hidden" id="anaesthetist_13520" name="anaesthetist_13520" value="0">
                                                    <input type="hidden" id="general_anaesthetic_13520" name="general_anaesthetic_13520" value="0">
                                                    <input type="hidden" id="available_13520" name="available_13520" value="1">
                                                    <div class="sessionComments" style="display:block; width:205px;">
                                                        <h4>Session Comments</h4>
                                                        <textarea style="display: none;" rows="2" name="comments_13520" class="comments diaryEditMode" data-id="13520"></textarea>
                                                        <div class="comments_ro diaryViewMode" data-id="13520" title="Modified on 23 Aug 2012 at :51:3 by Enoch Root"></div>
                                                    </div>
                                                </div>
                                                <table class="theatre_list">
                                                    <thead id="thead_13520">
                                                        <tr>
                                                            <th>Admit time</th>
                                                            <th class="th_sort diaryEditMode" data-id="13520" style="display: none;">Sort</th>
                                                            <th>Hospital #</th>
                                                            <th>Confirmed</th>
                                                            <th>Patient (Age)</th>
                                                            <th>[Eye] Operation</th>
                                                            <th>Priority</th>
                                                            <th>Anesth</th>
                                                            <th>Ward</th>
                                                            <th>Info</th>
                                                        </tr>
                                                    </thead>
                                                     <tbody id="tbody_14045">
                                                        <tr id="oprow_53716">
                                                            <td class="session">
                                                                <input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_53716" data-id="14045" data-operation-id="53716" value="07:30" size="4">
                                                                <span class="admitTime_ro diaryViewMode" data-id="14045" data-operation-id="53716">07:30</span>
                                                            </td>
                                                            <td class="td_sort diaryEditMode" data-id="14045" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28">
                                                            </td>
                                                            <td class="hospital"><a href="/OphTrOperationbooking/default/view/2367696">1718129</a></td>
                                                            <td class="confirm"><input type="hidden" name="confirm_53716" value="0"><input id="confirm_53716" type="checkbox" value="1" name="confirm_53716" disabled="disabled"></td>
                                                            <td class="patient leftAlign">CULLINANE, John (49)</td>
                                                            <td class="operation leftAlign">[Right] Phakoemulsification and IOL</td>
                                                            <td class="">Routine</td>
                                                            <td class="anesthetic">LAC</td>
                                                            <td class="ward">Mackellar</td>
                                                            <td class="alerts">
                                                                <img src="/assets/24e0c351/img/diaryIcons/male.png" alt="male" title="male" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/comment.png" alt="2nd eye Posterior subcapsular cataract (steroid-induced)" title="2nd eye Posterior subcapsular cataract (steroid-induced)" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/booked_user.png" alt="Created by: Marta Ugarte
                                                                    Last modified by: Sherry Ramos" title="Created by: Marta Ugarte
                                                                    Last modified by: Sherry Ramos" width="17" height="17">
                                                            </td>
                                                        </tr>
                                                        <tr id="oprow_54507">
                                                            <td class="session">
                                                                <input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_54507" data-id="14045" data-operation-id="54507" value="07:30" size="4">
                                                                <span class="admitTime_ro diaryViewMode" data-id="14045" data-operation-id="54507">07:30</span>
                                                            </td>
                                                            <td class="td_sort diaryEditMode" data-id="14045" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28">
                                                            </td>
                                                            <td class="hospital"><a href="/OphTrOperationbooking/default/view/2377689">1626235</a></td>
                                                            <td class="confirm"><input type="hidden" name="confirm_54507" value="0"><input id="confirm_54507" type="checkbox" value="1" name="confirm_54507" disabled="disabled"></td>
                                                            <td class="patient leftAlign">PREEDY, Iris (86)</td>
                                                            <td class="operation leftAlign">[Right] Phakoemulsification and IOL</td>
                                                            <td class="">Routine</td>
                                                            <td class="anesthetic">LA</td>
                                                            <td class="ward">Sedgwick</td>
                                                            <td class="alerts">
                                                                <img src="/assets/24e0c351/img/diaryIcons/female.png" alt="female" title="female" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/booked_user.png" alt="Created by: Moloy Dey
                                                                    Last modified by: Sherry Ramos" title="Created by: Moloy Dey
                                                                    Last modified by: Sherry Ramos" width="17" height="17">
                                                            </td>
                                                        </tr>
                                                        <tr id="oprow_56127">
                                                            <td class="session">
                                                                <input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_56127" data-id="14045" data-operation-id="56127" value="07:30" size="4">
                                                                <span class="admitTime_ro diaryViewMode" data-id="14045" data-operation-id="56127">07:30</span>
                                                            </td>
                                                            <td class="td_sort diaryEditMode" data-id="14045" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28">
                                                            </td>
                                                            <td class="hospital"><a href="/OphTrOperationbooking/default/view/2394831">1817519</a></td>
                                                            <td class="confirm"><input type="hidden" name="confirm_56127" value="0"><input id="confirm_56127" type="checkbox" value="1" name="confirm_56127" disabled="disabled"></td>
                                                            <td class="patient leftAlign">SHARPLES, Jamie (39)</td>
                                                            <td class="operation leftAlign">[Left] Phakoemulsification and IOL</td>
                                                            <td class="">Routine</td>
                                                            <td class="anesthetic">LA</td>
                                                            <td class="ward">Mackellar</td>
                                                            <td class="alerts">
                                                                <img src="/assets/24e0c351/img/diaryIcons/male.png" alt="male" title="male" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/booked_user.png" alt="Created by: Periyasamy Kumar
                                                                    Last modified by: Periyasamy Kumar" title="Created by: Periyasamy Kumar
                                                                    Last modified by: Periyasamy Kumar" width="17" height="17">
                                                            </td>
                                                        </tr>
                                                        <tr id="oprow_56549">
                                                            <td class="session">
                                                                <input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_56549" data-id="14045" data-operation-id="56549" value="07:30" size="4">
                                                                <span class="admitTime_ro diaryViewMode" data-id="14045" data-operation-id="56549">07:30</span>
                                                            </td>
                                                            <td class="td_sort diaryEditMode" data-id="14045" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28">
                                                            </td>
                                                            <td class="hospital"><a href="/OphTrOperationbooking/default/view/2399169">1660929</a></td>
                                                            <td class="confirm"><input type="hidden" name="confirm_56549" value="0"><input id="confirm_56549" type="checkbox" value="1" name="confirm_56549" disabled="disabled"></td>
                                                            <td class="patient leftAlign">RAJENDRAN, Kandasamy (46)</td>
                                                            <td class="operation leftAlign">[Left] Phakoemulsification and IOL</td>
                                                            <td class="">Routine</td>
                                                            <td class="anesthetic">LA</td>
                                                            <td class="ward">Mackellar</td>
                                                            <td class="alerts">
                                                                <img src="/assets/24e0c351/img/diaryIcons/male.png" alt="male" title="male" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/comment.png" alt="Quiescent treated PDR" title="Quiescent treated PDR" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/booked_user.png" alt="Created by: Aneel Suri
                                                                    Last modified by: Sherry Ramos" title="Created by: Aneel Suri
                                                                    Last modified by: Sherry Ramos" width="17" height="17">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="9" class="footer available clearfix">
                                                                <div class="session_timeleft">
                                                                    90 minutes unallocated
                                                                    <span style="display: none;" class="session_unavailable" id="session_unavailable_13520"> - session unavailable</span>
                                                                </div>
                                                                <div class="specialists">
                                                                    <div id="consultant_icon_13520" class="consultant" title="Consultant Present">Consultant</div>
                                                                    <div style="display: none;" id="anaesthetist_icon_13520" class="anaesthetist" title="Anaesthetist Present">Anaesthetist</div>
                                                                    <div style="display: none;" id="paediatric_icon_13520" class="paediatric" title="Paediatric Session">Paediatric</div>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <div style="display: none;" data-id="13520" class="classy_buttons diaryEditMode">
                                                    <img id="loader2_13520" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 2px; display: none">
                                                    <button type="submit" class="classy green mini" id="btn_edit_session_save_13520"><span class="button-span button-span-green">Save changes to session</span></button>
                                                    <button type="submit" class="classy red mini" id="btn_edit_session_cancel_13520"><span class="button-span button-span-red">Cancel</span></button>
                                                </div>
                                            </div>
                                        </form>
                                        <h3 class="theatre"><strong>Theatre One (City Road)</strong></h3>
                                        <div class="infoBox diaryViewMode" id="infoBox_11688" style="display: none;">
                                            <strong>Session updated!</strong>
                                        </div>
                                        <form id="session_form11688" action="/OphTrOperationbooking/theatreDiary/saveSession" method="post">
                                            <div style="display:none"><input type="hidden" value="9cb390ef836625ffd1ece5bba3e426d1bad151f9" name="YII_CSRF_TOKEN"></div>
                                            <div class="action_options diaryViewMode" data-id="11688" style="float: right;">
                                                <img id="loader_11688" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;">
                                                <div class="session_options diaryViewMode" data-id="11688">
                                                    <span class="aBtn_inactive">View</span>
                                                    <span class="aBtn edit-event">
                                                    <a href="#" rel="11688" class="edit-session">Edit</a>
                                                    </span>
                                                </div>
                                                <div class="session_options diaryEditMode" data-id="11688" style="display: none;">
                                                    <span class="aBtn view-event">
                                                    <a href="#" rel="11688" class="view-session">View</a>
                                                    </span>
                                                    <span class="aBtn_inactive edit-event">Edit</span>
                                                </div>
                                            </div>
                                            <h3 class="sessionDetails">
                                                <span class="date">
                                                <strong>
                                                19 Aug			</strong>
                                                2013		</span>
                                                -
                                                <strong>
                                                <span class="day">
                                                Monday			</span>,
                                                <span class="time">
                                                08:30:00				-
                                                13:00:00			</span>
                                                </strong>
                                                for
                                                Miller Michael		for (Cataract)
                                            </h3>
                                            <div class="theatre-sessions whiteBox clearfix">
                                                <div style="float: right;">
                                                    <input type="hidden" id="consultant_11688" name="consultant_11688" value="0">
                                                    <input type="hidden" id="paediatric_11688" name="paediatric_11688" value="0">
                                                    <input type="hidden" id="anaesthetist_11688" name="anaesthetist_11688" value="1">
                                                    <input type="hidden" id="general_anaesthetic_11688" name="general_anaesthetic_11688" value="1">
                                                    <input type="hidden" id="available_11688" name="available_11688" value="1">
                                                    <div class="sessionComments" style="display:block; width:205px;">
                                                        <h4>Session Comments</h4>
                                                        <textarea style="display: none;" rows="2" name="comments_11688" class="comments diaryEditMode" data-id="11688"></textarea>
                                                        <div class="comments_ro diaryViewMode" data-id="11688" title="Modified on 23 Aug 2012 at :51:2 by Enoch Root"></div>
                                                    </div>
                                                </div>
                                                <table class="theatre_list">
                                                    <thead id="thead_11688">
                                                        <tr>
                                                            <th>Admit time</th>
                                                            <th class="th_sort diaryEditMode" data-id="11688" style="display: none;">Sort</th>
                                                            <th>Hospital #</th>
                                                            <th>Confirmed</th>
                                                            <th>Patient (Age)</th>
                                                            <th>[Eye] Operation</th>
                                                            <th>Priority</th>
                                                            <th>Anesth</th>
                                                            <th>Ward</th>
                                                            <th>Info</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody_11688">
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="9" class="footer available clearfix">
                                                                <div class="session_timeleft">
                                                                    270 minutes unallocated
                                                                    <span style="display: none;" class="session_unavailable" id="session_unavailable_11688"> - session unavailable</span>
                                                                </div>
                                                                <div class="specialists">
                                                                    <div style="display: none;" id="consultant_icon_11688" class="consultant" title="Consultant Present">Consultant</div>
                                                                    <div id="anaesthetist_icon_11688" class="anaesthetist" title="Anaesthetist Present">Anaesthetist (GA)</div>
                                                                    <div style="display: none;" id="paediatric_icon_11688" class="paediatric" title="Paediatric Session">Paediatric</div>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <div style="display: none;" data-id="11688" class="classy_buttons diaryEditMode">
                                                    <img id="loader2_11688" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 2px; display: none">
                                                    <button type="submit" class="classy green mini" id="btn_edit_session_save_11688"><span class="button-span button-span-green">Save changes to session</span></button>
                                                    <button type="submit" class="classy red mini" id="btn_edit_session_cancel_11688"><span class="button-span button-span-red">Cancel</span></button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="infoBox diaryViewMode" id="infoBox_11500" style="display: none;">
                                            <strong>Session updated!</strong>
                                        </div>
                                        <form id="session_form11500" action="/OphTrOperationbooking/theatreDiary/saveSession" method="post">
                                            <div style="display:none"><input type="hidden" value="9cb390ef836625ffd1ece5bba3e426d1bad151f9" name="YII_CSRF_TOKEN"></div>
                                            <div class="action_options diaryViewMode" data-id="11500" style="float: right;">
                                                <img id="loader_11500" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;">
                                                <div class="session_options diaryViewMode" data-id="11500">
                                                    <span class="aBtn_inactive">View</span>
                                                    <span class="aBtn edit-event">
                                                    <a href="#" rel="11500" class="edit-session">Edit</a>
                                                    </span>
                                                </div>
                                                <div class="session_options diaryEditMode" data-id="11500" style="display: none;">
                                                    <span class="aBtn view-event">
                                                    <a href="#" rel="11500" class="view-session">View</a>
                                                    </span>
                                                    <span class="aBtn_inactive edit-event">Edit</span>
                                                </div>
                                            </div>
                                            <h3 class="sessionDetails">
                                                <span class="date">
                                                <strong>
                                                19 Aug			</strong>
                                                2013		</span>
                                                -
                                                <strong>
                                                <span class="day">
                                                Monday			</span>,
                                                <span class="time">
                                                13:30:00				-
                                                18:00:00			</span>
                                                </strong>
                                                for
                                                Ficker Linda		for (External)
                                            </h3>
                                            <div class="theatre-sessions whiteBox clearfix">
                                                <div style="float: right;">
                                                    <input type="hidden" id="consultant_11500" name="consultant_11500" value="1">
                                                    <input type="hidden" id="paediatric_11500" name="paediatric_11500" value="0">
                                                    <input type="hidden" id="anaesthetist_11500" name="anaesthetist_11500" value="1">
                                                    <input type="hidden" id="general_anaesthetic_11500" name="general_anaesthetic_11500" value="1">
                                                    <input type="hidden" id="available_11500" name="available_11500" value="1">
                                                    <div class="sessionComments" style="display:block; width:205px;">
                                                        <h4>Session Comments</h4>
                                                        <textarea style="display: none;" rows="2" name="comments_11500" class="comments diaryEditMode" data-id="11500"></textarea>
                                                        <div class="comments_ro diaryViewMode" data-id="11500" title="Modified on 23 Aug 2012 at :51:2 by Enoch Root"></div>
                                                    </div>
                                                </div>
                                                <table class="theatre_list">
                                                    <thead id="thead_11500">
                                                        <tr>
                                                            <th>Admit time</th>
                                                            <th class="th_sort diaryEditMode" data-id="11500" style="display: none;">Sort</th>
                                                            <th>Hospital #</th>
                                                            <th>Confirmed</th>
                                                            <th>Patient (Age)</th>
                                                            <th>[Eye] Operation</th>
                                                            <th>Priority</th>
                                                            <th>Anesth</th>
                                                            <th>Ward</th>
                                                            <th>Info</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody_11500">
                                                     <tbody id="tbody_14045">
                                                        <tr id="oprow_53716">
                                                            <td class="session">
                                                                <input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_53716" data-id="14045" data-operation-id="53716" value="07:30" size="4">
                                                                <span class="admitTime_ro diaryViewMode" data-id="14045" data-operation-id="53716">07:30</span>
                                                            </td>
                                                            <td class="td_sort diaryEditMode" data-id="14045" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28">
                                                            </td>
                                                            <td class="hospital"><a href="/OphTrOperationbooking/default/view/2367696">1718129</a></td>
                                                            <td class="confirm"><input type="hidden" name="confirm_53716" value="0"><input id="confirm_53716" type="checkbox" value="1" name="confirm_53716" disabled="disabled"></td>
                                                            <td class="patient leftAlign">CULLINANE, John (49)</td>
                                                            <td class="operation leftAlign">[Right] Phakoemulsification and IOL</td>
                                                            <td class="">Routine</td>
                                                            <td class="anesthetic">LAC</td>
                                                            <td class="ward">Mackellar</td>
                                                            <td class="alerts">
                                                                <img src="/assets/24e0c351/img/diaryIcons/male.png" alt="male" title="male" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/comment.png" alt="2nd eye Posterior subcapsular cataract (steroid-induced)" title="2nd eye Posterior subcapsular cataract (steroid-induced)" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/booked_user.png" alt="Created by: Marta Ugarte
                                                                    Last modified by: Sherry Ramos" title="Created by: Marta Ugarte
                                                                    Last modified by: Sherry Ramos" width="17" height="17">
                                                            </td>
                                                        </tr>
                                                        <tr id="oprow_54507">
                                                            <td class="session">
                                                                <input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_54507" data-id="14045" data-operation-id="54507" value="07:30" size="4">
                                                                <span class="admitTime_ro diaryViewMode" data-id="14045" data-operation-id="54507">07:30</span>
                                                            </td>
                                                            <td class="td_sort diaryEditMode" data-id="14045" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28">
                                                            </td>
                                                            <td class="hospital"><a href="/OphTrOperationbooking/default/view/2377689">1626235</a></td>
                                                            <td class="confirm"><input type="hidden" name="confirm_54507" value="0"><input id="confirm_54507" type="checkbox" value="1" name="confirm_54507" disabled="disabled"></td>
                                                            <td class="patient leftAlign">PREEDY, Iris (86)</td>
                                                            <td class="operation leftAlign">[Right] Phakoemulsification and IOL</td>
                                                            <td class="">Routine</td>
                                                            <td class="anesthetic">LA</td>
                                                            <td class="ward">Sedgwick</td>
                                                            <td class="alerts">
                                                                <img src="/assets/24e0c351/img/diaryIcons/female.png" alt="female" title="female" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/booked_user.png" alt="Created by: Moloy Dey
                                                                    Last modified by: Sherry Ramos" title="Created by: Moloy Dey
                                                                    Last modified by: Sherry Ramos" width="17" height="17">
                                                            </td>
                                                        </tr>
                                                        <tr id="oprow_56127">
                                                            <td class="session">
                                                                <input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_56127" data-id="14045" data-operation-id="56127" value="07:30" size="4">
                                                                <span class="admitTime_ro diaryViewMode" data-id="14045" data-operation-id="56127">07:30</span>
                                                            </td>
                                                            <td class="td_sort diaryEditMode" data-id="14045" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28">
                                                            </td>
                                                            <td class="hospital"><a href="/OphTrOperationbooking/default/view/2394831">1817519</a></td>
                                                            <td class="confirm"><input type="hidden" name="confirm_56127" value="0"><input id="confirm_56127" type="checkbox" value="1" name="confirm_56127" disabled="disabled"></td>
                                                            <td class="patient leftAlign">SHARPLES, Jamie (39)</td>
                                                            <td class="operation leftAlign">[Left] Phakoemulsification and IOL</td>
                                                            <td class="">Routine</td>
                                                            <td class="anesthetic">LA</td>
                                                            <td class="ward">Mackellar</td>
                                                            <td class="alerts">
                                                                <img src="/assets/24e0c351/img/diaryIcons/male.png" alt="male" title="male" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/booked_user.png" alt="Created by: Periyasamy Kumar
                                                                    Last modified by: Periyasamy Kumar" title="Created by: Periyasamy Kumar
                                                                    Last modified by: Periyasamy Kumar" width="17" height="17">
                                                            </td>
                                                        </tr>
                                                        <tr id="oprow_56549">
                                                            <td class="session">
                                                                <input style="display: none;" type="text" class="admitTime diaryEditMode" name="admitTime_56549" data-id="14045" data-operation-id="56549" value="07:30" size="4">
                                                                <span class="admitTime_ro diaryViewMode" data-id="14045" data-operation-id="56549">07:30</span>
                                                            </td>
                                                            <td class="td_sort diaryEditMode" data-id="14045" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/draggable_row.png" alt="draggable_row" width="25" height="28">
                                                            </td>
                                                            <td class="hospital"><a href="/OphTrOperationbooking/default/view/2399169">1660929</a></td>
                                                            <td class="confirm"><input type="hidden" name="confirm_56549" value="0"><input id="confirm_56549" type="checkbox" value="1" name="confirm_56549" disabled="disabled"></td>
                                                            <td class="patient leftAlign">RAJENDRAN, Kandasamy (46)</td>
                                                            <td class="operation leftAlign">[Left] Phakoemulsification and IOL</td>
                                                            <td class="">Routine</td>
                                                            <td class="anesthetic">LA</td>
                                                            <td class="ward">Mackellar</td>
                                                            <td class="alerts">
                                                                <img src="/assets/24e0c351/img/diaryIcons/male.png" alt="male" title="male" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/confirmed.png" alt="confirmed" width="17" height="17" class="confirmed" title="confirmed" style="display: none;">
                                                                <img src="/assets/24e0c351/img/diaryIcons/comment.png" alt="Quiescent treated PDR" title="Quiescent treated PDR" width="17" height="17">
                                                                <img src="/assets/24e0c351/img/diaryIcons/booked_user.png" alt="Created by: Aneel Suri
                                                                    Last modified by: Sherry Ramos" title="Created by: Aneel Suri
                                                                    Last modified by: Sherry Ramos" width="17" height="17">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="9" class="footer available clearfix">
                                                                <div class="session_timeleft">
                                                                    270 minutes unallocated
                                                                    <span style="display: none;" class="session_unavailable" id="session_unavailable_11500"> - session unavailable</span>
                                                                </div>
                                                                <div class="specialists">
                                                                    <div id="consultant_icon_11500" class="consultant" title="Consultant Present">Consultant</div>
                                                                    <div id="anaesthetist_icon_11500" class="anaesthetist" title="Anaesthetist Present">Anaesthetist (GA)</div>
                                                                    <div style="display: none;" id="paediatric_icon_11500" class="paediatric" title="Paediatric Session">Paediatric</div>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <div style="display: none;" data-id="11500" class="classy_buttons diaryEditMode">
                                                    <img id="loader2_11500" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 2px; display: none">
                                                    <button type="submit" class="classy green mini" id="btn_edit_session_save_11500"><span class="button-span button-span-green">Save changes to session</span></button>
                                                    <button type="submit" class="classy red mini" id="btn_edit_session_cancel_11500"><span class="button-span button-span-red">Cancel</span></button>
                                                </div>
                                            </div>
                                        </form>
                                        <h3 class="theatre"><strong>Refractive Laser 1 (City Road)</strong></h3>
                                        <div class="infoBox diaryViewMode" id="infoBox_13481" style="display: none;">
                                            <strong>Session updated!</strong>
                                        </div>
                                        <form id="session_form13481" action="/OphTrOperationbooking/theatreDiary/saveSession" method="post">
                                            <div style="display:none"><input type="hidden" value="9cb390ef836625ffd1ece5bba3e426d1bad151f9" name="YII_CSRF_TOKEN"></div>
                                            <div class="action_options diaryViewMode" data-id="13481" style="float: right;">
                                                <img id="loader_13481" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;">
                                                <div class="session_options diaryViewMode" data-id="13481">
                                                    <span class="aBtn_inactive">View</span>
                                                    <span class="aBtn edit-event">
                                                    <a href="#" rel="13481" class="edit-session">Edit</a>
                                                    </span>
                                                </div>
                                                <div class="session_options diaryEditMode" data-id="13481" style="display: none;">
                                                    <span class="aBtn view-event">
                                                    <a href="#" rel="13481" class="view-session">View</a>
                                                    </span>
                                                    <span class="aBtn_inactive edit-event">Edit</span>
                                                </div>
                                            </div>
                                            <h3 class="sessionDetails">
                                                <span class="date">
                                                <strong>
                                                19 Aug			</strong>
                                                2013		</span>
                                                -
                                                <strong>
                                                <span class="day">
                                                Monday			</span>,
                                                <span class="time">
                                                14:30:00				-
                                                16:00:00			</span>
                                                </strong>
                                                for
                                                Allan Bruce		for (Refractive)
                                            </h3>
                                            <div class="theatre-sessions whiteBox clearfix">
                                                <div style="float: right;">
                                                    <input type="hidden" id="consultant_13481" name="consultant_13481" value="1">
                                                    <input type="hidden" id="paediatric_13481" name="paediatric_13481" value="0">
                                                    <input type="hidden" id="anaesthetist_13481" name="anaesthetist_13481" value="0">
                                                    <input type="hidden" id="general_anaesthetic_13481" name="general_anaesthetic_13481" value="0">
                                                    <input type="hidden" id="available_13481" name="available_13481" value="1">
                                                    <div class="sessionComments" style="display:block; width:205px;">
                                                        <h4>Session Comments</h4>
                                                        <textarea style="display: none;" rows="2" name="comments_13481" class="comments diaryEditMode" data-id="13481"></textarea>
                                                        <div class="comments_ro diaryViewMode" data-id="13481" title="Modified on 23 Aug 2012 at :51:3 by Enoch Root"></div>
                                                    </div>
                                                </div>
                                                <table class="theatre_list">
                                                    <thead id="thead_13481">
                                                        <tr>
                                                            <th>Admit time</th>
                                                            <th class="th_sort diaryEditMode" data-id="13481" style="display: none;">Sort</th>
                                                            <th>Hospital #</th>
                                                            <th>Confirmed</th>
                                                            <th>Patient (Age)</th>
                                                            <th>[Eye] Operation</th>
                                                            <th>Priority</th>
                                                            <th>Anesth</th>
                                                            <th>Ward</th>
                                                            <th>Info</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody_13481">
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="9" class="footer available clearfix">
                                                                <div class="session_timeleft">
                                                                    90 minutes unallocated
                                                                    <span style="display: none;" class="session_unavailable" id="session_unavailable_13481"> - session unavailable</span>
                                                                </div>
                                                                <div class="specialists">
                                                                    <div id="consultant_icon_13481" class="consultant" title="Consultant Present">Consultant</div>
                                                                    <div style="display: none;" id="anaesthetist_icon_13481" class="anaesthetist" title="Anaesthetist Present">Anaesthetist</div>
                                                                    <div style="display: none;" id="paediatric_icon_13481" class="paediatric" title="Paediatric Session">Paediatric</div>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <div style="display: none;" data-id="13481" class="classy_buttons diaryEditMode">
                                                    <img id="loader2_13481" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 2px; display: none">
                                                    <button type="submit" class="classy green mini" id="btn_edit_session_save_13481"><span class="button-span button-span-green">Save changes to session</span></button>
                                                    <button type="submit" class="classy red mini" id="btn_edit_session_cancel_13481"><span class="button-span button-span-red">Cancel</span></button>
                                                </div>
                                            </div>
                                        </form>
                                        <h3 class="theatre"><strong>Theatre Two (City Road)</strong></h3>
                                        <div class="infoBox diaryViewMode" id="infoBox_12911" style="display: none;">
                                            <strong>Session updated!</strong>
                                        </div>
                                        <form id="session_form12911" action="/OphTrOperationbooking/theatreDiary/saveSession" method="post">
                                            <div style="display:none"><input type="hidden" value="9cb390ef836625ffd1ece5bba3e426d1bad151f9" name="YII_CSRF_TOKEN"></div>
                                            <div class="action_options diaryViewMode" data-id="12911" style="float: right;">
                                                <img id="loader_12911" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;">
                                                <div class="session_options diaryViewMode" data-id="12911">
                                                    <span class="aBtn_inactive">View</span>
                                                    <span class="aBtn edit-event">
                                                    <a href="#" rel="12911" class="edit-session">Edit</a>
                                                    </span>
                                                </div>
                                                <div class="session_options diaryEditMode" data-id="12911" style="display: none;">
                                                    <span class="aBtn view-event">
                                                    <a href="#" rel="12911" class="view-session">View</a>
                                                    </span>
                                                    <span class="aBtn_inactive edit-event">Edit</span>
                                                </div>
                                            </div>
                                            <h3 class="sessionDetails">
                                                <span class="date">
                                                <strong>
                                                19 Aug			</strong>
                                                2013		</span>
                                                -
                                                <strong>
                                                <span class="day">
                                                Monday			</span>,
                                                <span class="time">
                                                08:30:00				-
                                                13:00:00			</span>
                                                </strong>
                                                for
                                                Maurino Vincenzo		for (Cataract)
                                            </h3>
                                            <div class="theatre-sessions whiteBox clearfix">
                                                <div style="float: right;">
                                                    <input type="hidden" id="consultant_12911" name="consultant_12911" value="1">
                                                    <input type="hidden" id="paediatric_12911" name="paediatric_12911" value="0">
                                                    <input type="hidden" id="anaesthetist_12911" name="anaesthetist_12911" value="1">
                                                    <input type="hidden" id="general_anaesthetic_12911" name="general_anaesthetic_12911" value="1">
                                                    <input type="hidden" id="available_12911" name="available_12911" value="1">
                                                    <div class="sessionComments" style="display:block; width:205px;">
                                                        <h4>Session Comments</h4>
                                                        <textarea style="display: none;" rows="2" name="comments_12911" class="comments diaryEditMode" data-id="12911"></textarea>
                                                        <div class="comments_ro diaryViewMode" data-id="12911" title="Modified on 23 Aug 2012 at :51:3 by Enoch Root"></div>
                                                    </div>
                                                </div>
                                                <table class="theatre_list">
                                                    <thead id="thead_12911">
                                                        <tr>
                                                            <th>Admit time</th>
                                                            <th class="th_sort diaryEditMode" data-id="12911" style="display: none;">Sort</th>
                                                            <th>Hospital #</th>
                                                            <th>Confirmed</th>
                                                            <th>Patient (Age)</th>
                                                            <th>[Eye] Operation</th>
                                                            <th>Priority</th>
                                                            <th>Anesth</th>
                                                            <th>Ward</th>
                                                            <th>Info</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody_12911">
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="9" class="footer available clearfix">
                                                                <div class="session_timeleft">
                                                                    270 minutes unallocated
                                                                    <span style="display: none;" class="session_unavailable" id="session_unavailable_12911"> - session unavailable</span>
                                                                </div>
                                                                <div class="specialists">
                                                                    <div id="consultant_icon_12911" class="consultant" title="Consultant Present">Consultant</div>
                                                                    <div id="anaesthetist_icon_12911" class="anaesthetist" title="Anaesthetist Present">Anaesthetist (GA)</div>
                                                                    <div style="display: none;" id="paediatric_icon_12911" class="paediatric" title="Paediatric Session">Paediatric</div>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <div style="display: none;" data-id="12911" class="classy_buttons diaryEditMode">
                                                    <img id="loader2_12911" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 2px; display: none">
                                                    <button type="submit" class="classy green mini" id="btn_edit_session_save_12911"><span class="button-span button-span-green">Save changes to session</span></button>
                                                    <button type="submit" class="classy red mini" id="btn_edit_session_cancel_12911"><span class="button-span button-span-red">Cancel</span></button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="infoBox diaryViewMode" id="infoBox_11343" style="display: none;">
                                            <strong>Session updated!</strong>
                                        </div>
                                        <form id="session_form11343" action="/OphTrOperationbooking/theatreDiary/saveSession" method="post">
                                            <div style="display:none"><input type="hidden" value="9cb390ef836625ffd1ece5bba3e426d1bad151f9" name="YII_CSRF_TOKEN"></div>
                                            <div class="action_options diaryViewMode" data-id="11343" style="float: right;">
                                                <img id="loader_11343" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;">
                                                <div class="session_options diaryViewMode" data-id="11343">
                                                    <span class="aBtn_inactive">View</span>
                                                    <span class="aBtn edit-event">
                                                    <a href="#" rel="11343" class="edit-session">Edit</a>
                                                    </span>
                                                </div>
                                                <div class="session_options diaryEditMode" data-id="11343" style="display: none;">
                                                    <span class="aBtn view-event">
                                                    <a href="#" rel="11343" class="view-session">View</a>
                                                    </span>
                                                    <span class="aBtn_inactive edit-event">Edit</span>
                                                </div>
                                            </div>
                                            <h3 class="sessionDetails">
                                                <span class="date">
                                                <strong>
                                                19 Aug			</strong>
                                                2013		</span>
                                                -
                                                <strong>
                                                <span class="day">
                                                Monday			</span>,
                                                <span class="time">
                                                13:30:00				-
                                                18:00:00			</span>
                                                </strong>
                                                for
                                                Uddin Jimmy		for (Adnexal)
                                            </h3>
                                            <div class="theatre-sessions whiteBox clearfix">
                                                <div style="float: right;">
                                                    <input type="hidden" id="consultant_11343" name="consultant_11343" value="1">
                                                    <input type="hidden" id="paediatric_11343" name="paediatric_11343" value="0">
                                                    <input type="hidden" id="anaesthetist_11343" name="anaesthetist_11343" value="1">
                                                    <input type="hidden" id="general_anaesthetic_11343" name="general_anaesthetic_11343" value="1">
                                                    <input type="hidden" id="available_11343" name="available_11343" value="1">
                                                    <div class="sessionComments" style="display:block; width:205px;">
                                                        <h4>Session Comments</h4>
                                                        <textarea style="display: none;" rows="2" name="comments_11343" class="comments diaryEditMode" data-id="11343"></textarea>
                                                        <div class="comments_ro diaryViewMode" data-id="11343" title="Modified on 23 Aug 2012 at :51:2 by Enoch Root"></div>
                                                    </div>
                                                </div>
                                                <table class="theatre_list">
                                                    <thead id="thead_11343">
                                                        <tr>
                                                            <th>Admit time</th>
                                                            <th class="th_sort diaryEditMode" data-id="11343" style="display: none;">Sort</th>
                                                            <th>Hospital #</th>
                                                            <th>Confirmed</th>
                                                            <th>Patient (Age)</th>
                                                            <th>[Eye] Operation</th>
                                                            <th>Priority</th>
                                                            <th>Anesth</th>
                                                            <th>Ward</th>
                                                            <th>Info</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody_11343">
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="9" class="footer available clearfix">
                                                                <div class="session_timeleft">
                                                                    270 minutes unallocated
                                                                    <span style="display: none;" class="session_unavailable" id="session_unavailable_11343"> - session unavailable</span>
                                                                </div>
                                                                <div class="specialists">
                                                                    <div id="consultant_icon_11343" class="consultant" title="Consultant Present">Consultant</div>
                                                                    <div id="anaesthetist_icon_11343" class="anaesthetist" title="Anaesthetist Present">Anaesthetist (GA)</div>
                                                                    <div style="display: none;" id="paediatric_icon_11343" class="paediatric" title="Paediatric Session">Paediatric</div>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <div style="display: none;" data-id="11343" class="classy_buttons diaryEditMode">
                                                    <img id="loader2_11343" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 2px; display: none">
                                                    <button type="submit" class="classy green mini" id="btn_edit_session_save_11343"><span class="button-span button-span-green">Save changes to session</span></button>
                                                    <button type="submit" class="classy red mini" id="btn_edit_session_cancel_11343"><span class="button-span button-span-red">Cancel</span></button>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                    <div class="printable" id="printable"></div>
                                </div>
                            </div>
                            <div id="iframeprintholder" style="display: none;"></div>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                	// return getDiary();
                                });
                            </script>
                        </div>
                        <!-- #content -->
                        <div id="help" class="clearfix">
                        </div>
                        <!-- #help -->
                    </div>
                    <!--#container -->
                    <div id="footer">
                        <h6>© Copyright OpenEyes Foundation 2011–2013&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="/site/debuginfo" id="support-info-link">served, with love, by precise64</a>
                        </h6>
                        <div class="help">
                            <span><strong>Need Help?</strong></span>
                            <span class="divider">|</span>
                            <span>helpdesk@example.com</span>
                            <span class="divider">|</span>
                            <span><strong>12345678</strong></span>
                            <span class="divider">|</span>
                            <span><a target="_new" href="http://mehhome/about/trust-wide-projects/openeyes/training-arrangements/">Help Documentation</a></span>
                        </div>
                    </div>
                    <!-- #footer -->
                    <script type="text/javascript">
                        $(document).ready(function() {
                        	$('#support-info-link').live('click',function() {

                        		new OpenEyes.UI.Dialog({
                        			url: this.href,
                        			title: 'Support Information'
                        		}).open();

                        		return false;
                        	});
                        });
                    </script>
                    <!-- Dialog alert template -->
                    <script type="text/html" id="dialog-alert-template">
                        <p>{{{content}}}</p>
                        <div class="buttons">
                        	<button class="classy green mini confirm ok" type="button">
                        		<span class="button-span button-span-green">Ok</span>
                        	</button>
                        </div>
                    </script><script type="text/javascript">
                        /*<![CDATA[*/
                        jQuery(function($) {
                        jQuery('#date-start').datepicker({'showAnim':'fold','dateFormat':'d M yy'});
                        jQuery('#date-end').datepicker({'showAnim':'fold','dateFormat':'d M yy'});
                        });
                        /*]]>*/
                    </script>
                </body>
                </body>
            </html>