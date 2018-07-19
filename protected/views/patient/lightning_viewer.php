
<?php $this->renderPartial('//patient/episodes_sidebar');?>

<main class="oe-lightning-viewer">

    <div class="lightning-timeline">

        <div class="timeline-options js-lightning-options">

            <!-- interaction for the 'button' is all handled with JS, not CSS -->
            <div class="lightning-btn">
                <svg viewBox="0 0 30 30" class="lightning-icon">
                    <use xlink:href="../newblue/svg/oe-nav-icons.svg#lightning-viewer-icon"></use>
                </svg>
                <!-- indicates functionality -->
                <i class="oe-i small-icon arrow-down-bold"></i>
            </div>

            <!-- options menu, show on click / hover -->
            <div class="change-timeline" style="display: none;">

                <ul>
                    <li class="selected"><i class="oe-i-e i-CoCorrespondence"></i> Letters (57)</li>
                    <li><i class="oe-i-e i-ImPhoto"></i> Photos (5)</li>
                    <li><i class="oe-i-e i-InBiometry"></i> Biometry (17)</li>
                    <li><i class="oe-i-e i-InERG"></i> ERG (6)</li>
                    <li><i class="oe-i-e i-CoDocument"></i> DHR (6)</li>
                </ul>

            </div>

        </div><!-- timeline-options -->

        <!-- timeline -->
        <div class="timeline">
            <table>
                <tbody><tr>
                    <td class="date-divider">2016 <i class="oe-i small collapse js-timeline-date" data-icons="1"></i></td>
                    <td class="date-divider">2015 <i class="oe-i small collapse js-timeline-date" data-icons="2"></i></td>
                    <td class="date-divider">2014 <i class="oe-i small collapse js-timeline-date" data-icons="3"></i></td>
                    <td class="date-divider">2013 <i class="oe-i small collapse js-timeline-date" data-icons="4"></i></td>
                    <td class="date-divider">2012 - 2000 <i class="oe-i small js-timeline-date collapse" data-icons="5"></i></td>
                    <td class="date-divider">2000 - 1960 <i class="oe-i small collapse js-timeline-date" data-icons="6"></i></td>
                </tr>
                <tr>
                    <td>
                        <div id="js-icon-1" class="icon-group">
                            <span id="lqv_0" class="icon-event" data-lightning="sent,23 Nov 2016,Ms Angela Glasby,2016-11-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_1" class="icon-event" data-lightning="sent,23 Nov 2016,Ms Angela Glasby,2016-11-23_2"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_2" class="icon-event js-hover" data-lightning="sent,5 Mar 2016,Ms Angela Glasby,2016-3-5"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_3" class="icon-event" data-lightning="sent,23 Feb 2016,Ms Angela Glasby,2016-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span>							</div><!-- icon-group -->
                        <div style="display: none;">(4)</div></td>

                    <td>
                        <div id="js-icon-2" class="icon-group">
                            <span id="lqv_4" class="icon-event" data-lightning="sent,10 Aug 2015,Mr David Haider,2015-8-10"><i class="oe-i-e i-CoCorrespondence"></i></span>							</div><!-- icon-group -->
                        <div style="display: none;">(1)</div></td>
                    <td>
                        <div id="js-icon-3" class="icon-group">
                            <span id="lqv_5" class="icon-event" data-lightning="sent,7 Jul 2014,Mr David Haider,2014-7-7"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_6" class="icon-event" data-lightning="sent,16 Jun 2014,Mr David Haider,2014-6-16"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_7" class="icon-event" data-lightning="sent,10 Mar 2014,Mr David Haider,2014-3-10"><i class="oe-i-e i-CoCorrespondence"></i></span>							</div><!-- icon-group -->
                        <div style="display: none;">(3)</div></td>
                    <td>
                        <div id="js-icon-4" class="icon-group">
                            <span id="lqv_8" class="icon-event" data-lightning="sent,24 Nov 2013,Mr David Haider,2013-11-24"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_9" class="icon-event selected" data-lightning="sent,24 Nov 2013,Mr David Haider,2013-11-24_2"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_10" class="icon-event" data-lightning="sent,15 Jul 2013,Prof. James Morgan,2013-7-15"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_11" class="icon-event" data-lightning="sent,15 Jul 2013,Mr David Haider,2013-7-15_2"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_12" class="icon-event" data-lightning="sent,11 Jul 2013,Mr David Haider,2013-7-11"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_13" class="icon-event" data-lightning="sent,1 May 2013,Mr David Haider,2013-5-1"><i class="oe-i-e i-CoCorrespondence"></i></span>							</div><!-- icon-group -->
                        <div style="display: none;">(6)</div></td>
                    <td>
                        <div id="js-icon-5" class="icon-group" style="">
                            <span id="lqv_14" class="icon-event" data-lightning="sent,14 Nov 2012,Prof. James Morgan,2012-11-14"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_15" class="icon-event" data-lightning="sent,15 May 2012,Mr David Haider,2012-5-15"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_16" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_17" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_18" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_19" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_20" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_21" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_22" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_23" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_24" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_25" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_26" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_27" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_28" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_29" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_30" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_31" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_32" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_33" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_34" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_35" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_36" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_37" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_38" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_39" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_40" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_41" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_42" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_43" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_44" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_45" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span>							</div><!-- icon-group -->
                        <div style="display: none;">(32)</div></td>
                    <td>
                        <div id="js-icon-6" class="icon-group">
                            <span id="lqv_46" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_47" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_48" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_49" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_50" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_51" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_52" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_53" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_54" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_55" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span><span id="lqv_56" class="icon-event" data-lightning="sent,23 Feb 2012,Mr David Haider,2012-2-23"><i class="oe-i-e i-CoCorrespondence"></i></span>							</div><!-- icon-group -->
                        <div style="display: none;">(11)</div></td>
                </tr>
                </tbody></table>
        </div><!-- timeline -->

    </div><!-- lightning-timeline -->

    <div class="flex-layout flex-left flex-top">

        <!-- js generated content -->
        <div class="oe-lightning-meta">
            <div class="letter-type">Letter sent</div>
            <div class="date">5 Mar 2016</div>
            <div class="sender">Ms Angela Glasby</div>

            <div class="help">
                swipe to scan | click to lock
            </div>
        </div>


        <div class="oe-lightning-quick-view">
            <img src="../assets/img/_letters/2016-3-5.png" alt="_demo_letter">
        </div>

    </div>
</main>