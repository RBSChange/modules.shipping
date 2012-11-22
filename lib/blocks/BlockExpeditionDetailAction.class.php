<?php
/**
 * shipping_BlockExpeditionDetailAction
 * @package modules.mondialrelay.lib.blocks
 */
abstract class shipping_BlockExpeditionDetailAction extends website_BlockAction
{
	/**
	 * @var order_persistentdocument_expedition
	 */
	protected $expedition;
	
	/**
	 * @var array
	 */
	protected $param = array();
	
	/**
	 * Initialize $this->param
	 */
	abstract protected function init();
	
	/**
	 * @return shipping_Relay
	 */
	abstract protected function getRelayDetail();

	/**
	 * @param string $trackingNumber
	 * @return array
	 */
	abstract protected function getTrackingDetail($trackingNumber);
	
	/**
	 * @return string[]
	 */
	public function getRequestModuleNames()
	{
		$names = parent::getRequestModuleNames();
		if (!in_array('order', $names))
		{
			$names[] = 'order';
		}
		return $names;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return string
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		
		$expedition = $this->getDocumentParameter();
		if ((!$expedition instanceof order_persistentdocument_expedition) || !$expedition->isPublished())
		{
			return website_BlockView::NONE;
		}
		
		$this->expedition = $expedition;
		
		$this->init();
		
		$relay = $this->getRelayDetail();
		$request->setAttribute('relay', $relay);
		
		$request->setAttribute('expedition', $expedition);
		$expeditionlines = $expedition->getDocumentService()->getLinesForDisplay($expedition);
		
		$request->setAttribute('expeditionlines', $expeditionlines);
		$request->setAttribute('order', $expedition->getOrder());
		
		$packetByExpedition = array();
		$packetIndex = array();
		$trackingByPacket = array();
		
		foreach ($expedition->getLineArray() as $line)
		{
			/* @var $line order_persistentdocument_expeditionline */
			$packetNumber = $line->getPacketNumber() ? $line->getPacketNumber() : '-';
			if (!isset($packetIndex[$packetNumber]))
			{
				$packetIndex[$packetNumber] = count($packetIndex);
			}
			$packetByExpedition[$packetIndex[$packetNumber]][] = $line;
			
			$trackingNumber = $line->getTrackingNumber();
			if (!isset($trackingByPacket[$trackingNumber]))
			{
				$trackingDetail = $this->getTrackingDetail($trackingNumber);
				$trackingByPacket[$trackingNumber] = $trackingDetail;
			}
		}
		$request->setAttribute('packetByExpedition', $packetByExpedition);
		$request->setAttribute('trackingByPacket', $trackingByPacket);
		
		return $this->getView(website_BlockView::SUCCESS);
	}
	
	/**
	 * @param string $shortViewName
	 * @throws TemplateNotFoundException if template could not be found in current module and comment module
	 * @return TemplateObject
	 */
	protected function getView($shortViewName)
	{
		$template = $this->getTemplate($shortViewName);
		if ($template !== null)
		{
			return $template;
		}
		$templateName = 'Shipping-Block-ExpeditionDetail-' . $shortViewName;
		return $this->getTemplateByFullName('modules_shipping', $templateName);
	}
}