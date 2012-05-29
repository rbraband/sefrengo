var $jqsf = jQuery;

$jqsf(document).ready(function()
{
	SF.Plugin.loadAll();
});

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Tooltip',
	['.sf_hmenu_wrapper'],
	['jquery.qtip.css',
	 'lib/jquery.qtip.min.js'],
	function(scope)
	{
		scope = (typeof(scope) === 'undefined') ? document : scope;
		
		// tooltip init / binds
		if($jqsf(document).qtip)
		{
			var options = {
					content: {
						text: '' // replaced in loop
					},
					position: {
						my: 'top left',
						at: 'bottom center',
						viewport: $jqsf(window),
						adjust: {
							method: 'shift flip',
							x: -4
						}
					},
					hide: {
						fixed: true,
						delay: 500
					}
				},
				$wrapper = $jqsf('.sf_hmenu_wrapper', scope);
			
			for(var i = 0, len = $wrapper.length; i < len; i++)
			{
				options.content.text = $jqsf('.sf_hovermenu', $wrapper[i]);
				$jqsf('.sf_hmenu_trigger', $wrapper[i]).qtip(options);
			}
		}
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Datepicker',
	['.sfDatepicker'],
	['lib/jquery-ui-i18n.js'],
	function(scope)
	{
		scope = (typeof(scope) === 'undefined') ? document : scope;

		if ($jqsf(document).datepicker)
		{
			var $datepicker = $jqsf('.sfDatepicker'),
				$datepickerCur = {},
				options = {},
				onSelectFunc = function( selectedDate ) {
					var $this = $jqsf(this),
						options = $this.data('datepicker-options'),
						minmax = ($this.hasClass('sfDatepickerFrom')) ? "minDate" : "maxDate",
						instance = $this.data( "datepicker" ),
						date = $jqsf.datepicker.parseDate(
							instance.settings.dateFormat ||
							$jqsf.datepicker._defaults.dateFormat,
							selectedDate, instance.settings );
							
					if(typeof(options.connectedDatepickerId) !== 'undefined')
					{
						$jqsf('#' + options.connectedDatepickerId).datepicker( "option", minmax, date );
					}
				};
			
			for(var i = 0, len = $datepicker.length; i < len; i++)
			{
				$datepickerCur = $jqsf($datepicker[i]);
				options = (typeof($datepickerCur.data('datepicker-options')) === 'undefined') ? {} : $datepickerCur.data('datepicker-options');
				options.regional = (typeof(options.regional) === 'undefined') ? 'de' : options.regional;
				
				$jqsf.datepicker.setDefaults( $.datepicker.regional[ options.regional ] );
				
				options.showOn = 'both';
				options.buttonImage = SF.Config.img_dir + 'but_calendar.png';
				options.buttonImageOnly = true;
				options.buttonText = SF.Lang.show_calendar;
				options.showWeek = true;
				options.changeMonth = true;
				options.changeYear = true;
				//options.showOtherMonths = true;
				//options.selectOtherMonths = true;
				if($datepickerCur.hasClass('sfDatepickerFrom') || $datepickerCur.hasClass('sfDatepickerTo'))
				{
					options.onSelect = onSelectFunc;
				}
				
				$datepickerCur.datepicker(options);
			}
		}
	}
);

// ----------------------------------------------------------------- //

function delete_confirm()
{
	if(confirm(sf_lng_delete_confirm)) {
		return true;
	}
	
	return false;
}
