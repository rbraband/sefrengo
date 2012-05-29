<?php
/* 
 * Storagecontainer for module informations. Can be used in the frontendoutput
 * of a module.
 */
class SF_LIB_Modinfo extends SF_LIB_ApiObject
{

	/**
	 * Local settings
	 * @var cfg_lib
	 */
	protected $defaults = array(
							'is_editiable' => FALSE,
							'is_first_entry' => TRUE,
							'is_last_entry' => FALSE,
							'entry_nr' => 1,
							'container_id' => 0,
							'container_title' => '',
							'container_tag' => '',
							'mod_key' => '',
							'mod_values' => array(),
							);

	/**
	 * Construktor
	 */
	public function __construct()
	{
		//set singelton
        $this->_API_setObjectIsSingleton(true);
	}

	public function getIsEditable() { return $this->getMetaVal('is_editable'); }
	public function getIsFirstEntry() { return $this->getMetaVal('is_first_entry'); }
	public function getIsLastEntry() { return $this->getMetaVal('is_last_entry'); }
	public function getEntryNr() { return $this->getMetaVal('entry_nr'); }
	public function getIdContainer() { return $this->getMetaVal('container_id'); }
	public function getContainerTitle() { return $this->getMetaVal('container_title'); }
	public function getContainerTag() { return $this->getMetaVal('container_tag'); }
	public function getModKey() { return $this->getMetaVal('mod_key'); }

	public function getVal($key)
	{
		if (! array_key_exists($key, $this->defaults['mod_values']))
		{
			return FALSE;
		}

		return $this->defaults['mod_values'][$key];
	}
	
	
	public function getAllVals()
	{
		return $this->defaults['mod_values'];
	}

	public function getMetaVal($key)
	{
		if (! array_key_exists($key, $this->defaults))
		{
			return FALSE;
		}

		return $this->defaults[$key];
	}

	public function setVal($key, $val)
	{
		if (! array_key_exists($key, $this->defaults))
		{
			return FALSE;
		}

		$this->defaults[$key] = $val;

		return TRUE;
	}
}
?>
