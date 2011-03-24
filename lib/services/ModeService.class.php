<?php
/**
 * shipping_ModeService
 * @package shipping
 */
class shipping_ModeService extends f_persistentdocument_DocumentService
{
	/**
	 * @var shipping_ModeService
	 */
	private static $instance;

	/**
	 * @return shipping_ModeService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return shipping_persistentdocument_mode
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_shipping/mode');
	}

	/**
	 * Create a query based on 'modules_shipping/mode' model.
	 * Return document that are instance of modules_shipping/mode,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_shipping/mode');
	}
	
	/**
	 * Create a query based on 'modules_shipping/mode' model.
	 * Only documents that are strictly instance of modules_shipping/mode
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_shipping/mode', false);
	}
	
	/**
	 * @param order_persistentdocument_expedition $expedition
	 * @param shipping_persistentdocument_mode $mode
	 */
	public function completeExpedtionForMode($expedition, $mode)
	{
		Framework::info(__METHOD__);
		$expedition->setShippingModeId($mode->getId());
		$expedition->setTransporteur($mode->getCodeReference());
		$expedition->setTrackingURL($mode->getTrackingUrl());
	}
	
	/**
	 * @param order_persistentdocument_expeditionline $expeditionLine
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 */	
	public function completeExpeditionLineForDisplay($expeditionLine, $shippmentMode, $expedition)
	{
		
	}
	
	/**
	 * @param shipping_persistentdocument_mode $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId)
	{
		$document->setCodeReference($document->getCode());
	}

	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 * @return array<string, string>
	 */
	public function getNotificationParameters($mode, $expedition)
	{
		return array();
	}
}