<?php
$this->includeClass('LIB', 'CatinfosCustom');

class SF_LIB_Catinfos extends SF_LIB_CatinfosCustom
{    
    public function __construct()
	{
		parent::__construct();

		$this->_API_setObjectIsSingleton(true);

		$is_backend = $this->cfg->env('is_backend');
		$idlang = $this->cfg->env('idlang');

		$this->setIdlang($idlang);
		if ($is_backend)
		{
			$this->setCheckBackendperms(true);
		}
		else
		{
			$this->setCheckFrontendperms(true);
		}

        $this->generate();
    }
    
} 

?>