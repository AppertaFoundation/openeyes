$(document).ready(function(){
	$collapsed = true;

	$('#addNewEvent').unbind('click').click(function(e) {
		e.preventDefault();
		$collapsed = false;

		$('#add-event-select-type').slideToggle(100,function() {
			if($(this).is(":visible")){
				$('#addNewEvent').removeClass('green').addClass('inactive');
				$('#addNewEvent span.button-span-green').removeClass('button-span-green').addClass('button-span-inactive');
				$('#addNewEvent span.button-icon-green').removeClass('button-icon-green').addClass('button-icon-inactive');
			} else {
				$('#addNewEvent').removeClass('inactive').addClass('green');
				$('#addNewEvent span.button-span-inactive').removeClass('button-span-inactive').addClass('button-span-green');
				$('#addNewEvent span.button-icon-inactive').removeClass('button-icon-inactive').addClass('button-icon-green');
				$collapsed = true;
			}
			return false;
		});

		return false;
	});

	$('label').die('click').live('click',function() {
		if ($(this).prev().is('input:radio')) {
			$(this).prev().click();
		}
	});

	$('select.dropDownTextSelection').die('change').change(function() {
		if ($(this).val() != '') {
			var target_id = $(this).attr('id').replace(/^dropDownTextSelection_/,'');

			if ($('#'+target_id).text().length >0) {
				$('#'+target_id).text($('#'+target_id).text()+', ');
			}

			$('#'+target_id).text($('#'+target_id).text()+$(this).children('option:selected').text());

			$(this).children('option:selected').remove();
		}
	});
});

function selectSort(a, b) {		 
		if (a.innerHTML == rootItem) {
				return -1;		
		}
		else if (b.innerHTML == rootItem) {
				return 1;  
		}				
		return (a.innerHTML > b.innerHTML) ? 1 : -1;
};

var rootItem = null;

function sort_selectbox(element) {
	rootItem = element.children('option:first').text();
	element.append(element.children('option').sort(selectSort));
}

function WidgetSlider() {if (this.init) this.init.apply(this, arguments); }

WidgetSlider.prototype = {
	init : function(params) {
		for (var i in params) {
			this[i] = params[i];
		}

		var thiz = this;

		$(document).ready(function() {
			thiz.bindElement();
		});
	},

	bindElement : function() {
		var thiz = this;

		$('#'+this.range_id).change(function() {
			thiz.handleChange($(this));
		});
	},

	handleChange : function(element) {
		var val = element.val();

		if (val == '10.25') {
			val = '10.5';
		} else if (val == '-10.25') {
			val = '-10.5';
		}

		if (this.force_dp) {
			if (!val.match(/\./)) {
				val += '.';
				for (var i in this.force_dp) {
					val += '00';
				}
			} else {
				var dp = val.replace(/^.*?\./,'');

				while (dp.length < this.force_dp) {
					dp += '0';
					val += '0';
				}
			} 
		}

		if (this.prefix_positive && parseFloat(val) >0) {
			var val = this.prefix_positive + val;
		}

		$('#'+this.range_id+'_value_span').text(val);
	}
}
