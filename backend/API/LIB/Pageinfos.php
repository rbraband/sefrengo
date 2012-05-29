<?php
$this->includeClass('LIB', 'PageinfosCustom');

/**
 * Contains often used page informations like pagetitetel, metatags, url,.. of all
 * pages in the current project in the current lang.
 */
class SF_LIB_Pageinfos extends SF_LIB_PageinfosCustom
{
    /**
     * Constructor
     */
    public function  __construct()
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