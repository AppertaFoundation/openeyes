$(document).ready(function(){
	$collapsed = true;

// Add new event menu
	$(document).click(function() {
	  $("#add_event_wrapper .addEvent").hide(); //click came from somewhere else
		$('#addNewEvent').removeClass('inactive').addClass('green');
		$('#addNewEvent span.button-span-inactive').removeClass('button-span-inactive').addClass('button-span-green');
		$('#addNewEvent span.button-icon-inactive').removeClass('button-icon-inactive').addClass('button-icon-green');
	  $collapsed = true;
	});
	$('#addNewEvent').click(function(e) {
		$('#add_event_wrapper .addEvent').slideDown(100,function() {
			$('#addNewEvent').removeClass('green').addClass('inactive');
			$('#addNewEvent span.button-span-green').removeClass('button-span-green').addClass('button-span-inactive');
			$('#addNewEvent span.button-icon-green').removeClass('button-icon-green').addClass('button-icon-inactive');
			$collapsed = false;
		});
		e.stopPropagation();
	});

	$('label').die('click').live('click',function() {
		if ($(this).prev().is('input:radio')) {
			$(this).prev().click();
		}
	});

	$(this).undelegate('select.dropDownTextSelection','change').delegate('select.dropDownTextSelection','change',function() {
		if ($(this).val() != '') {
			var target = $('#' + $(this).attr('id').replace(/^dropDownTextSelection_/,''));
			var currentVal = target.val();
			
			if($(this).hasClass('delimited')) {
				var newText = $(this).val();
			} else {
				var newText = $(this).children('option:selected').text();
			}

			if (currentVal.length > 0 && !$(this).hasClass('delimited')) {
				if (newText.toUpperCase() == newText) {
					newText = ', ' + newText;
				} else {
					newText = ', ' + newText.charAt(0).toLowerCase() + newText.slice(1);
				}
			} else if (currentVal.length == 0 && $(this).hasClass('delimited')) {
				newText = newText.charAt(0).toUpperCase() + newText.slice(1)
			}
			
			target.val(currentVal + newText);
			target.trigger('autosize');

			$(this).val('');
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

		for (var value in this.remap) {
			if (val == value) {
				val = this.remap[value];
			}
		}

		if (this.null.length >0) {
			if (val.match(/\./)) {
				val = String(parseFloat(val) - 1);
			} else {
				val = String(parseInt(val) - 1);
			}
		}

		var min = $('#'+this.range_id).attr('min');

		if (min.match(/\./)) {
			min = parseFloat(min);
		} else {
			min = parseInt(min);
		}

		if (val < min && this.null.length >0) {
			val = this.null;
		} else {
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
					
					while (dp.length > this.force_dp) {
						dp = dp.replace(/.$/,'');
						val = val.replace(/.$/,'');
					}
				} 
			}

			if (this.prefix_positive && parseFloat(val) >0) {
				var val = this.prefix_positive + val;
			}
		}

		$('#'+this.range_id+'_value_span').text(val+this.append);
	}
}

function WidgetSliderTable() {if (this.init) this.init.apply(this, arguments); }

WidgetSliderTable.prototype = {
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

		$('#'+this.range_id+'_value_span').text(this.data[val]);
	}
}
