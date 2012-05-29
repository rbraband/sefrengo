$jqsf(document).ready(function()
{
	// doing the heavy init action and load all plugins if used
	SF.Plugin.loadAll();
	
	$jqsf(document)
		// open and close the filter form
		.on('click', 'a.filterform_toggle', function() {
			$jqsf("form[name='" + $jqsf(this).attr('rel') + "']").toggle();
			
			return false;
		})
		
		// submitting the form on changing the actionbox
		.on('change', 'select.actionbox', function() {
			var onchange_confirm = $(this).data('onchange-confirm');
			onchange_confirm = onchange_confirm.replace("{selection}", this.options[this.selectedIndex].text);
			if( this.options[this.selectedIndex].value != '' &&
				(onchange_confirm == '' || (onchange_confirm != '' && confirm( onchange_confirm ) == true))
			  )
			{
				$(this).parents('form').submit();
			}
		});
});

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'SortableSelectList',
	['select.sortableselect'],
	[], // no files
	function()
	{
		var $select = $jqsf('select.sortableselect'),
			select_len = $select.length;
			
		if(select_len > 0)
		{
			for (var i = 0; i < select_len; i++)
			{
				var $this = $($select[i]).hide(),
					html = '<div class="sortableselect" id="'+$this.attr('name')+'"><ul>',
					$option = $jqsf('option', $this),
					option_len = $option.length;
				
				for(var k = 0; k < option_len; k++)
				{
					if(k == 0)
					{
						html += '<li rel="'+$option[k].value+'" class="'+(($option[k].selected == true) ? 'ui-selected' : '')+'">'+$option[k].text+'</li>';
					}
					else if($option[k].selected == true && $option[k].value != '0')
					{
						html += '<li rel="'+$option[k].value+'" class="'+(($option[k].selected == true) ? 'ui-selected' : '')+'">'+$option[k].text+'</li>';
					}
					else
					{
						html += '<li rel="'+$option[k].value+'" class="'+(($option[k].selected == true) ? 'ui-selected' : '')+'">'+$option[k].text+'</li>';
					}
				};
				
				html += '</ul></div>';
				$this.parent().prepend(html);
				
				$jqsf('div.sortableselect ul', $this.parent())
					.sortable({ 
						handle: '.handle',
						axis: 'y',
						stop: function() {
							var $ul = $jqsf(this),
								$select = $ul.parent().parent().find('select'),
								options = {},
								$lis = $jqsf('li', $ul),
								$li = {};
								
							$select.html('');
							for(var i = 0, len = $lis.length; i < len; i++)
							{
								$li = $jqsf( $lis[i] );
								options +='<option value="'+$li.attr('rel')+'"';
								options += ($li.hasClass('ui-selected') == true) ? ' selected="selected"' : '';
								options += '>'+$li.text()+'</option>';
							};
							$select.html(options);
						}
					})
					.find('li').prepend('<div class="handle"><span></span></div>');
			};
			
			var lis = $jqsf('div.sortableselect ul li'),
				lastChecked = null,
				isShiftPressed = false,
				isCtrlPressed = false;
			
			$jqsf(document)
				// disable selection of a table
				.on('selection.disable', 'div.sortableselect ul', function() {
					$jqsf(this)
						.attr('unselectable', 'on')
						.addClass('disableselection')
						.on('selectstart', function() { return false; });
				})
				// enable selection of a table
				.on('selection.enable', 'div.sortableselect ul', function() {
					$jqsf(this)
						.removeAttr('unselectable')
						.removeClass('disableselection')
						.off('selectstart');
				})
				// highlight table rows by clicking only the td
				.on('click', 'div.sortableselect ul', function(event) {
					if($jqsf(event.target).is('li') == true)
					{
						var $li = $jqsf(event.target),
							$select = $li.parent().parent().siblings('select'),
							$option_selected = $jqsf('option:eq(' + $li.index()  + ')', $select);
						
						$li.toggleClass('ui-selected');
						
						if($li.hasClass('ui-selected'))
						{
							$option_selected.attr('selected', 'selected')
						}
						else
						{
							$option_selected.removeAttr('selected');
						}
						
						isShiftPressed = false; // assume that no shift key is pressed
						isCtrlPressed = false; // assume that no ctrl key is pressed
						
						// select multiple rows with shift key
						if(!lastChecked)
						{
							lastChecked = $li[0];
							return;
						}
						
						if(event.shiftKey == true)
						{
							var $lis = $li.parent().find('li'),
								start = $lis.index($li[0]),
								end = $lis.index(lastChecked),
								$options = $jqsf('option', $select),
								toSelect = $jqsf(lastChecked).hasClass('ui-selected');
							
							$lis.slice(Math.min(start,end), Math.max(start,end) + 1).toggleClass('ui-selected', toSelect);
							
							if(toSelect == true)
							{
								$options.slice(Math.min(start,end), Math.max(start,end) + 1).attr('selected', 'selected')
							}
							else
							{
								$options.slice(Math.min(start,end), Math.max(start,end) + 1).removeAttr('selected');
							}
							
							// enable selection
							$jqsf('div.sortableselect ul').trigger('selection.enable');
							
							// reset and indicate that shift key was pressed
							lastChecked = null;
							isShiftPressed = true;
							return;
						}
						else if(event.ctrlKey == true)
						{
							lastChecked = null;
							isCtrlPressed = true;
							return;
						}
						
						lastChecked = $li[0];
					}
				})
				.on('keydown', function(event) {
					if(isShiftPressed == false && event.shiftKey == true && lastChecked != null)
					{
						isShiftPressed = true;
						$jqsf('div.sortableselect ul').trigger('selection.disable');
					}
					else if(isCtrlPressed == false && event.ctrlKey == true && lastChecked != null)
					{
						isCtrlPressed = true;
						$jqsf('div.sortableselect ul').trigger('selection.disable');
					}
				})
				.on('keyup', function(event) {
					if(isShiftPressed == true && lastChecked != null)
					{
						isShiftPressed = false;
						$jqsf('div.sortableselect ul').trigger('selection.enable');
					}
					else if(isCtrlPressed == true && lastChecked != null)
					{
						isCtrlPressed = false;
						$jqsf('div.sortableselect ul').trigger('selection.enable');
					}
				});
		}
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Splitter',
	['#main .leftpane'],
	['lib/jquery.splitter.min.js'],
	function()
	{
		// init jquery splitter if leftpane and rightpane are set
		if ($jqsf(document).splitter &&
			$jqsf(this.leftpane, this.container)[0] &&
			$jqsf(this.rightpane, this.container)[0])
		{
			$jqsf(document.body).addClass('addedSplitter');
			
			var main_metadata = (typeof($jqsf(this.container).data('config')) === 'undefined') ? {} : $jqsf(this.container).data('config'),
				customCookiePath = location.pathname;
			
			customCookiePath = customCookiePath.replace( customCookiePath.substring( customCookiePath.lastIndexOf('/')+1 ), '');
			this.save_leftpane_width = parseInt($jqsf(this.leftpane, this.container).css('width'));
			
			// init splitter
			$jqsf(this.container).splitter({
				splitVertical: true,
				sizeLeft: true,
				resizeToWidth: true,
				anchorToWindow: true,
				activeClass: 'vsplitbar_active',
				cookie: main_metadata.leftpane_name + '-splitter',
				cookiePath: customCookiePath
			});
			
			$jqsf('.vsplitbar').append('<span class="vsplitbar_arrows"></'+'span>');
			
			SF.Plugin.get('DebugTable').appendTable(this.rightpane);
			this.paneToggler();
		}
	}
);

SF.Plugin.get('Splitter').leftpane = '.leftpane';
SF.Plugin.get('Splitter').rightpane = '.rightpane';
SF.Plugin.get('Splitter').container = '#main';
SF.Plugin.get('Splitter').save_leftpane_width = 0;

SF.Plugin.get('Splitter').paneToggler = function() {
	if ($jqsf(document).splitter &&
		$jqsf(this.leftpane, this.container)[0] &&
		$jqsf(this.rightpane, this.container)[0])
	{
		var leftpane_drag_width = 0;
			
		$(document)
			.on('mousedown', '.vsplitbar', function(){
				var p = SF.Plugin.get('Splitter');
				leftpane_drag_width = $jqsf(p.leftpane).width();
			})
			.on('mouseup', '.vsplitbar', function(){
				var p = SF.Plugin.get('Splitter');
				if($jqsf(p.leftpane).width() == leftpane_drag_width) 
				{
					if(parseInt($jqsf(p.leftpane, p.container).css('width')) > 10)
					{
						p.closeLeftpane();
					}
					else
					{
						p.openLeftpane();
					}
				}
			});
		
		// check if other width is set from splitter cookie
		if(parseInt($jqsf(this.leftpane, this.container).css('width')) > 10)
		{
			this.save_leftpane_width = parseInt($jqsf(this.leftpane, this.container).css('width'));
			this.openLeftpane();
		}
		else
		{
			this.closeLeftpane();
		}
	}
};
			
SF.Plugin.get('Splitter').openLeftpane = function() {
	var main_metadata = (typeof($jqsf(this.container).data('config')) === 'undefined') ? {} : $jqsf(this.container).data('config');
	
	//init loading the tree if not exists
	if(typeof($jqsf('.treeview', this.leftpane)[0]) === 'undefined' &&
		typeof(main_metadata.loadurl) !== 'undefined')
	{
		$jqsf(this.leftpane).addClass('loading');
		
		$jqsf.get(main_metadata.loadurl, null, function(data, textStatus) {
			var p = SF.Plugin.get('Splitter');
			
			$jqsf(p.leftpane).children(0).append(data);
			
			var treeview_options = {};
			if(typeof($jqsf('.treeview', p.leftpane).data('options')) !== 'undefined')
			{
				treeview_options = $jqsf('.treeview', p.leftpane).data('options');
				
				if( typeof(treeview_options.persist) === 'undefined' && 
					typeof(main_metadata.leftpane_name) !== 'undefined')
				{
					treeview_options.persist = 'cookie';
					treeview_options.cookieId = main_metadata.leftpane_name + '-tree';
				}
			}
			
			SF.Plugin.load('Treeview', {rerun: true, arguments: [p.leftpane, treeview_options]});
			
			$jqsf(p.leftpane).removeClass('loading');
			
		}, 'html');
	}
	$jqsf(this.container).trigger('resize', [this.save_leftpane_width]);
	$jqsf('.vsplitbar').addClass('vsplitbar_opened').removeClass('vsplitbar_closed');
};
			
SF.Plugin.get('Splitter').closeLeftpane = function() {
	$jqsf(this.container).trigger('resize', [0]); // width: 0px
	$jqsf('.vsplitbar').removeClass('vsplitbar_opened').addClass('vsplitbar_closed');
};

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Treeview',
	['.treeview'],
	['jquery.treeview.css',
	 'lib/jquery.treeview.min.js'],
	function(scope, options)
	{
		scope = (typeof(scope) === 'undefined') ? document : scope;
		options = (typeof(options) === 'undefined') ? {} : options;
	
		//console.log('treeview',$jqsf('.treeview', scope)[0], $jqsf(document).treeview);
		// init jquery treeview if one found in document
		if ($jqsf(document).treeview && $jqsf('.treeview', scope)[0]) 
		{
			if(options.enableDirectoryChooser && options.enableDirectoryChooser == true)
			{
				SF.Plugin.load('DirectoryChooser', {rerun: true, arguments: [scope]});
				options.enableDirectoryChooser = null;
			}
			
			var treeview = $jqsf('.treeview', scope),
				treeview_options = (typeof(treeview.data('options')) === 'undefined') ? {} : treeview.data('options');
			
			$jqsf.extend(treeview_options, options);
			
			// auto open tree structure to active folder
			this.updateActiveNode();
			
			treeview.treeview(treeview_options);
			
			$jqsf('a.active', treeview).parents('li.expandable').children('div.hitarea').trigger('click');
			
			if($jqsf('a.active', treeview)[0])
			{
				var top = $jqsf('a.active', treeview).position().top - 25;
				top = (top < 0) ? 0 : top;
				treeview.parent().scrollTop(top);
			}
		}
	}
);

SF.Plugin.get('Treeview').updateActiveNode = function(scope)
{
	scope = (typeof(scope) === 'undefined') ? '.rightpane' : scope;
	
	// add new :textEquals filter to jQuery
	$jqsf.expr[":"].textEquals = function (a, i, m) {
	    return new RegExp("^" + m[3] + "$").test(a.textContent || a.innerText || $jqsf(a).text() || "");
	};
	
	// set new active folder in tree by using active breadcrumb
	if($jqsf('#crumbs', scope)[0] && $jqsf('.treeview', '.leftpane')[0])
	{
		$jqsf('a.active', '.leftpane').removeClass('active');
		
		// set new active class to link in tree
		var crumbs = $jqsf('#crumbs li:nth-child(2n+1)');
		
		// mark root folder as active
		if(crumbs.length === 1)
		{
			$jqsf('a.directorytree_root', '.leftpane').addClass('active');
		}
		else
		{
			var target_links = $jqsf('a:textEquals("' + $jqsf.trim( $jqsf(crumbs.get(-1)).text() ) + '")', '.leftpane ul'),
				parent_links = {},
				equal_links = true; 
			
			// iterate through each possible link in the tree
			for (var i = k = len2 = 0, len = target_links.length; i < len; i++)
			{
				// follow the tree up and and search parent links 
				parent_links = $jqsf(target_links[i]).parents('li').children('a');
				
				// skip parents that are not in the correct depth
				if(crumbs.length-1 != parent_links.length)
				{
					return;
				}
				
				equal_links = true; 
				
				// skip items of the first depth, they are always correct
				if(parent_links.length > 1)
				{
					// iterate through all parent links
					for (k = 0, len2 = parent_links.length; k < len2; k++)
					{
						// skip root directory
						if(k == 0) continue;
						
						// check if text of crumb link and tree link are equal
						if($('a', crumbs.get((k+1)*-1)).text() == parent_links[k].text)
						{
							equal_links = true;
						}
						else
						{
							equal_links = false;
							break; // exit the for
						}
					};
				}
				
				// all parent links are equal, mark current link as active
				if(equal_links == true)
				{
					$jqsf(target_links[i]).addClass('active');
				}
				
			};
		}
	}
};

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'DirectoryChooser',
	['.directorychooser'],
	[], // no files
	function(scope)
	{
		scope = (typeof(scope) === 'undefined') ? document : scope;
	
		var $directorychooser = $jqsf('.directorychooser', scope);
		
		if ($directorychooser[0])
		{
			var input_field = $directorychooser.siblings('input');
			$directorychooser.on('click', 'a', function()
			{
				var id = $jqsf(this).attr('rel');
				if(id == '')
				{
					id = 0;
				}
				input_field.attr('value', id);
				$jqsf('a.active', $directorychooser).removeClass('active');
				$jqsf(this).addClass('active');
				return false;
			});
			
			$jqsf('a.active', $directorychooser).removeClass('active');
			$jqsf('a[rel='+input_field.attr('value')+']').addClass('active');
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
			var $datepicker = {},
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
				
			$jqsf(document)
				.on('ajaxdeeplink.loaded datepicker.bind', function(event, container)
				{			
					$datepicker = $jqsf('.sfDatepicker', container);
					$datepickerCur = {};
					options = {};
					
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
				})
				
				.trigger('datepicker.bind', [null, scope]);
		}
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Tooltip',
	['.uber a[title]',
	 '.config img',
	 '.toolinfo',
	 '.buttons img[title]',
	 '.forms img[title]',
	 '.forms button[title]',
	 '.toolbar img[title]',
	 '.toolbar button[title]',
	 '.toolbar a[title]'],
	['jquery.qtip.css',
	 'lib/jquery.qtip.min.js'],
	function(scope)
	{
		scope = (typeof(scope) === 'undefined') ? document : scope;
		
		// tooltip init / binds
		if($jqsf(document).qtip)
		{
			var options = {
					position: {
						my: 'bottom center',
						at: 'top center',
						viewport: $jqsf(window)
					},
					show: {
						delay: 500
					},
					hide: {
						//fixed: true // for testing purpose
					},
					style: {
						classes: 'sfTooltip',
						width: 'auto'
					}
				},
				$toolinfos = $jqsf('.toolinfo', scope),
				$toolinfo = null;
			
			$jqsf('.uber a[title], .config img, .buttons img[title], .forms img[title], .forms button[title], .toolbar img[title], .toolbar button[title], .toolbar a[title]', scope).qtip(options);
			
			if($jqsf('.rightpane')[0])
			{
				options.position.viewport = $jqsf('.rightpane');
			}
			options.style.classes += ' sfToolinfo';
			options.content = { text: '' }; // text is replaced in loop
			
			for(var i = 0, len = $toolinfos.length; i < len; i++)
			{
				$toolinfo = $jqsf($toolinfos[i]);
				options.content.text = $toolinfo.siblings('span.toolinfo');
				$toolinfo.qtip(options);
			};
		}
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'ModrewriteCheckbox',
	['#rewrite_use_automatic'],
	[], // no files
	function()
	{
		// set & bind mod_rewrite input field to mod_rewrite automatic-checkbox
		if ($jqsf('#rewrite_use_automatic').attr('checked'))
		{
			$jqsf('#rewrite_alias').addClass('disabled-field-bg');
			$jqsf('#rewrite_url').addClass('disabled-field-bg');
		}
	
		$jqsf(document).on('click', '#rewrite_use_automatic', function()
		{
			var isChecked = $jqsf('#rewrite_use_automatic').is(':checked');
			$jqsf('#rewrite_alias')
				.toggleClass('disabled-field-bg', isChecked)
				.attr('disabled', isChecked);
			$jqsf('#rewrite_url')
				.toggleClass('disabled-field-bg', isChecked)
				.attr('disabled', isChecked);
		});
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'CheckAllNoneInvert',
	['a.chk_select_all', 'a.chk_select_none', 'a.chk_select_invert'],
	[], // no files
	function()
	{
		$jqsf(document)
			// link to select all checkboxes; example: <a href="#" class="chk_select_all" rel="mychk">All</a>
			.on('click', 'a.chk_select_all', function() {
				var rel = $jqsf(this).attr('rel').split(','),
					chk = {};
				for(var i = k = len2 = 0, len = rel.length; i < len; i++)
				{
					chk = $jqsf(":checkbox[name='" + rel[i] + "']");

					for(k = 0, len2 = chk.length; k < len2; k++)
					{
						chk[k].checked = true;
						$jqsf(chk).closest('tr').addClass('tblrbgcolorover');
					}
				}
				return false;
			})
			// link to select no checkboxes; example: <a href="#" class="chk_select_none" rel="mychk">None</a>
			.on('click', 'a.chk_select_none', function() {
				var rel = $jqsf(this).attr('rel').split(','),
					chk = {};
				for(var i = k = len2 = 0, len = rel.length; i < len; i++)
				{
					chk = $jqsf(":checkbox[name='" + rel[i] + "']");

					for(k = 0, len2 = chk.length; k < len2; k++)
					{
						chk[k].checked = false;
						$jqsf(chk).closest('tr').removeClass('tblrbgcolorover');
					}
				}
				return false;
			})
			// link to invert selection for checkboxes; example: <a href="#" class="chk_select_invert" rel="mychk">Invert</a>
			.on('click', 'a.chk_select_invert', function() {
				var rel = $jqsf(this).attr('rel').split(','),
					chk = {};
				for(var i = k = len2 = 0, len = rel.length; i < len; i++)
				{
					chk = $jqsf(":checkbox[name='" + rel[i] + "']");

					for(k = 0, len2 = chk.length; k < len2; k++)
					{
						chk[k].checked = !chk[k].checked;
						$jqsf(chk).closest('tr').toggleClass('tblrbgcolorover');
					}
				}
				return false;
			})
		
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'SelectTableRow',
	['table.sfSelectTableRow'],
	[], // no files
	function()
	{
		var chks = $jqsf('table.sfSelectTableRow :checkbox'),
			lastChecked = null,
			isShiftPressed = false;
		
		$jqsf(document)
			// reset default after ajax loaded
			.on('ajaxdeeplink.loaded', function() {
				chks = $jqsf('table.sfSelectTableRow :checkbox');
				lastChecked = null;
			})
			// disable selection of a table
			.on('selection.disable', 'table.sfSelectTableRow', function() {
				$jqsf(this)
					.attr('unselectable', 'on')
					.addClass('disableselection')
					.on('selectstart', function() { return false; });
			})
			// enable selection of a table
			.on('selection.enable', 'table.sfSelectTableRow', function() {
				$jqsf(this)
					.removeAttr('unselectable')
					.removeClass('disableselection')
					.off('selectstart');
			})
			// highlight table rows by clicking only the td
			.on('click', 'table.sfSelectTableRow tr', function(event) {
				if($jqsf(event.target).is('td') == true)
				{
					var $tr = $jqsf(this),
						$chk = $tr.find(':checkbox');

					if($chk[0])
					{
						$chk[0].checked = !$chk[0].checked;
						$tr.toggleClass('tblrbgcolorover', $chk.checked);
						isShiftPressed = false; // assume that no shift key is pressed
						
						// select multiple rows with shift key
						if(!lastChecked) {
							lastChecked = $chk[0];
							return;
						}
						
						if(event.shiftKey == true) {
							var $chks = $jqsf(chks),
								start = $chks.index($chk[0]),
								end = $chks.index(lastChecked);
							$chks.slice(Math.min(start,end), Math.max(start,end)+ 1).attr('checked', lastChecked.checked).trigger('change');
							// enable selection
							$tr.parents('table').trigger('selection.enable');
							// reset and indicate that shift key was pressed
							lastChecked = null;
							isShiftPressed = true;
							return;
						}
						
						lastChecked = $chk[0];
					}
				}
			})
			.on('keydown', function(event) {
				if(isShiftPressed == false && event.shiftKey == true && lastChecked != null)
				{
					isShiftPressed = true;
					$jqsf('table.sfSelectTableRow').trigger('selection.disable');
				}
			})
			.on('keyup', function(event) {
				if(isShiftPressed == true && lastChecked != null)
				{
					isShiftPressed = false;
					$jqsf('table.sfSelectTableRow').trigger('selection.enable');
				}
			})
			// on change the state of the checkbox, highlight the surrounding table row
			.on('change', 'table.sfSelectTableRow :checkbox', function() {
				$(this).closest('tr').toggleClass('tblrbgcolorover', this.checked);
			});
			
		// highlight all table rows by default
		for(i = 0, len = chks.length; i < len; i++)
		{
			$jqsf(chks[i]).closest('tr').toggleClass('tblrbgcolorover', chks[i].checked);
		}
		
	}
);
		
// ----------------------------------------------------------------- //

SF.Plugin.create(
	'AddCheckboxesToUrl',
	['a.add_chk_to_url'],
	[], // no files
	function()
	{
		$jqsf(document).on('click', 'a.add_chk_to_url', function() {
			return SF.Plugin.get('AddCheckboxesToUrl').append(this);
		});
	}
);

SF.Plugin.get('AddCheckboxesToUrl').append = function(link)
{
	var target = $jqsf(link),
		rel = target.attr('rel').split(','),
		firstchar = (target.attr('href').search(/\?/) === -1) ? '?' : '&',
		params = [],
		chk = {};
		
	for(i = k = len2 = 0, len = rel.length; i < len; i++)
	{
		chk = $jqsf(":checkbox[name='" + rel[i] + "']:checked");
			
		for(k = 0, len2 = chk.length; k < len2; k++)
		{
			params.push(rel[i]+'='+chk[k].value);
		}
	}
	
	location.href = target.attr('href') + firstchar + params.join('&');
	
	return false;
};

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Tabs',
	['.tab-pane','.tab-pane-vertical'],
	[], // no files
	function(scope)
	{
		scope = (typeof(scope) === 'undefined') ? document : scope;

		// tabs init / binds
		if($jqsf(document).tabs)
		{
			var options = {},
				$tabpanes = $jqsf('.tab-pane,.tab-pane-vertical', scope),
				$tabpane = $h2s = $h2 = {};
			
			// cookie plugin avaliable?
			if($jqsf.cookie) 
				options.cookie = {};
			
			for(var i = k = len2 = 0, len = $tabpanes.length; i < len; i++)
			{
				$tabpane = $( $tabpanes[i] );
				$h2s = $jqsf('h2.tab', $tabpane);
				
				// create link and replace h2 with li
				for(k = 0, len2 = $h2s.length; k < len2; k++)
				{
					$h2 = $( $h2s[k] );
					$h2.wrapInner('<a href="#' + $h2.parent().attr('id') + '" />')
					   .replaceWith('<li class="tab">' + $h2.html() + '</li>');
				};
				
				// move li to ul
				$jqsf('li.tab', $tabpane)
					.removeClass('tab')
					.appendTo( $tabpane.prepend('<ul></ul>').children('ul') );
				
				// init tabs
				$tabpane.tabs(options);
			}
		}
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Html5Placeholder',
	['input[placeholder]'],
	['lib/jquery.html5placeholder.min.js'],
	function()
	{
		var bindHtml5Placeholder = function(event, container) {
			$('input[placeholder]', container).placeholder();
		};
		
		$jqsf(document).on('ajaxdeeplink.loaded', bindHtml5Placeholder);
		
		bindHtml5Placeholder(null, document);
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'ToolbarAdvancedSearch',
	['.toolbarAdvancedSearch'],
	[], // no files
	function(scope)
	{
		scope = (typeof(scope) === 'undefined') ? document : scope;
		
		// define function for open/close handling
		var bindOnChangeFormElements = function(event, container) {
				container = (typeof(container) === 'undefined') ? document : container;
				var $form_elments = $jqsf('.toolbarAdvancedSearch form')
						.map(function(){ return $jqsf.makeArray(this.elements); })
						.on('change', function(event) {
							onChangeFormElements(event.currentTarget);
						}),
					// without setTimeout, the function call won't work, because the elements are not available at this time
					timerId = setTimeout(function() {
						clearTimeout(timerId);
						for(var i = 0, len = $form_elments.length; i < len; i++)
						{
							onChangeFormElements($form_elments[i]);
						}
					}, 5);
			},
			
			onChangeFormElements = function(elem) {
				var $this = $jqsf(elem),
					$panel = $this.parents('.ui-tabs-panel'),
					$span = $panel.siblings('.ui-tabs-nav').find('a[href="#' + $panel.attr('id')+'"] span');
				
				if($this.is('select'))
				{
					var arr = [],
						options = $jqsf('option:selected', $this);
					for (var i = 0, len = options.length; i < len; i++)
					{
						arr.push(options[i].text);
					};
					$span.text( (arr.length == 0) ? SF.Lang.no_selection : arr.join(', ') );
				}
				else if($this.is('input:text'))
				{
					$span.text( ($this.val() == '') ? SF.Lang.no_selection : $this.val() );
				}
				else if($this.is('input:checkbox'))
				{
					
					$span.text( ($this.is(':checked') == false) ? SF.Lang.no_selection : $this.siblings('label[for="' + $this.attr('id') + '"]').text() );
				}
				else if($this.is('input:radio:checked'))
				{
					$span.text( $this.siblings('label[for="' + $this.attr('id') + '"]').text() );
					//$span.text( ($this.val() == '') ? SF.Lang.no_selection : $this.siblings('label[for="' + $this.attr('id') + '"]').text() );
				}
			},
					
			clickOutsideEvent = function() {
				$jqsf('.toolbarAdvancedSearch').removeClass('open');
				$jqsf(document)
					.off('click', 'body', clickOutsideEvent)
					.off('click', '.toolbarAdvancedSearch .flyout', clickContainerEvent);
				
				return false;
			},
			
			clickContainerEvent = function(event) {
				event.stopPropagation();
			};
			
		// bind open/close handling
		$jqsf(document)
			.on('click', '.toolbarAdvancedSearch a.sfOpenLink', function() {
				var $this = $jqsf(this),
					$adv_search = $this.parents('.toolbarAdvancedSearch');
				$adv_search.toggleClass('open');
				
				$(document)
					.on('click', 'body', clickOutsideEvent)
					.on('click', '.toolbarAdvancedSearch .flyout', clickContainerEvent);
					
				return false;
			})
			.on('click', '.toolbarAdvancedSearch a.sfCloseLink', clickOutsideEvent)
			
			// if user entered some text into searchfield add class changed
			.on('change', '.toolbarSearch form > input', function() {
				var $this = $jqsf(this);
				$this.parents('.toolbarSearch').toggleClass('changed', ($this.val().length > 0));
			})
			
			// focus the first form field on selecting a tab (Note: The event is trigger from jQuery UI Tabs)
			.on('tabsselect', '.toolbarAdvancedSearch .flyout .ui-tabs', function(event, ui) {
				// without setTimeout, the focus won't work, because the panel is still hidden at this time
				var timerId = setTimeout(function() {
					clearTimeout(timerId);
					$(ui.panel).find(':input').first().focus();
				}, 5);
			})
			
			.on('ajaxdeeplink.loaded', bindOnChangeFormElements);
		
		bindOnChangeFormElements();
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'MainMenu',
	['#mainmenu'],
	[], // no files
	function()
	{
		$('ul#mainmenu').children('li').children('a')
			.click(function() {
				$('li.open', 'ul#mainmenu').removeClass('open');
				$(this).parent().addClass('open');
				return false;
			});
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Loading',
	['#sf_loading'],
	['lib/jquery.waypoints.js'],
	function() {
		var $sf_loading = $jqsf('#sf_loading');
		$('#main').waypoint(function(event, direction) {
			$sf_loading.toggleClass('sticky', direction === 'down');
			event.stopPropagation();
		});
		
		if($sf_loading.position().top > 50)
		{
			$sf_loading.addClass('sticky');
		}
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'FooterLicense',
	['#footer'],
	[], // no files
	function(scope) {
		scope = (typeof(scope) === 'undefined') ? document : scope;
	
		$jqsf(document)
			.on('ajaxdeeplink.loaded footerlicense.move', function(event, container) {
				var $footer = $jqsf('#footer', scope),
					$main = $('#main');
					
				// move footer in visible area
				if($footer.length > 0)
				{
					// no leftpane available, replace the footer after div#main
					if($jqsf('.leftpane', $main).length == 0)
					{
						$main.after($footer);
					}
					else
					{
						$footer
							.hide() // original node for further copies
							.clone()
							.show() // copied node
							.appendTo($jqsf('.rightpane_spacer', $main));
					}
				}
			})
			.trigger('footerlicense.move', [scope]);
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'DebugTable',
	['#sf_debug'],
	[], // no files
	function() {
	}
);

SF.Plugin.get('DebugTable').appendTable = function(appendTo, scope)
{
	scope = (typeof(scope) === 'undefined') ? document : scope;
	
	var $table = $jqsf('#sf_debug', scope),
		$main = $('#main');
	
	// move debug/logger output in visible area
	if($table.length > 0)
	{
		// no leftpane available, replace the debug table after div#main
		if($jqsf('.leftpane', $main).length == 0)
		{
			$main.siblings('#sf_debug').remove();
			$main.after($table);
		}
		else
		{
			$table.appendTo(appendTo);
		}
	}
	
	$jqsf(document).trigger('hideparams.bind', ['#sf_debug']);
};

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'HideParams',
	['pre.params'],
	[], // no files
	function(scope)
	{
		scope = (typeof(scope) === 'undefined') ? document : scope;
		
		$jqsf(document)
			.on('ajaxdeeplink.loaded hideparams.bind', function(event, container) {
				// remove empty param boxes
				$jqsf('pre.params:empty').remove();
				
				var $params = $jqsf('pre.params', container).hide();
				if($jqsf('a', $params.parent()).length <= 0)
				{
					$params.before('<a href="#">' + SF.Lang['show_params'] + '</a>');
					$params.parent().on('click', 'a', function(event) {
						event.preventDefault();
						var $this = $jqsf(this),
							$params = $this.siblings('.params');
						$this.text( ($params.is(':hidden') == true) ? SF.Lang['hide_params'] : SF.Lang['show_params'] );
						$params.toggle();
					});
				}
			})
			.trigger('hideparams.bind', [scope]);
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'AjaxDeeplink',
	['a.ajaxdeeplink'],//, 'form.ajaxdeeplink'],
	['lib/jquery.address.min.js'],
	function()
	{
		// initialize jQuery Address state function with Sefrengo subdirctory
		// if HTML5 pushState is supported
		if(window.history.pushState && typeof(SF.Config.backend_dir) !== 'undefined' &&
			SF.Config.backend_dir != '') {
			$.address.state(SF.Config.backend_dir);
		}
		
		var isAddressInitialized = false;
		//var shared_parameters = null;
		$jqsf.address
			.init(function(event) {
				// use jQuery address to get a deeplink
				$jqsf('a.ajaxdeeplink').address();
				
				//$jqsf(document).on('submit', 'form.ajaxdeeplink', function () { 
				//	shared_parameters = $jqsf(this).serializeArray();     
				//	$jqsf.address.value( $jqsf(this).attr('action') );
				//	return false;
				//});
				
				isAddressInitialized = true;
				
				//if($jqsf('#crumbs').length > 0) {
				//	$jqsf.address.title($jqsf('#crumbs').text());
				//}
				
			// handle the deeplink change
			})
			.change(function(event) {
				// if HTML5 is enabed, abort loading directly after initializing
				// reason: avoid loading the loaded state twice
				if(typeof($.address.state()) !== 'undefined' && isAddressInitialized == true) {
					isAddressInitialized = false;
					return false;
				}
				
				// cut off the / at the beginning
				var url = event.value.substr(1),
					rel = (typeof(event.parameters.rel) !== 'undefined') ? event.parameters.rel : 'rightpane',
					container = '.'+rel;
				
				// if the full url is given, shorten it to use only filename and query
				if (url.indexOf('main.php') != 0)
				{
					url = url.substring(url.lastIndexOf('/')+1);
				}
				
				// if no url given or container doesn't exists, then nothing to do
				if(url == '' || url.indexOf('main.php') == -1 || $jqsf(container).length <= 0)
				{
					return false;
				}
				
				$jqsf('body').addClass('loadingInProgress');
				
				var parameters = {render: rel};
				//if(shared_parameters !== null)
				//{
				//	shared_parameters.render = rel;
				//	parameters = shared_parameters;
				//	shared_parameters = null;
				//}
				
				$jqsf.get(
					url,
					parameters,
					function(data) {
						// add extra root element to traverse with jQuery
						data = '<div>' + data + '</div>';
						var new_content = $jqsf(container, data);
						
						// if container not found, the user maybe logged out
						if(new_content.length === 0)
						{
							// so reload the page and show login screen
							location.reload();
							return false;
						}
						
						// place new content
						$jqsf(container).html(
							new_content.html()
						);
						
						// change title recently
						//if($jqsf('#crumbs').length > 0) {
						//	$jqsf.address.title($jqsf('#crumbs').text());
						//}
						
						$jqsf(document).trigger('ajaxdeeplink.loaded', [container]);
						
						// move debug/logger output in visible area
						SF.Plugin.get('DebugTable').appendTable(container, data);
						SF.Plugin.get('Treeview').updateActiveNode(container);
						SF.Plugin.load('Tooltip', {rerun: true, arguments: [container]});
						SF.Plugin.load('Tabs', {rerun: true, arguments: [container]});
						SF.Plugin.load('Overlay', {rerun: true});
							
						$jqsf('body').removeClass('loadingInProgress');
						
						//paneToggler('#main', '.leftpane', '.rightpane');
					}
				).error(function() {
					alert('Error loading url ' + url + ' by AJAX!');
					$jqsf('body').removeClass('loadingInProgress');
				});
				
				return false;
			});
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Uploadify',
	[], // load manually
	['lib/swfobject.js',
	 'lib/jquery.uploadify.min.js',
	 'lib/jquery.multifile.pack.js'],
	function(_this, wrap, url) {
		$.sf_overlay('upload', _this, wrap, url);
	}
);

// ----------------------------------------------------------------- //

SF.Plugin.create(
	'Overlay',
	['a.overlay[rel]'],
	['lib/jquery.tools.min.js!'], // Note: The '!' indicates that there is no minified file
	function() {
		$jqsf.sf_overlay('init');
	}
);

// ----------------------------------------------------------------- //

(function( $ ){
	
	var options = {},
		overlay_methods = {};
	
	options = {
		positionTop : 85,
		isCloseConfirmEnabled : false,
		langCloseConfirm : 'close_confirm',
		imgAutoResize : true
	};
	
	overlay_methods = {
		init : function(scope)
		{
			scope = (typeof(scope) === 'undefined') ? document : scope;
			
			var $overlayLinks = $jqsf("a.overlay[rel]", scope);
			
			// exit init if related overlays not found
			if($jqsf($overlayLinks.attr('rel')).length == 0)
			{
				return false;
			}
			
			$overlayLinks.overlay({
				top: options.positionTop,
				left: 'center',
				expose: '#111',
				fixed: false,
				//closeOnClick: false,
				onBeforeLoad: function()
				{
					//$jqsf(document.body).addClass('disableOverflow');
					
					var _this = this,
						// grab wrapper element inside content 
						wrap = _this.getOverlay().find(".sf_contentWrap"),
						url = _this.getTrigger().attr("href"),
						type = "";
					
					_this.getOverlay().addClass('sf_loading');
					
					if(typeof(_this.getTrigger().data('overlay-type')) !== 'undefined')
					{
						type = _this.getTrigger().data('overlay-type');
						_this.getOverlay().addClass('sf_overlay_'+type);
					}
					
					if(type == 'messages')
					{
						$.sf_overlay('messages', _this, wrap, url);
					}
					else if(type == 'preview')
					{
						$.sf_overlay('preview', _this, wrap, url);
					}
					else if(type == 'upload')
					{
						options.langCloseConfirm = 'upload_close_confirm';
						
						// lazy loading uploadify and swfobject
						SF.Plugin.load('Uploadify', {rerun: true, manually: true, arguments: [_this, wrap, url]});
					}
					else if(type == 'scan')
					{
						options.langCloseConfirm = 'scan_close_confirm';
						
						$.sf_overlay('scan', _this, wrap, url);
					}
				},
				onLoad: function()
				{
					var _this = this;
					_this.getOverlay().removeClass('sf_loading');
				},
				onBeforeClose: function()
				{
					if( options.isCloseConfirmEnabled === true &&
						confirm(SF.Lang[ options.langCloseConfirm ]) === false)
					{
						return false; // abort closing
					}
					
					return true;
				},
				onClose: function()
				{
					$.sf_overlay('closeConfirm', false);
					options.langCloseConfirm = 'close_confirm';
					
					//$jqsf(document.body).removeClass('disableOverflow');
					
					this.getOverlay().attr('style', '').removeClass().addClass('sf_overlay');
					
					var wrap = this.getOverlay().find(".sf_contentWrap");
					wrap.attr('style', '').html('');
				}
			});
		},
		
		closeConfirm : function( enabled )
		{
			options.isCloseConfirmEnabled = enabled;
			
			if(enabled === true)
			{
				$(window).on('beforeunload', function() { return SF.Lang[ options.langCloseConfirm ]; });
			}
			else
			{
				$(window).off('beforeunload');
			}
		},
		
		messages : function(overlay, wrap, url)
		{
			wrap.load(
				url,
				null,
				function(responseText, textStatus, xhr)
				{
					if(textStatus == 'success')
					{
						SF.Plugin.load('HoverToSfActionButtons', {rerun: true, arguments: [wrap]});
						
						wrap.on('click', 'input[name=close]', function() {
							overlay.close();
							location.reload();
						});
					}
					else
					{
						overlay.close();
						alert(SF.Lang['error_loadinglayer']);
					}
				}
			);
		},
			
		scan : function(overlay, wrap, url)
		{
			wrap.load(
				url,
				null,
				function(responseText, textStatus, xhr)
				{
					if(textStatus == 'success')
					{
						SF.Plugin.load('HoverToSfActionButtons', {rerun: true, arguments: [wrap]});
		
						wrap.on('click', 'input[name=cancel_progress]', function() {
							overlay.close();
						});
						
						wrap.on('click', 'input[name=close_progress]', function() {
							$.sf_overlay('closeConfirm', false);
							
							overlay.close();
							
							location = $jqsf('#close_url', wrap).val();
						});
						
						wrap.on('click', 'input[name=close_settings]', function() {
							$.sf_overlay('closeConfirm', false);
							
							overlay.close();
						});
						
						wrap.on('submit', '#scan_settings_form', function() {
							$jqsf('input[name=cancel_progress]', wrap).show();
							$jqsf('input[name=close_progress]', wrap).hide();
							$jqsf('.overlay_scandirectory_settings', wrap).hide();
							$jqsf('.overlay_scandirectory_progress', wrap).show();
							
							$.sf_overlay('closeConfirm', true);
							
							var scanurl = $(this).attr('action'),
								updatethumbs = ($jqsf('#updatethumbs').is(':checked')),
								nosubdirscan = ($jqsf('#nosubdirscan').is(':checked'));
								
							$jqsf.ds.init(wrap, scanurl, updatethumbs, nosubdirscan);
							$jqsf.ds.start();
							
							return false;
						});
					}
					else
					{
						overlay.close();
						alert(SF.Lang['error_loadinglayer']);
					}
				}
			);
		},
		
		preview : function(overlay, wrap, url)
		{
			if(overlay.getTrigger().hasClass('overlay_image') == true)
			{
				overlay.getOverlay().addClass('overlay_image');
				
				var img = new Image();
				img.onload = function()
				{
					var document_width = $jqsf(document).width(); // cache original width
					
					// calculate new image size
					function resizeImg (width, height, maxw, maxh) {
						var maxRatio = maxw/maxh,
							ratio = width/height,
							ratio2= height/width;
						
						if (ratio > maxRatio){
							 // width is the problem
							if (width > maxw){
								img.width = maxw - parseInt(maxw/10, 10);
								img.height = (width > height) ? parseInt(img.width/ratio, 10) : parseInt(img.width*ratio2, 10);
							}
						} else {
							// width is the problem
							if (height > maxh){
								img.height = maxh - parseInt(maxh/10, 10);
								img.width = (width > height) ? parseInt(img.height*ratio, 10) : parseInt(img.height/ratio2, 10);
							}
						}
					};

					// size of image and viewport
					var width = this.width,
						height = this.height,
						windowWidth = $jqsf(window).width(),
						windowHeight = $jqsf(window).height() - options.positionTop;

					// autoResize is anabled
					if(options.imgAutoResize === true && ((width > windowWidth) || (height > windowHeight)))
					{
						resizeImg(width, height, windowWidth,  windowHeight);
					}
					
					// add with image tag
					wrap.html('<img id="overlay_img" style="display:none;" src="' + this.src + '" width="' + this.width + '" height="' + this.height + '" />');
					
					// center overlay with the new width
					var center_width = (document_width - this.width) * 0.5;
					center_width = (center_width < 0) ? 0 : center_width;
					
					overlay.getOverlay().animate(
						{
							height: this.height,
							width: this.width,
							left: center_width
						},
						'fast',
						'linear',
						function() {
							$jqsf('#overlay_img', overlay.getOverlay()).fadeIn();
						}
					);
				};
				img.onerror = function() 
				{
					alert(SF.Lang['error_nopreview']);
					overlay.close();
				};
				img.src = url;
			}
			else if(overlay.getTrigger().hasClass('overlay_text') == true)
			{
				overlay.getOverlay().addClass('overlay_text');
				
				$jqsf.get(
					url,
					null,
					function(data, textStatus, xhr)
					{
						if(xhr.getResponseHeader('content-type').search('text') > -1)
						{
							wrap.html(data);
							var windowHeight = $jqsf(window).height() - (options.positionTop*2);
							if(windowHeight < parseInt(wrap.css('height')))
							{
								wrap.css('height', windowHeight+'px');
							}
						}
						else
						{
							alert(SF.Lang['error_nopreview']);
							overlay.close();
						}
					},
					'text'
				);
			}
			else
			{
				alert(SF.Lang['error_nopreview']);
				overlay.close();
			}
		},
		
		upload : function(overlay, wrap, url)
		{
			wrap.load(
				url,
				null,
				function(responseText, textStatus, xhr)
				{
					//init uploadify
					if(textStatus == 'success' && $jqsf(document).uploadify)
					{
						SF.Plugin.load('HoverToSfActionButtons', {rerun: true, arguments: [wrap]});
						SF.Plugin.load('Treeview', {rerun: true, arguments: [wrap, {enableDirectoryChooser: true} ] });
						
						uploadify_options = {
							onSelectOnce: function(event, data)
							{
								$.sf_overlay('uploadToggleLinksAndMessages', wrap, data);
							},
							onCancel: function(event, queueID, fileObj, data)
							{
								$.sf_overlay('uploadToggleLinksAndMessages', wrap, data);
							},
							onClearQueue: function(event, data)
							{
								$.sf_overlay('uploadToggleLinksAndMessages', wrap, data);
							},
							onError: function(event, queueID, fileObj, errorObj)
							{
								//console.log(event, queueID, fileObj, errorObj);
							},
							onProgress: function(event, queueID, fileObj, data)
							{
								//console.log(event, queueID, fileObj, data);
							},
							onComplete: function(event, queueID, fileObj, response, data)
							{
								//console.log(event, queueID, fileObj, response, data);
								var resobj = eval('(' + response + ')'),
									p = $jqsf('.overlay_upload_finish').find('p.dummy:first')
										.clone()
										.appendTo($jqsf('.messages', '.overlay_upload_finish')),
									span_msg = $jqsf('.message', p),
									span_filename = $jqsf('.filename', p).html(fileObj.name);
								//console.log(resobj);
								
								if(typeof(resobj.error) !== 'undefined')
								{
									span_msg.html(resobj.error);
									p.addClass('errormsg');
								}
								else if(typeof(resobj.warning) !== 'undefined')
								{
									span_msg.html(resobj.warning);
									p.addClass('warning');
								}
								else if(typeof(resobj.ok) !== 'undefined')
								{
									span_msg.html(resobj.ok);
									p.addClass('ok');
								}
								
								p.removeClass('dummy');
							},
							onAllComplete: function(event, data)
							{
								$.sf_overlay('closeConfirm', false);
							
								$jqsf('.overlay_upload', wrap).hide();
								$jqsf('.overlay_upload_finish', wrap).show();
								//console.log(event, data);
							}
						};
						
						if(typeof($jqsf('.overlay_upload', wrap).data('options')) !== 'undefined')
						{
							uploadify_options = $jqsf.extend(uploadify_options, $jqsf('.overlay_upload', wrap).data('options'));
						}
						
						$jqsf('#uploadify', wrap).uploadify(uploadify_options);
						
						// does not work on flash with jQuery 1.4
						$jqsf('#uploadifyUploader', wrap).hover(
							function() {
								$jqsf(this).siblings('input[name=browse]').trigger('mouseenter');
							},
							function() {
								$jqsf(this).siblings('input[name=browse]').trigger('mouseleave');
							}
						);
						
						// for HTML upload
						$jqsf('#sf_upload').MultiFile({
							list: '#upload_file_queue',
							STRING: {
								remove: '<img src="'+uploadify_options.cancelImg+'" alt="x"/>'
							},
							afterFileAppend: function(element, value, master_element) {
								var data = {};
								data.fileCount = $jqsf('.MultiFile-label', '#upload_file_queue').length;
								$.sf_overlay('uploadToggleLinksAndMessages', wrap, data);
							},
							afterFileRemove: function(element, value, master_element) {
								var data = {};
								data.fileCount = $jqsf('.MultiFile-label', '#upload_file_queue').length;
								$.sf_overlay('uploadToggleLinksAndMessages', wrap, data);
							}
						});
						
						wrap.on('click', 'input[name=clear]', function() {
							$jqsf('#uploadify', wrap).uploadifyClearQueue();
						});
						
						wrap.on('click', 'input[name=upload]', function() {
							if($jqsf('.upload_mode_flash', wrap).is(':visible'))
							{
								$.sf_overlay('closeConfirm', true);
								
								$jqsf('#uploadify', wrap).uploadifySettings('folder', $jqsf('#destination', wrap).attr('value'));
								$jqsf('#uploadify', wrap).uploadifySettings('scriptData', {
									extractfiles: $jqsf('#extractfiles').is(':checked'),
									upload_mode: 'flash'
								});
			
								$jqsf('#uploadify', wrap).uploadifyUpload();
							}
							else
							{
								$.sf_overlay('closeConfirm', true);
								
								$jqsf('form[name=upload_html]').submit();
							}
						});
						
						wrap.on('click', 'input[name=cancel]', function() {
							overlay.close();
						});
						
						wrap.on('click', 'input[name=close]', function() {
							overlay.close();
							location.reload();
						});
						
						wrap.on('click', '.overlay_upload_finish .filter a', function()
						{
							$jqsf('.overlay_upload_finish .filter a.active', wrap).removeClass('active');
							$jqsf(this).addClass('active');
							
							var css = $jqsf(this).attr('rel'),
								messages = $jqsf('.overlay_upload_finish .messages', wrap);
							
							if(css == 'all')
							{
								$jqsf('p', messages).removeClass('hidden');
							}
							else
							{
								$jqsf('p', messages).addClass('hidden');
								$jqsf('p.'+css, messages).removeClass('hidden');
							}
							return false;
						});
						
						wrap.on('click', 'a.upload_mode', function()
						{
							var mode_to = $jqsf(this).attr('rel');
							$jqsf('.upload_mode_flash').hide();
							$jqsf('.upload_mode_html').hide();
							$jqsf('.upload_mode_'+mode_to).show();
							return false;
						});
						
						// no flash or too old version = only html upload
						if (swfobject.hasFlashPlayerVersion( "9.0.24" ) === false) {
							$jqsf('.upload_mode_flash').hide();
							$jqsf('.upload_mode_html').show();
							$jqsf('.upload_mode', wrap).hide();
						}
					}
					else
					{
						overlay.close();
						alert(SF.Lang['error_loadinglayer']);
					}
				}
			);
		},
		
		uploadToggleLinksAndMessages : function(scope, data)
		{
			if(data.fileCount > 0)
			{
				$jqsf('.empty_queue', scope).hide();
				$jqsf('.upload_mode', scope).hide();
				if(typeof(data.allBytesTotal) !== 'undefined')
				{
					$jqsf('.totalsize span', scope).html(SF.Utils.readablizeBytes(data.allBytesTotal));
					$jqsf('.totalsize', scope).show();
				}
			}
			else
			{
				$jqsf('.upload_mode', scope).show();
				$jqsf('.empty_queue', scope).show();
				$jqsf('.totalsize', scope).hide();
			}
			
		}
	};

	$.sf_overlay = function( method ) {
		if ( overlay_methods[method] ) {
			return overlay_methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return overlay_methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.sf_overlay' );
		}
	};
	
})( $jqsf );


/**
 * Directoryscan
 */
(function( $jqsf ){
	
	$jqsf.ds = {
		_url : '',
		_updatethumbs : false,
		_nosubdirscan : false,
		
		_wrap : null,
		_table : 'table.stats',
		_progressbar_value : '.progressbar_value',
		_progressbar_text : '.progressbar_text',
		_progressbar_percent : 0.00,
		
		_active_step : 0,
		_progressdata_default : {
			'directories_total' : 0,
			'directories_done' : -1,
			'directories_open' : 0,
			
			'files_total' : 0,
			'files_done' : 0,
			'files_open' : 0,
			
			'thumbs_total' : 0,
			'thumbs_done' : 0,
			'thumbs_open' : 0
		},
		_progressdata : {},
		
		_timeout : 250,
		_timer_start : '',
		_duration : 0,
		_timer_end : '',
		
		/**
		 * Initialize the Directoryscan. Call this function first.
		 */
		init : function(wrap, url, updatethumbs, nosubdirscan)
		{
			$jqsf.ds._wrap = wrap;
			$jqsf.ds._url = url;
			$jqsf.ds._updatethumbs = updatethumbs;
			$jqsf.ds._nosubdirscan = nosubdirscan;
			
			$jqsf.ds._progressdata = $jqsf.extend({}, $jqsf.ds._progressdata_default);
			
			//if($jqsf.ds._nosubdirscan == true)
			//{
				//$jqsf.ds._progressdata['directories_done'] = 0;
			//}
			
			$jqsf.ds._timer_start = new Date();
		},
		
		/**
		 * Start the directory scan. This calls itself after
		 * waiting the defined timeout.
		 */
		start : function()
		{
			$jqsf.getJSON(
				$jqsf.ds._url,
				{
					'updatethumbs': $jqsf.ds._updatethumbs,
					'nosubdirscan': $jqsf.ds._nosubdirscan
				},
				function(json) {
					if(typeof(json['active_step']) !== 'undefined')
					{
						$jqsf.ds._active_step = json['active_step'];
					}
					
					$jqsf.ds.updateProgressData( json );
					$jqsf.ds.updateTable();
					$jqsf.ds.updateProgressbar();
					
					if($jqsf.ds._active_step >= 3)
					{
						$.sf_overlay('closeConfirm', false);
						
						$jqsf($jqsf.ds._progressbar_value, $jqsf.ds._wrap).addClass('finish');
						$jqsf('input[name=cancel_progress]', $jqsf.ds._wrap).hide();
						$jqsf('input[name=close_progress]', $jqsf.ds._wrap).show();
					}
					
					// call as long new update uri is given
					if(typeof(json['next_url']) === 'string' && json['next_url'] != '')
					{
						$jqsf.ds._url = json['next_url'].replace(/&amp;/g, '&');
						
						// set timeout and call start function again
						setTimeout($jqsf.ds.start, $jqsf.ds._timeout);
					}
				}
			);
		},
		
		/**
		 * Update the progress statistic from the retrieved JSON data
		 */
		updateProgressData : function( jsondata )
		{
			var _progressdata = $jqsf.ds._progressdata;
			
			_progressdata['directories_total'] += jsondata['directories_found'];
			_progressdata['directories_done'] += jsondata['directories_done'];
			_progressdata['directories_open'] = _progressdata['directories_total'] - _progressdata['directories_done'];
			
			_progressdata['files_total'] += jsondata['files_found'];
			_progressdata['files_done'] += jsondata['files_done'];
			_progressdata['files_open'] = _progressdata['files_total'] - _progressdata['files_done'];
			
			if(jsondata['thumbs_found'] > _progressdata['thumbs_total'])
			{
				_progressdata['thumbs_total'] = jsondata['thumbs_found'];	
			}
			_progressdata['thumbs_done'] += jsondata['thumbs_done'];
			_progressdata['thumbs_open']  = _progressdata['thumbs_total'] - _progressdata['thumbs_done'];
			
			$jqsf.ds._progressdata = _progressdata;
			//console.log($jqsf.ds._progressdata);
		},
		
		/**
		 * Update the Table statistic
		 */
		updateTable : function()
		{
			var $table = $jqsf($jqsf.ds._table),
				$cell = null;
			if($table[0])
			{
				$jqsf.each($jqsf.ds._progressdata, function(field, value) {
					$cell = $jqsf('#'+field, $table);
					if($cell[0])
					{
						$cell.html(value);
					}
				});
			}
		},
		
		/**
		 * Calculate the progress in percent and update the progressbar
		 */
		updateProgressbar : function()
		{
			var _progressdata = $jqsf.ds._progressdata,
				_progressbar_percent = $jqsf.ds._progressbar_percent,
				$value = $jqsf($jqsf.ds._progressbar_value),
				$text = $jqsf($jqsf.ds._progressbar_text);
			
			switch($jqsf.ds._active_step)
			{
				case 0:
					_progressbar_percent = 0.00;
					if(_progressdata['directories_total'] > 0)
					{
						_progressbar_percent = 33.33 * (_progressdata['directories_done'] / _progressdata['directories_total'])
					}
					break;
					
				case 1:
					_progressbar_percent = 33.33;
					
					if(_progressdata['files_total'] > 0)
					{
						_progressbar_percent += 33.33 * (_progressdata['files_done'] / _progressdata['files_total']);
					}
					else if(_progressdata['files_done'] >= _progressdata['files_total'])
					{
						_progressbar_percent += 33.33;
					}
					else
					{
						_progressbar_percent += 0.00;
					}
					break;
					
				case 2:
					_progressbar_percent = 66.66;
					
					if(_progressdata['thumbs_total'] > 0)
					{
						_progressbar_percent += 33.34 * (_progressdata['thumbs_done'] / _progressdata['thumbs_total']);
					}
					else if(_progressdata['thumbs_done'] >= _progressdata['thumbs_total']  && _progressdata['thumbs_total'] > 0)
					{
						_progressbar_percent += 33.34;
					}
					else
					{
						_progressbar_percent += 0.00;
					}
					break;
				
				default:
					_progressbar_percent = 100.00;
					
					$jqsf.ds._timer_end = new Date();
					$jqsf.ds._duration = $jqsf.ds._timer_end.getTime() - $jqsf.ds._timer_start.getTime()
					$jqsf.ds._duration *= 0.001;
					
					//console.log($jqsf.ds._duration+' Sekunden');
					break;
			}
			
			_progressbar_percent = Math.round(_progressbar_percent);
			$jqsf.ds._progressbar_percent = _progressbar_percent;
			
			$text.html(_progressbar_percent+'%');
			
			if(_progressbar_percent == 0)
			{
				$value.css('width', _progressbar_percent+'%');
			}
			else
			{
				$value.animate({width: _progressbar_percent+'%'}, $jqsf.ds._timeout);
			}
			
			if(_progressbar_percent > 50)
			{
				$value.addClass('over_50_percent');
			}
			else
			{
				$value.removeClass('over_50_percent');
			}
		}
	};
	
})( $jqsf );