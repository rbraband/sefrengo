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
			//'@url' => array()
		),
		'upload_file' => array(
			'type' => 'link',
			'text' => '#{ctr_name}_upload_file',
			'@url' => array(
				'area' => '{ctr_name}_upload_file'
			),
			'icon' => 'upl_upload.gif',
			'attributes' => ' class="action overlay" data-overlay-type="upload" rel="#sf_overlay"',
			'perm' => array(
				'obj' => 'file',
				'name' => 'upload',
				'id' => '{iddirectory}'
			)
		),
		'delimiter1' => array(
			'type' => 'delimiter',
			'attributes' => ' class="delimiter"'
		),
		'create_directory' => array(
			'type' => 'link',
			'text' => '#{ctr_name}_create_directory',
			'@url' => array(
				'area' => '{ctr_name}_create_directory',
				'parentid' => '{iddirectory}'
			),
			'icon' => 'but_newcat.gif',
			'perm' => array(
				'obj' => 'directory',
				'name' => 'create',
				'id' => '{iddirectory}'
			)
		),
		'delimiter2' => array(
			'type' => 'delimiter',
			'attributes' => ' class="delimiter"'
		),
		'create_file' => array(
			'type' => 'icon',
			'text' => '#{ctr_name}_create_file',
			'@url' => array(
				'area' => '{ctr_name}_create_file'
			),
			'icon' => 'but_newside.gif',
			'perm' => array(
				'obj' => 'file',
				'name' => 'upload',
				'id' => '{iddirectory}'
			)
		),
		'viewtype_compact' => array(
			'type' => 'icon',
			'text' => '#{ctr_name}_viewtype_compact',
			'@url' => array(
				'viewtype' => 'compact'
			),
			'icon' => 'but_viewcompact.gif'
		),
		'viewtype_detail' => array(
			'type' => 'icon',
			'text' => '#{ctr_name}_viewtype_detail',
			'@url' => array(
				'viewtype' => 'detail'
			),
			'icon' => 'but_viewdetail.gif'
		),
		'scan_directory' => array(
			'type' => 'icon',
			'text' => '#{ctr_name}_scan_current_directory',
			'@url' => array(
				'area' => 'ds',
				'dais' => 1, // disable (store) area in session
				'parent_area' => '{ctr_name}',
				'iddirectory' => '{iddirectory}',
				'id' => '',
			),
			'icon' => 'but_folder_scan.gif',
			'attributes' => ' class="action overlay" data-overlay-type="scan" rel="#sf_overlay"',
			'perm' => array(
				'obj' => 'directory',
				'name' => 'scan',
				'id' => '{iddirectory}'
			)
		),
		'edit_directory' => array(
			'type' => 'icon',
			'text' => '#{ctr_name}_edit_current_directory',
			'@url' => array(
				'area' => '{ctr_name}_edit_directory',
				'id' => '{iddirectory}'
			),
			'icon' => 'but_edit.gif',
			'perm' => array(
				'obj' => 'directory',
				'name' => 'edit',
				'id' => '{iddirectory}'
			)
		),
		'copy_directory' => array(
			'type' => 'icon',
			'text' => '#{ctr_name}_copy_current_directory',
			'@url' => array(
				'area' => '{ctr_name}_copy_directory',
				'id' => '{iddirectory}'
			),
			'icon' => 'but_copy_cat.gif',
			'perm' => array(
				'obj' => 'directory',
				'name' => 'copy',
				'id' => '{iddirectory}'
			)
		),
		'download_directory' => array(
			'type' => 'icon',
			'text' => '#{ctr_name}_download_current_directory',
			'@url' => array(
				'area' => '{ctr_name}_download_directory',
				'id' => '{iddirectory}',
				'iddirectory' => ''
			),
			'icon' => 'but_download.gif',
			'perm' => array(
				'obj' => 'directory',
				'name' => 'download',
				'id' => '{iddirectory}'
			)
		),
		'delete_directory' => array(
			'type' => 'icon',
			'text' => '#{ctr_name}_delete_current_directory',
			'@url' => array(
				'area' => '{ctr_name}_delete_directory',
				'id' => '{iddirectory}'
			),
			'icon' => 'but_delete.gif',
			'attributes' => 'onclick="return delete_confirm((typeof($jqsf) != \'undefined\') ? $jqsf(\'span.active\', \'#crumbs\').text() : \'\', \'delete_dir_confirm\')"',
			'perm' => array(
				'obj' => 'directory',
				'name' => 'delete',
				'id' => '{iddirectory}'
			)
		),
		'delimiter3' => array(
			'type' => 'delimiter',
			'attributes' => ' class="delimiter"'
		),
		'hidden_area' => array(
			'type' => 'hidden',
			'name' => 'area',
			'value' => '{ctr_name}_index'
		),
		'search' => array(
			'type' => 'search',
			'name' => 'searchterm'
		),
		/*'actionbox' => array(
			'type' => 'actionbox',
			'name' => 'mode',
			'values' => array(
				'' => 'Modus&hellip;',
				'normal' => 'Normaler Modus',
				'ftp_project' => 'FTP: Projekt',
				'ftp_basedir' => 'FTP: Basisverzeichnis',
			)
		)*/
	),
	
	'edit_file' => array(
		'form' => array(
			'type' => 'form',
			//'@url' => array()
		),
		'hidden_area' => array(
			'type' => 'hidden',
			'name' => 'area',
			'value' => '{ctr_name}_edit_file'
		),
		'hidden_iddirectory' => array(
			'type' => 'hidden',
			'name' => 'iddirectory',
			'value' => ''
		),
		'hidden_idfile' => array(
			'type' => 'hidden',
			'name' => 'id',
			'value' => ''
		),
		'actionbox' => array(
			'type' => 'actionbox',
			'name' => 'foreign_idlang',
			'values' => array(
				'' => '#{ctr_name}_switch_language'
			),
			'attributes' => ' class="actionbox"',
			'onchange_confirm' => '#{ctr_name}_switch_language_confirm'
		)
	),
	
	'edit_directory' => array(
		'form' => array(
			'type' => 'form',
			//'@url' => array()
		),
		'hidden_area' => array(
			'type' => 'hidden',
			'name' => 'area',
			'value' => '{ctr_name}_edit_directory'
		),
		'hidden_iddirectory' => array(
			'type' => 'hidden',
			'name' => 'id',
			'value' => ''
		),
		'actionbox' => array(
			'type' => 'actionbox',
			'name' => 'foreign_idlang',
			'values' => array(
				'' => '#{ctr_name}_switch_language'
			),
			'attributes' => ' class="actionbox"',
			'onchange_confirm' => '#{ctr_name}_switch_language_confirm'
		)
	)
);

/**
 * Configuration for the tables and forms
 * @var array
 */
$cfg['fields'] = array(
	
	'index_head' => array(
		
		'checkbox' => array (
			'fieldname' => array (
				'iddirectory',
				'idupl',
			),
			'lang_head' => array (
				'',
				''
			),
			'attributes_head' => 'width="13"'
		),
		
		'name' => array (
			'fieldname' => array (
				'name',
				'filename'
			),
			'lang_head' => array (
				'#{ctr_name}_directory_name',
				'#{ctr_name}_file_filename'
			),
			'multifieldseparator_head' => ' / ',
			'attributes_head' => 'width="25%"'
		),
		
		'title' => array (
			'fieldname' => array (
				'title',
				'description'
			),
			'lang_head' => array (
				'#{ctr_name}_file_title',
				'#{ctr_name}_file_description'
			),
			'multifieldseparator_head' => ' / ',
			'attributes_head' => ''
		),
		
		'lastmodified' => array (
			'fieldname' => array (
				'lastmodified'
			),
			'lang_head' => array (
				'#{ctr_name}_file_lastmodified'
			),
			'attributes_head' => 'width="160"'
		),
		
		'action' => array(
			'fieldname' => array (
				''
			),
			'lang_head' => array (
				'#{ctr_name}_action'
			),
			'attributes_head' => 'width="100" style="text-align:center;"'
		)
	),
	
	'index_directory' => array(
	
		'checkbox' => array (
			'fieldname' => array (
				'iddirectory'
			),
			'lang_head' => array (
				''
			),
			'attributes_body' => 'width="13" class="entry"',
			'renderer' => array(
				'classname' => 'TableCellRendererCheckbox',
				'chk_name' => 'd',
			)
		),
		
		'name' => array (
			'fieldname' => array (
				'name'
			),
			'lang_head' => array (
				'#{ctr_name}_directory_name'
			),
			'attributes_body' => 'width="25%" class="entry"',
			'renderer' => array(
				'classname' => 'TableCellRendererActionFm',
				'actions' => array(
					'icon' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_edit_directory',
						'@url' => array(
							'area' => '{ctr_name}_edit_directory',
							'id' => '{id}'
						),
						'icon' => 'but_folder_off2.gif',
						'perm' => 'edit',
						'attributes' => ' class="toolinfo" '
					),
					'link' => array(
						'render_as' => 'text',
						'suppress_title' => TRUE,
						'@url' => array(
							'area' => '{ctr_name}_index', 'iddirectory' => '{id}', 'searchterm' => ''
						),
						'attributes' => ' class="action dirname ajaxdeeplink" rel="rightpane" ',
						'perm' => 'view',
						'text' => 'USE_FIELDNAME', // constant
						'fieldname' => 'name'
					),
					'toolinfo' => array(
						'render_as' => 'toolinfotable',
						'table_class' => 'toolinfotablefolder',
						'lang_head' => '#{ctr_name}_directory_info',
						'created' => array (
							'text' => '#{ctr_name}_directory_created',
							'fieldname' => 'created',
							'format' => array(
								'pattern' => '{created:date} - {created:time} / {created:author}',
							)
						),
						'lastmodified' => array (
							'text' => '#{ctr_name}_directory_lastmodified',
							'fieldname' => 'lastmodified',
							'format' => array(
								'pattern' => '{lastmodified:date} - {lastmodified:time} / {lastmodified:author}',
							)
						)
					)
				),
			),
		),
		
		'description' => array (
			'fieldname' => array (
				'description'
			),
			'lang_head' => array (
				'#{ctr_name}_directory_description'
			),
			'attributes_body' => ' class="entry"'
		),
		
		'lastmodified' => array (
			'fieldname' => array (
				'lastmodified'
			),
			'lang_head' => array (
				'#{ctr_name}_directory_lastmodified'
			),
			'attributes_body' => 'width="160" class="entry"',
			'format' => array(
				'pattern' => '{lastmodified:date} - {lastmodified:time}'
			)
		),
		
		'action' => array(
			'fieldname' => array (
				''
			),
			'lang_head' => array (
				'#{ctr_name}_action'
			),
			'attributes_body' => 'width="100" class="entry nowrap" align="right"',
			'renderer' => array(
				'classname' => 'TableCellRendererActionFm',
				'actions' => array(
					'scan' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_scan_directory',
						'@url' => array(
							'area' => 'ds',
							'parent_area' => '{ctr_name}',
							'iddirectory' => '{id}',
							'id' => '',
						),
						'icon' => 'but_folder_scan.gif',
						'attributes' => ' class="overlay" data-overlay-type="scan" rel="#sf_overlay"',
						'perm' => 'scan'
					),
					'edit' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_edit_directory',
						'@url' => array(
							'area' => '{ctr_name}_edit_directory',
							'id' => '{id}'
						),
						'icon' => 'but_edit.gif',
						'perm' => 'edit'
					),
					'copy' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_copy_directory',
						'@url' => array(
							'area' => '{ctr_name}_copy_directory',
							'id' => '{id}'
						),
						'icon' => 'but_copy_cat.gif',
						'perm' => 'copy'
					),
					'download' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_download_directory',
						'@url' => array(
							'area' => '{ctr_name}_download_directory',
							'id' => '{id}',
							'iddirectory' => ''
						),
						'icon' => 'but_download.gif',
						'perm' => 'download'
					),
					'delete' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_delete_directory',
						'@url' => array(
							'area' => '{ctr_name}_delete_directory',
							'id' => '{id}'
						),
						'icon' => 'but_delete.gif',
						'attributes' => 'onclick="return delete_confirm((typeof($jqsf) != \'undefined\') ? $jqsf(this).parents(\'tr\').find(\'a.dirname\').text() : \'\', \'delete_dir_confirm\')"',
						'perm' => 'delete'
					)
				)
			)
		)
	),
	
	'index_files' => array(
	
		'checkbox' => array (
			'fieldname' => array (
				'idupl'
			),
			'lang_head' => array (
				''
			),
			'attributes_body' => 'width="13" class="entry"',
			'renderer' => array(
				'classname' => 'TableCellRendererCheckbox',
				'chk_name' => 'f',
			)
		),
		
		'filename' => array (
			'fieldname' => array (
				'filename'
			),
			'lang_head' => array (
				'#{ctr_name}_file_filename'
			),
			'attributes_body' => 'width="25%" class="entry"',
			'renderer' => array(
				'classname' => 'TableCellRendererActionFm',
				'actions' => array(
					'icon' => array(
						'render_as' => 'icon',
						// special implementation for area FM
						'get_filetype_icon' => TRUE,
						'@url' => array(
							'area' => '{ctr_name}_edit_file',
							'id' => '{id}'
						),
						'icon' => '{filetype}',
						'perm' => 'edit',
						'attributes' => ' class="toolinfo"',
					),
					'link' => array(
						'render_as' => 'text',
						'suppress_title' => TRUE,
						// special implementation for area FM
						'switch_url_by_filetype' => TRUE,
						'@url_preview' => array(
							'area' => '{ctr_name}_preview_file',
							'id' => '{id}',
							'iddirectory' => ''
						),
						'attributes_preview' => ' class="filename overlay" data-overlay-type="preview" rel="#sf_overlay" target="_blank"',
						'@url_download' => array(
							'area' => '{ctr_name}_download_file',
							'id' => '{id}',
							'iddirectory' => ''
						),
						'attributes_download' => ' class="filename"',
						'perm' => 'view',
						//'title' => '#{ctr_name}_preview_file',
						'text' => 'USE_FIELDNAME', // constant
						'fieldname' => 'filename'
					),
					'toolinfo' => array(
						'render_as' => 'toolinfotable',
						'table_class' => 'toolinfotableside',
						'lang_head' => '#{ctr_name}_file_info',
						'filesize' => array (
							'text' => '#{ctr_name}_file_filesize',
							'fieldname' => 'filesize',
							'format' => array(
								'classname' => 'TableCellFormatterFm',
								'pattern' => '{filesize:filesize}',
							)
						),
						'created' => array (
							'text' => '#{ctr_name}_file_created',
							'fieldname' => 'created',
							'format' => array(
								'pattern' => '{created:date} - {created:time} / {created:author}',
							)
						),
						'lastmodified' => array (
							'text' => '#{ctr_name}_file_lastmodified',
							'fieldname' => 'lastmodified',
							'format' => array(
								'pattern' => '{lastmodified:date} - {lastmodified:time} / {lastmodified:author}',
							)
						),
						'image_dimension' => array (
							'text' => '#{ctr_name}_file_image_dimension',
							'fieldname' => array (
								'pictwidth',
								'pictheight'
							),
							'format' => array(
								'pattern' => '{pictwidth} x {pictheight} px',
							)
						),
						'thumbnail' => array (
							'text' => '#{ctr_name}_file_thumbnail',
							'fieldname' => 'thumbnail',
							'thumb_index' => 0,
							'@thumb_url' => array(
								'area' => '{ctr_name}_preview_file',
								'id' => '{id}',
								'iddirectory' => '',
								'thumb' => '{thumb_index}'
							),
						),
					)
				),
			),
		),
		
		'title' => array (
			'fieldname' => array (
				'title',
				'description'
			),
			'lang_head' => array (
				'#{ctr_name}_file_title',
				'#{ctr_name}_file_description'
			),
			'multifieldseparator_head' => ' / ',
			'attributes_body' => ' class="entry"',
			'format' => array(
				'pattern' => '{title} {description}',
			)
		),
		
		'lastmodified' => array (
			'fieldname' => array (
				'lastmodified'
			),
			'lang_head' => array (
				'#{ctr_name}_file_lastmodified'
			),
			'attributes_body' => 'width="160" class="entry"',
			'format' => array(
				'pattern' => '{lastmodified:date} - {lastmodified:time}'
			)
		),
		
		'action' => array(
			'fieldname' => array (
				''
			),
			'lang_head' => array (
				'#{ctr_name}_action'
			),
			'attributes_body' => 'width="100" class="entry nowrap" align="right"',
			'renderer' => array(
				'classname' => 'TableCellRendererActionFm',
				'actions' => array(
					'preview' => array(
						'render_as' => 'icon',
						'title' => '#{ctr_name}_preview_file',
						// special implementation for area FM
						'switch_url_by_filetype' => TRUE,
						'@url_preview' => array(
							'area' => '{ctr_name}_preview_file',
							'id' => '{id}',
							'iddirectory' => ''
						),
						'attributes_preview' => ' class="overlay" data-overlay-type="preview" rel="#sf_overlay" target="_blank"',
						'@url_download' => array(
							'area' => '{ctr_name}_download_file',
							'id' => '{id}',
							'iddirectory' => ''
						),
						'attributes_download' => '',
						'icon' => 'but_preview.gif',
						'perm' => 'view'
					),
					'edit' => array(
						'render_as' => 'icon',
						'title' => '#{ctr_name}_edit_file',
						'@url' => array(
							'area' => '{ctr_name}_edit_file',
							'id' => '{id}'
						),
						'icon' => 'but_edit.gif',
						'perm' => 'edit'
					),
					'copy' => array(
						'render_as' => 'icon',
						'title' => '#{ctr_name}_copy_file',
						'@url' => array(
							'area' => '{ctr_name}_copy_file',
							'id' => '{id}'
						),
						'icon' => 'but_duplicate.gif',
						'perm' => 'copy'
					),
					'download' => array(
						'render_as' => 'icon',
						'title' => '#{ctr_name}_download_file',
						'@url' => array(
							'area' => '{ctr_name}_download_file',
							'id' => '{id}',
							'iddirectory' => ''
						),
						'icon' => 'but_download.gif',
						'perm' => 'download'
					),
					'delete' => array(
						'render_as' => 'icon',
						'title' => '#{ctr_name}_delete_file',
						'@url' => array(
							'area' => '{ctr_name}_delete_file',
							'id' => '{id}'
						),
						'icon' => 'but_delete.gif',
						'attributes' => 'onclick="return delete_confirm((typeof($jqsf) != \'undefined\') ? $jqsf(this).parents(\'tr\').find(\'a.filename\').text() : \'\', \'delete_file_confirm\')"',
						'perm' => 'delete'
					)
				)
			)
		)
	),
	
	'index_files_detail' => array(
		'thumbnail' => array (
			'text' => '#{ctr_name}_file_thumbnail',
			'fieldname' => 'thumbnail',
			'thumb_index' => 0,
			'@thumb_url' => array(
				'area' => '{ctr_name}_preview_file',
				'id' => '{id}',
				'iddirectory' => '',
				'thumb' => '{thumb_index}'
			),
		),
		'title' => array (
			'text' => '#{ctr_name}_file_title',
			'fieldname' => 'title',
		),
		'description' => array (
			'text' => '#{ctr_name}_file_description',
			'fieldname' => 'description',
		),
		'filename' => array (
			'text' => '#{ctr_name}_file_filename',
			'fieldname' => 'filename',
		),
		'filesize' => array (
			'text' => '#{ctr_name}_file_filesize',
			'fieldname' => 'filesize',
			'format' => array(
				'classname' => 'TableCellFormatterFm',
				'pattern' => '{filesize:filesize}'
			)
		),
		'image_dimension' => array (
			'text' => '#{ctr_name}_file_image_dimension',
			'fieldname' => array (
				'pictwidth',
				'pictheight'
			),
			'format' => array(
				'pattern' => '{pictwidth:asInt} x {pictheight:asInt} px',
			)
		),
		'created' => array (
			'text' => '#{ctr_name}_file_created',
			'fieldname' => 'created',
			'format' => array(
				'pattern' => '{created:date} - {created:time} / {created:author}'
			)
		),
		'lastmodified' => array (
			'text' => '#{ctr_name}_file_lastmodified',
			'fieldname' => 'lastmodified',
			'format' => array(
				'pattern' => '{lastmodified:date} - {lastmodified:time} / {lastmodified:author}'
			)
		),
	),
	
	'index_footer' => array(
		
		'action' => array(
			'attributes_body' => 'width="100" class="entry nowrap" align="right"',
			'renderer' => array(
				'classname' => 'TableCellRendererActionFm',
				'actions' => array(
					'copy' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_copy_multiple',
						'@url' => array(
							'area' => '{ctr_name}_copy_multiple',
						),
						'icon' => 'but_duplicate.gif',
						'attributes' => 'rel="d[],f[]" class="add_chk_to_url"'
					),
					'download' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_download_multiple',
						'@url' => array(
							'area' => '{ctr_name}_download_multiple'
						),
						'icon' => 'but_download.gif',
						'attributes' => 'rel="d[],f[]" class="add_chk_to_url"'
					),
					'delete' => array(
						'render_as' => 'icon',
						'text' => '#{ctr_name}_delete_multiple',
						'@url' => array(
							'area' => '{ctr_name}_delete_multiple',
						),
						'icon' => 'but_delete.gif',
						'attributes' => 'rel="d[],f[]" onclick="if(delete_confirm(\'\', \'delete_multi_confirm\')) { SF.Plugin.get(\'AddCheckboxesToUrl\').append(this); } return false;"'
					)
				)
			)
		)
	),
	
	'edit_directory' => array(

		'id' => array(
			'type' => 'hidden',
			'title' => '#{ctr_name}_directory_id',
			'val' => '',
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),
		
		'parentid' => array(
			'type' => 'hidden',
			'title' => '#{ctr_name}_directory_parentid',
			'val' => '',
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),
		
		'name' => array(
			'type' => 'text',
			'title' => '#{ctr_name}_directory_name',
			'val' => '',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array(
				'directoryname' => array('note' => '#{ctr_name}_validation_error_directoryname'),
				'required' => array('note' => '#{ctr_name}_validation_error_required')
			)
		),
		
		'rights' => array(
			'type' => 'rightspanel',
			'title' => '#{ctr_name}_directory_edit_rights',
			'val' => '',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array(),
			'panel_config' => array()
		),
		
		'description'  => array (
			'type' => 'textarea',
			'title' => '#{ctr_name}_directory_description',
			'val' => '',
			'attributes' => array(
				'class'=>'w800 sans-serif'
			),
			'validation' => array()
		),
		
		'created'  => array (
			'type' => 'infofield',
			'title' => '#{ctr_name}_directory_created',
			'val' => '-',
			'format' => '{date} - {time} / {author}',
			'attributes' => array(
				'class'=>'w800'
			),
			'validation' => array()
		),
		
		'lastmodified'  => array (
			'type' => 'infofield',
			'title' => '#{ctr_name}_directory_lastmodified',
			'val' => '-',
			'format' => '{date} - {time} / {author}',
			'attributes' => array(
				'class'=>'w800'
			),
			'validation' => array()
		),
	),
	
	'copy_directory' => array(

		'id' => array(
			'type' => 'hidden',
			'title' => '#{ctr_name}_directory_id',
			'val' => '',
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),

		'source' => array(
			'type' => 'info',
			'title' => '#{ctr_name}_directory_source',
			'val' => ''
		),
		
		'name' => array(
			'type' => 'text',
			'title' => '#{ctr_name}_directory_new_name',
			'val' => '',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array(
				'directoryname' => array('note' => '#{ctr_name}_validation_error_directoryname'),
				'required' => array('note' => '#{ctr_name}_validation_error_required')
			)
		),
		
		'parentid' => array(
			'type' => 'directorychooser',
			'title' => '#{ctr_name}_directory_destination',
			'val' => '',
			'attributes' => array(
				'class' => 'directorychooser'
			),
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),
		
		'move'  => array (
			'type' => 'checkbox',
			'title' => '#{ctr_name}_directory_move',
			'val' => array(
				'1' => '#{ctr_name}_directory_move_label',
			),
			'checked' => array(
				'1' => TRUE,
			),
			'validation' => array()
		)
	),
	
	
	'edit_file' => array(

		'id' => array(
			'type' => 'hidden',
			'title' => '#{ctr_name}_file_id',
			'val' => '',
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),
		
		'iddirectory' => array(
			'type' => 'hidden',
			'title' => '#{ctr_name}_file_iddirectory',
			'val' => '',
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),
		
		'filename' => array(
			'type' => 'text',
			'title' => '#{ctr_name}_file_filename',
			'val' => '',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array(
				'filename' => array('note' => '#{ctr_name}_validation_error_filename'),
				'required' => array('note' => '#{ctr_name}_validation_error_required')
			)
		),
		
		'rights' => array(
			'type' => 'rightspanel',
			'title' => '#{ctr_name}_file_edit_rights',
			'val' => '',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array(),
			'panel_config' => array()
		),
		
		'title' => array(
			'type' => 'text',
			'title' => '#{ctr_name}_file_title',
			'val' => '',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array()
		),
		
		'description'  => array (
			'type' => 'textarea',
			'title' => '#{ctr_name}_file_description',
			'val' => '',
			'attributes' => array(
				'class'=>'w800 sans-serif'
			),
			'validation' => array()
		),
		
		'content'  => array (
			'type' => 'editor',
			'title' => '#{ctr_name}_file_content',
			'editor_type' => 'CodeMirrorEditor',
			'editor_instance' => null,
			'editor_config' => array(
				//'filetypes' => array('txt'),
				//'options' => array(
				//	'theme' => 'default'
				//)
			),
			'editor_attributes' => array(
				'id' => 'filecontent',
				'class' => 'w800',
				'style' => 'height: 250px;'
			),
			'val' => '',
			'attributes' => array(),
			'validation' => array()
		),
		
		'created'  => array (
			'type' => 'infofield',
			'title' => '#{ctr_name}_file_created',
			'val' => '-',
			'format' => '{date} - {time} / {author}',
			'attributes' => array(
				'class'=>'w800'
			),
			'validation' => array()
		),
		
		'lastmodified'  => array (
			'type' => 'infofield',
			'title' => '#{ctr_name}_file_lastmodified',
			'val' => '-',
			'format' => '{date} - {time} / {author}',
			'attributes' => array(
				'class'=>'w800'
			),
			'validation' => array()
		),
	),
	
	'copy_file' => array(

		'id' => array(
			'type' => 'hidden',
			'title' => '#{ctr_name}_file_id',
			'val' => '',
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),

		'source' => array(
			'type' => 'info',
			'title' => '#{ctr_name}_file_source',
			'val' => ''
		),
		
		'filename' => array(
			'type' => 'text',
			'title' => '#{ctr_name}_file_new_filename',
			'val' => '',
			'attributes' => array(
				'class' => 'w800'
			),
			'validation' => array(
				'filename' => array('note' => '#{ctr_name}_validation_error_filename'),
				'required' => array('note' => '#{ctr_name}_validation_error_required')
			)
		),
		
		'iddirectory' => array(
			'type' => 'directorychooser',
			'title' => '#{ctr_name}_file_iddirectory',
			'val' => '',
			'attributes' => array(
				'class' => 'directorychooser'
			),
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),
		
		'move'  => array (
			'type' => 'checkbox',
			'title' => '#{ctr_name}_file_move',
			'val' => array(
				'1' => '#{ctr_name}_file_move_label'
			),
			'checked' => array(
				'1' => FALSE
			),
			'validation' => array()
		)
	),
	
	'copy_multiple' => array(
		
		'directories' => array(
			'type' => 'hidden',
			'title' => '#{ctr_name}_directory_id',
			'val' => ''
		),
		
		'files' => array(
			'type' => 'hidden',
			'title' => '#{ctr_name}_file_id',
			'val' => ''
		),

		'selection' => array(
			'type' => 'info',
			'title' => '#{ctr_name}_multiple_selection',
			'val' => ''
		),
		
		'destination' => array(
			'type' => 'directorychooser',
			'title' => '#{ctr_name}_multiple_destination',
			'val' => '',
			'attributes' => array(
				'class' => 'directorychooser'
			),
			'validation' => array(
				'naturalNoZero' => array('note' => '#{ctr_name}_validation_error_numericNoZero')
			)
		),
		
		'move'  => array (
			'type' => 'checkbox',
			'title' => '#{ctr_name}_multiple_move',
			'val' => array(
				'1' => '#{ctr_name}_multiple_move_label'
			),
			'checked' => array(
				'1' => FALSE
			),
			'validation' => array()
		)
	),
	
);
?>