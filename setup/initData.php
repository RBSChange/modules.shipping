<?php
/**
 * @package modules.shipping
 */
class shipping_Setup extends object_InitDataSetup
{
	public function install()
	{
		$this->executeModuleScript('init.xml');
		
	}
}