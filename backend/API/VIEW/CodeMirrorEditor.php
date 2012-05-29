<?php

$this->includeClass('VIEW', 'AbstractItemEditor');

class SF_VIEW_CodeMirrorEditor extends SF_VIEW_AbstractItemEditor
{
	/**
	 * CodeMirror supproted modes with file- and MIME type 
	 */
	private $modes = array(
		'null' => array(
			'filetypes' => array('txt'),
			'mimetypes' => array('text/plain'),
		),
		'css' => array(
			'filetypes' => array('css'),
			'mimetypes' => array('text/css'),
		),
		'htmlmixed' => array(
			'filetypes' => array('htm', 'html'),
			'mimetypes' => array('text/html'),
		),
		'javascript' => array(
			'filetypes' => array('js', 'json'),
			'mimetypes' => array('text/javascript', 'application/json'),
		),
		'less' => array(
			'filetypes' => array('less'),
			'mimetypes' => array('text/css'),
		),
		'perl' => array(
			'filetypes' => array('pl'),
			'mimetypes' => array('application/x-perl'),
		),
		'php' => array(
			'filetypes' => array('php'),
			'mimetypes' => array('application/x-httpd-php', 'text/x-php'),
		),
		'plsql' => array(
			'filetypes' => array('sql'),
			'mimetypes' => array('text/x-plsql'),
		),
		'python' => array(
			'filetypes' => array('py'),
			'mimetypes' => array('text/x-python'),
		),
		'sparql' => array(
			'filetypes' => array('sparql'),
			'mimetypes' => array('application/x-sparql-query'),
		),
		'xml' => array(
			'filetypes' => array('xml'),
			'mimetypes' => array('application/xml'),
		),
	);
	
	/**
	 * Default configuration for the editor
	 * Can be overwritten in the field configuration
	 * @var array
	 */
	protected $config = array(
		// editor works with this file extensions 
		'filetypes' => array(),
		
		// configuration to pass via data-option property
		// @see http://codemirror.net/manual.html#config
		'options' => array(
			//'value' => '',
			'mode' => 'null', // mode-name or MIME type
			//'theme' => 'default',
			//'indentUnit' => 2,
			//'indentWithTabs' => FALSE,
			//'tabMode' => 'classic', // 'classic', 'shift', 'indent', 'default'
			//'enterMode' => 'indent', // 'indent', 'keep', 'flat'
			//'electricChars' => TRUE,
			'lineNumbers' => TRUE,
			//'firstLineNumber' => 1,
			//'gutter' => TRUE,
			//'readOnly' => FALSE,
			//'matchBrackets' => TRUE,
			//'undoDepth' => 40,
			//'tabindex' => 1, // HTML tabindex attribute
			
			'searchreplace' => FALSE
		)
	);
	
	/**
	 * Stores the new content of the item
	 * @var string
	 */
	private $content = '';
	
	/**
	 * Stores an backup of the content.
	 * It's mostly the original file content,
	 * before saving the new one. 
	 * @var string
	 */
	private $backupcontent = '';
	
	/**
	 * Constructor retrieves the backend language 
	 * to display edit area accordingly.
	 */
	public function __construct()
	{
		parent::__construct();
		
		// extract filetypes for supported modes 
		foreach($this->modes as $mode => $detail)
		{
			$this->config['filetypes'] = array_merge($this->config['filetypes'], $detail['filetypes']);
		}
	}
	
	/**
	 * Set the item that is modified by this editor.
	 * The function is extended to retrieve the file content
	 * from request var or from file it's self.
	 * @param SF_MODEL_FileSqlItem $item
	 */
	public function setItem($item)
	{
		if($item == null || $this->isItemAllowed($item) == FALSE)
		{
			return FALSE;
		}
		
		$bool = parent::setItem($item);
		
		$this->item->addItemEditor($this->editor_name, $this);
		
		$this->content = $this->req->req($this->editor_name, $this->item->getContent());
		
		return $bool;
	}
	
	/**
	 * Checks if the item fullfills the object type.
	 * For this editor the item must be an instance of
	 * SF_MODEL_FileSqlItem. 
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#isItemAllowed($item)
	 * @param SF_MODEL_FileSqlItem $item
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function isItemAllowed($item)
	{
		return ($item instanceof SF_MODEL_FileSqlItem);
	}
	
	/**
	 * Checks if editor is available for the given item.
	 * In this case the file extension must accord to the 
	 * editor configuration. 
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#isEditorAvailable()
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function isEditorAvailable()
	{
		if($this->item == null)
		{
			return FALSE;
		}
		
		$filename = $this->item->getField('filename');
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$extension = $fsm->getPathinfo($filename, 'extension');
		$mode = $this->config['options']['mode'];
		
		// allow editor for new files if set in config filetypes
		if($filename == FALSE && in_array('', $this->config['filetypes']))
		{
			return TRUE;
		}
		else if($this->item != null && in_array($extension, $this->config['filetypes']) == TRUE)
		{
			foreach($this->modes as $mode => $detail)
			{
				if(in_array($extension, $detail['filetypes']) == TRUE)
				{
					$this->config['options']['mode'] = $mode;
					break;
				}
			}
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Build the editor and return the HTML output to include
	 * in the page.
	 * To convert the attributes into HTML attributes use the
	 * HTML Helper Library.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#getEditor($attributes = array())
	 * @param array $attributes
	 * @return string Returns the output of the editor
	 */
	public function getEditor($attributes = array())
	{
		if($this->isEditorAvailable() == FALSE)
		{
			return '';
		}
		
		$cfg = sf_api('LIB', 'Config');
		$enable_code_editor = (bool)$cfg->cms('enable_code_editor');
		unset($cfg);
		
		if($enable_code_editor == TRUE)
		{
			// Add JavaScript files to header
			$header = sf_api('VIEW', 'Header');
			$header->addJsFiles(array('tpl/{SKIN}/js/editor/codemirror/lib/codemirror-compressed.js'), TRUE);
			$header->addCssFiles(array('tpl/{SKIN}/js/editor/codemirror/lib/codemirror.css'), TRUE);
			$header->addCssFiles(array('tpl/{SKIN}/js/editor/codemirror/theme/default.css'), TRUE);
			$header->addJsFiles(array('tpl/{SKIN}/js/editor/codemirror/sefrengo/jquery.sf_codemirror.js'), TRUE);
			$header->addCssFiles(array('tpl/{SKIN}/js/editor/codemirror/sefrengo/sf_codemirror.css'), TRUE);
			
			$options = array();
			if(array_key_exists('options', $this->config))
			{
				$options = $this->config['options'];
				
				if(array_key_exists('theme', $options) == TRUE)
				{
					$header->addCssFiles(array('tpl/{SKIN}/js/editor/codemirror/theme/'.$options['theme'].'.css'), TRUE);
				}
			}
			
			$attributes['name'] = $this->editor_name;
			$attributes['class'] .= ' codemirror';
			$attributes['data-options'] = $options;
		}
		
		$html = sf_api('LIB', 'HtmlHelper');
		
		$editor = '<textarea '.$html->attributesArrayToHtmlString($attributes).'>'.$this->content.'</textarea>';
		
		if( $enable_code_editor == TRUE &&
			array_key_exists('searchreplace', $options) == TRUE && $options['searchreplace'] == TRUE)
		{
			$editor .= '<button type="button" class="'.$attributes['id'].'_search_btn">Search</button> ';
			$editor .= '<input type="text" class="'.$attributes['id'].'_query_txt" value="" /> or ';
			$editor .= '<button type="button" class="'.$attributes['id'].'_replace_btn">replace</button> it by ';
			$editor .= '<input type="text" class="'.$attributes['id'].'_replace_txt" value="" />';
		}
		
		return $editor;
	}
	
	/**
	 * Transaction begin checks if the content
	 * from file is different to new content.
	 * If does so, backup the content and check
	 * if writeable.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemBegin()
	{
		if($this->isEditorAvailable() == FALSE)
		{
			return TRUE;
		}
		
		// replace content if different
		if(md5($this->content) != md5_file( $this->item->getPath() ))
		{
			// backup the whole current content, in case to restore
			$this->backupcontent = $this->item->getContent(filesize($this->item->getPath()), TRUE);
			
			if($this->content != '' && $this->backupcontent != '')
			{
				return is_writable($this->item->getPath());
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Writes the new content to file and
	 * sets it as new item content.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemCommit()
	{
		if($this->isEditorAvailable() == FALSE)
		{
			return TRUE;
		}
		
		// replace content if different
		if(md5($this->content) != md5($this->backupcontent))
		{
			$fsm = sf_api('LIB', 'FilesystemManipulation');
			
			// create a new empty file if not exists
			if($this->content == '' && $this->backupcontent == '')
			{
				return $fsm->createEmptyFile($this->item->getPath());
			}
			else if($fsm->writeContentToFile($this->item->getPath(), $this->content) == TRUE)
			{
				// refresh item content
				$this->item->setContent($this->content);
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Tries to write the backup content to file.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemRollback()
	{
		if($this->isEditorAvailable() == FALSE)
		{
			return TRUE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		if($fsm->writeContentToFile($this->item->getPath(), $this->backupcontent) == TRUE)
		{
			$this->item->setContent($this->backupcontent);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
}
?>