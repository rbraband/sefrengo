<?php
/**
 * Configuration for the toolbar
 * The order of the arrays reprensents the view order.
 * @var array
 */
$cfg['toolbar'] = array(
	'index' => array(
		'form' => array(
			'type' => 'form',
			//'url' => array()
		),
		'search' => array(
			'type' => 'adv_search',
			'name' => 'searchterm',
			'values' => array(
				// @see define as fields in $cfg['fields']['toolbar_search']
			),
		),
		'area' => array(
			'type' => 'actionbox',
			'name' => 'area',
			'values' => array(
				'' => '#{ctr_name}_area',
				'#{ctr_name}_index' => '#{ctr_name}_log_db',
				'#{ctr_name}_logfile_be' => '#{ctr_name}_log_be',
				'#{ctr_name}_logfile_fe' => '#{ctr_name}_log_fe',
			),
			'attributes' => ' class="actionbox"',
		),
	),
);

/**
 * Configuration for the tables and forms
 * @var array
 */
$cfg['fields'] = array(

	'toolbar_search' => array(
		'searchterm' => array(
			'type' => 'none', // just added to get the title
			'title' => '#{ctr_name}_searchterm',
		),
		'period' => array(
			'type' => 'datepicker',
			'title' => '#{ctr_name}_period',
			'val' => array(
				'from' => '',
				'to' => ''
			),
			'label' => array(
				'from' => '#{ctr_name}_period_from',
				'to' => '#{ctr_name}_period_to',
			),
			'attributes' => array(
				'from' => array(
					'class' => 'sfDatepicker sfDatepickerFrom',
					'data-datepicker-options' => array(
						'connectedDatepickerId' => 'period_to' // fieldname + '_to'
					)
				),
				'to' => array(
					'class' => 'sfDatepicker sfDatepickerTo',
					'data-datepicker-options' => array(
						'connectedDatepickerId' => 'period_from' // fieldname + '_to'
					)
				)
			),
			'validation' => array()
		),
		'priority' => array(
			'type' => 'select',
			'title' => '#{ctr_name}_priority',
			'val' => array(
				//'value' => 'text',
			),
			//'selected' => array('val_key1', 'val_key2'),
			'attributes' => array(
				'size' => 11,
				'multiple' => 'multiple',
				'style' => 'width: 100%'
			),
			'validation' => array()
		),
		'type' => array(
			'type' => 'select',
			'title' => '#{ctr_name}_type',
			'val' => array(
				//'value' => 'text',
			),
			//'selected' => array('val_key1', 'val_key2'),
			'attributes' => array(
				'size' => 11,
				'multiple' => 'multiple',
				'style' => 'width: 100%'
			),
			'validation' => array()
		),
		'created_author' => array(
			'type' => 'text',
			'title' => '#{ctr_name}_author',
			'val' => '',
			'attributes' => array(
				'style' => 'width: 96%'
			),
			'validation' => array()
		),
		'is_backend' => array(
			'type' => 'radio',
			'title' => '#{ctr_name}_is_backend_logs',
			'val' => array(
				'-1' => '#{ctr_name}_is_backend_default',
				'0' => '#{ctr_name}_is_backend_hide',
				'1' => '#{ctr_name}_is_backend_show',
			),
			'checked' => '-1',
			'attributes' => array(),
			'validation' => array()
		),
	),
	
	'index_head' => array(
		
		'checkbox' => array (
			'fieldname' => array (
				'idlog',
			),
			'lang_head' => array (
				'',
			),
			'attributes_head' => 'width="13"'
		),
		
		'date' => array (
			'fieldname' => array (
				'created',
			),
			'lang_head' => array (
				'#{ctr_name}_created',
			),
			'attributes_head' => 'width="150"'
		),
		
		'priority' => array (
			'fieldname' => array (
				'priorityname',
			),
			'lang_head' => array (
				'#{ctr_name}_priority',
			),
			'attributes_head' => 'width="100"'
		),
		
		'type' => array (
			'fieldname' => array (
				'type',
			),
			'lang_head' => array (
				'#{ctr_name}_type',
			),
			'attributes_head' => 'width="100"'
		),
		
		'message' => array (
			'fieldname' => array (
				'message'
			),
			'lang_head' => array (
				'#{ctr_name}_message'
			),
			'attributes_head' => ''
		),
		
		'author' => array (
			'fieldname' => array (
				'created_author'
			),
			'lang_head' => array (
				'#{ctr_name}_author'
			),
			'attributes_head' => 'width="180"'
		),
		
		'backend' => array (
			'fieldname' => array (
				'is_backend'
			),
			'lang_head' => array (
				'#{ctr_name}_is_backend'
			),
			'attributes_head' => 'width="70" style="text-align:center;"'
		),
	),
	
	'index_body' => array(
	
		'checkbox' => array (
			'fieldname' => array (
				'idlog'
			),
			'lang_head' => array (
				''
			),
			'attributes_body' => 'width="13" class="entry"',
			'renderer' => array(
				'classname' => 'TableCellRendererCheckbox',
				'chk_name' => 'l',
			)
		),
		
		'date' => array (
			'fieldname' => array (
				'created',
			),
			'lang_head' => array (
				'#{ctr_name}_created',
			),
			'attributes_body' => 'width="150" class="entry"',
			'format' => array(
				'pattern' => '{created:date} - {created:time}',
			)
		),
		
		'priority' => array (
			'fieldname' => array (
				'priorityname',
			),
			'lang_head' => array (
				'#{ctr_name}_priority',
			),
			'attributes_body' => 'width="100" class="entry"'
		),
		
		'type' => array (
			'fieldname' => array (
				'type',
			),
			'lang_head' => array (
				'#{ctr_name}_type',
			),
			'attributes_body' => 'width="100" class="entry"'
		),
		
		'message' => array (
			'fieldname' => array (
				'message',
				'param'
			),
			'lang_head' => array (
				'#{ctr_name}_message'
			),
			'attributes_body' => 'class="entry"',
			'format' => array(
				'classname' => 'TableCellFormatterLogs',
				'pattern' => '{message:lang}' //'{message:lang}<br /><pre class="params">{param:tostring}</pre>'
			)
		),
		
		'author' => array (
			'fieldname' => array (
				'created_author'
			),
			'lang_head' => array (
				'#{ctr_name}_author'
			),
			'attributes_body' => 'width="180" class="entry"'
		),
		
		'backend' => array (
			'fieldname' => array (
				'is_backend'
			),
			'lang_head' => array (
				'#{ctr_name}_is_backend'
			),
			'attributes_body' => 'width="70" class="entry" style="text-align:center;"'
		),
	),
	
	'index_footer' => array(
		
		'action' => array(
			'attributes_body' => 'width="70" class="entry nowrap" align="right"',
			'renderer' => array(
				'classname' => 'TableCellRendererActionFm',
				'actions' => array(
					'delete' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_delete_multiple',
						'@url' => array(
							'area' => '{ctr_name}_delete_multiple',
						),
						'icon' => 'but_delete.gif',
						'attributes' => 'rel="l[]" onclick="if(delete_confirm(\'\', \'delete_multi_confirm\')) { SF.Plugin.get(\'AddCheckboxesToUrl\').append(this); } return false;"'
					)
				)
			)
		)
	),
	
	'view_logfile' => array(
		
		'path'  => array (
			'type' => 'infofield',
			'title' => '#{ctr_name}_file_path',
			'val' => '-',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array()
		),
		
		'content'  => array (
			'type' => 'textarea',
			'title' => '#{ctr_name}_file_content',
			'val' => '',
			'attributes' => array(
				'cols' => '100',
				'rows' => '25',
				'class' => 'w800',
			),
			'validation' => array()
		),
		
		'filesize'  => array (
			'type' => 'infofield',
			'title' => '#{ctr_name}_file_size',
			'val' => '-',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array()
		),
		
		'lastmodified'  => array (
			'type' => 'infofield',
			'title' => '#{ctr_name}_file_lastmodified',
			'val' => '-',
			'format' => '{date} - {time}',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array()
		),
		
		'delete_check'  => array (
			'type' => 'checkbox',
			'title' => '#{ctr_name}_file_delete',
			'val' => array(
				'1' => '#{ctr_name}_file_delete_question',
			),
			'checked' => array(
				'1' => FALSE,
			),
			'attributes' => array(),
			'validation' => array()
		),
		
		'actionbuttons' => array(
			'type' => 'actionbuttons',
			'title' => '',
			'buttons' => array(
				'deletebutton' => array(
					'type' => 'submit',
					'name' => '',
					'val' => '#gen_delete',
					'attributes' => array(
						'class' => 'sf_buttonActionCancel',
					)
				)
			)
		)
	),
);
?>