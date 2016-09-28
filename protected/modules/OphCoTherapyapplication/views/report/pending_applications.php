<div class="box reports">
    <div class="report-fields">
        <h2>Pending Therapy Applications Report</h2>
        <?php if ($sent):?>
            <span>Report sent</span>
        <?php else:?>
            <form>
                <button type="submit" name="report" value="generate">Generate</button>
            </form>
        <?php endif;?>
    </div>
</div>
