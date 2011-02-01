<?php
/**
 * shipping_patch_0350
 * @package modules.shipping
 */
class shipping_patch_0350 extends patch_BasePatch
{

 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$results = shipping_ModeService::getInstance()->createQuery()
			->add(Restrictions::eq('codeReference', 'Shipping mode'))->find();
		foreach ($results as $mode) 
		{
			if ($mode instanceof shipping_persistentdocument_mode) 
			{
				$mode->setCodeReference($mode->getCode());
				$this->log($mode->getId() . ' - ' . $mode->getLabel() . '=>' . $mode->getCode());
				$mode->save();
			} 
		}
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
		return '0350';
	}
}