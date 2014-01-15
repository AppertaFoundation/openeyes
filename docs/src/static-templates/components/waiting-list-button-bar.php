<button id="btn_print_all" class="small">Print all</button>
<button id="btn_print" class="small">Print selected</button>
<div class="panel admin">
	<label for="adminconfirmdate">Set latest letter sent to be:</label>
	<input class="small fixed-width" type="text" />
</div>
<div class="panel admin">
	<select name="adminconfirmto" id="adminconfirmto">
		<option value="OFF">Off</option>
		<option value="noletters">No letters sent</option>
		<option value="0">Invitation letter</option>
		<option value="1">1st reminder letter</option>
		<option value="2">2nd reminder letter</option>
		<option value="3">GP letter</option>
	</select>
</div>
<button class="small secondary">
	Confirm selected
</button>