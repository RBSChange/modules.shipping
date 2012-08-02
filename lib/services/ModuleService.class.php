<?php
/**
 * @package modules.shipping.lib.services
 */
class shipping_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var shipping_ModuleService
	 */
	private static $instance = null;
	
	/**
	 * @return shipping_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

}