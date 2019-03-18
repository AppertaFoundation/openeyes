 <form class="report-search-form mdl-color-text--grey-600" action="/report/reportData" style="display: none;">
        <input type="hidden" name="report" value="<?= $report->getApp()->getRequest()->getQuery('report'); ?>" />
        <fieldset>
            <div id="search-form-to-side-bar">
                <div class="mdl-selectfield">
                    <h3>Months Since Operation</h3>
                    <select name="months" id="months-since-operation" style="font-size: 1em; width: inherit" >
                        <?php
                        $monthsoptions = array(1, 0.25,0.5,0.75,2,3,4);
                        foreach ($monthsoptions as $option):?>
                            <option value="<?=$option?>"><?=$option?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div>
                <button type="submit" name="action">Submit</button>
            </div>
        </fieldset>
 </form>