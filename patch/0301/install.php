<?php
/**
 * shipping_patch_0301
 * @package modules.shipping
 */
class shipping_patch_0301 extends patch_BasePatch
{
//  by default, isCodePatch() returns false.
//  decomment the following if your patch modify code instead of the database structure or content.
    /**
     * Returns true if the patch modify code that is versionned.
     * If your patch modify code that is versionned AND database structure or content,
     * you must split it into two different patches.
     * @return Boolean true if the patch modify code that is versionned.
     */
//	public function isCodePatch()
//	{
//		return true;
//	}
 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$this->execChangeCommand('update-autoload', array('modules/shipping'));
		
		$this->execChangeCommand('compile-documents', array());
		$this->execChangeCommand('compile-db-schema', array());
		$this->execChangeCommand('compile-editors-config', array()) ;
		$this->execChangeCommand('compile-locales', array('shipping'));
		
		$this->execChangeCommand('compile-roles', array());
		
		$this->executeLocalXmlScript('list.xml');
	}

	/**
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'shipping';
	}

	/**
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0301';
	}
}