<div class="patient-activity">
	<input placeholder="<?= (isset($html_options['placeholder']) ? $html_options['placeholder'] : 'Type to search') ?>" class="cols-full search autocompletesearch" id="<?= $field_name; ?>" type="text" value="" name="<?= $field_name; ?>" autocomplete="off">
	<ul class="oe-autocomplete hidden" id="ui-id-1" tabindex="0">
	</ul>
	<div class="data-group no-result warning alert-box hidden">
	    <div class="small-12 column text-center"> 
	        No results found. 
	    </div>
	</div>
	<div class="data-group min-chars warning alert-box hidden">
	    <div class="small-12 column text-center"> 
	        Minimum of 2 characters
	    </div>
	</div>
</div>