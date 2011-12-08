<?php
/**
 * shipping_VirtualmodeService
 * @package modules.shipping
 */
class shipping_VirtualmodeService extends shipping_ModeService
{
	/**
	 * @var shipping_VirtualmodeService
	 */
	private static $instance;

	/**
	 * @return shipping_VirtualmodeService
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
	 * @return shipping_persistentdocument_virtualmode
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_shipping/virtualmode');
	}

	/**
	 * Create a query based on 'modules_shipping/virtualmode' model.
	 * Return document that are instance of modules_shipping/virtualmode,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_shipping/virtualmode');
	}
	
	/**
	 * Create a query based on 'modules_shipping/virtualmode' model.
	 * Only documents that are strictly instance of modules_shipping/virtualmode
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_shipping/virtualmode', false);
	}
	
	/**
	 * @param order_persistentdocument_expedition $expedition
	 * @param shipping_persistentdocument_mode $mode
	 */
	public function completeExpedtionForMode($expedition, $mode)
	{
		parent::completeExpedtionForMode($expedition, $mode);
		$expedition->setStatus(order_ExpeditionService::SHIPPED);
		$expedition->setShippingDate(date_Calendar::getInstance()->toString());
		
		$order = $expedition->getOrder();
		$trackingNumber = $order->getId() . ' - ' . $order->getCustomer()->getUser()->getId();	
		$expedition->setTrackingNumber(md5($trackingNumber));
		$expedition->setTrackingURL(null);
	}
	
	/**
	 * @param order_persistentdocument_expeditionline $expeditionLine
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 */	
	public function completeExpeditionLineForDisplay($expeditionLine, $shippmentMode, $expedition)
	{
		parent::completeExpeditionLineForDisplay($expeditionLine, $shippmentMode, $expedition);
		$product = $expeditionLine->getProduct();
		if ($product instanceof catalog_persistentdocument_virtualproduct)
		{
			$media = $product->getSecureMedia();
			$parameters = array('trakingNumber' => $expedition->getTrackingNumber()); 
			$url = $media->getDocumentService()->generateDownloadUrl($media, $media->getLang(), $parameters);
			$expeditionLine->setURL($url);
		}
	}
	
	/**
	 * @param shipping_persistentdocument_virtualmode $mode
	 * @param order_persistentdocument_order $order
	 * @param order_CartInfo $cartInfo
	 * @param boolean $setDefault
	 * @return boolean true if shippingAddress property was set on order.
	 */
	public function setShippingAddress($mode, $order, $cartInfo, $setDefault = true)
	{
		if ($setDefault)
		{
			$order->setShippingAddress(null);
		} 
		return false;
	}
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 * @return website_persistentdocument_page
	 */
	public function getDisplayPageForExpedition($mode, $expedition)
	{
		$order = $expedition->getOrder();
		$tag = 'contextual_website_website_modules_order_expeditionvirtual';
		$website = $order->getWebsite();
		$page = TagService::getInstance()->getDocumentByContextualTag($tag, $website, false);
		return $page !== null ? $page : false;
	}
}