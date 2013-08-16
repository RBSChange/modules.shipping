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
	 * @param shipping_persistentdocument_mode $mode
	 * @param integer $countryId
	 * @return boolean
	 */
	public function isValidForCountryId($mode, $countryId)
	{
		if ($mode->getDeliveryZoneCount() === 0)
		{
			return true;
		}
		$zcs = zone_CountryService::getInstance();
		foreach ($mode->getDeliveryZoneArray() as $zone)
		{
			$countries = $zcs->getCountries($zone);
			foreach ($countries as $country) 
			{
				if ($country->getId() == $countryId)
				{
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param shipping_persistentdocument_mode $mode
	 * @return zone_persistentdocument_country[]
	 */
	public function getDeliveryCountries($mode)
	{
		$zcs = zone_CountryService::getInstance();
		if ($mode->getDeliveryZoneCount() === 0)
		{
			return $zcs->getPublished(Order::asc('label'));
		}
		$result = array();
		foreach ($mode->getDeliveryZoneArray() as $zone)
		{
			$countries = $zcs->getCountries($zone);
			foreach ($countries as $country) 
			{
				$result[strtolower($country->getLabel())] = $country;
			}
		}
		ksort($result);
		return array_values($result);
	}
	
	/**
	 * @param shipping_persistentdocument_mode[] $modes
	 * @return zone_persistentdocument_country[]
	 */
	public function getDeliveryCountriesForModes($modes)
	{
		$zcs = zone_CountryService::getInstance();
		$result = array();
		$zones = array();
		
		foreach ($modes as $mode) 
		{
			if ($mode->getDeliveryZoneCount() === 0)
			{
				$countries = $zcs->getPublished(Order::asc('label'));
				foreach ($countries as $country) 
				{
					$result[strtolower($country->getLabel())] = $country;
				}
				break;
			}
			
			foreach ($mode->getDeliveryZoneArray() as $zone)
			{
				$zones[$zone->getId()] = $zone;
			}
		}
		
		if (count($result) === 0 && count($zones) > 0)
		{
			foreach ($zones as $zone)
			{
				$countries = $zcs->getCountries($zone);
				foreach ($countries as $country) 
				{
					$result[strtolower($country->getLabel())] = $country;
				}
			}
		}
		
		ksort($result);
		return array_values($result);
	}
	
	
	/**
	 * @param order_persistentdocument_expedition $expedition
	 * @param shipping_persistentdocument_mode $mode
	 */
	public function completeExpedtionForMode($expedition, $mode)
	{
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
		if ($expeditionLine->getTrackingURL() === null)
		{
			$expeditionLine->setTrackingURL($expedition->getOriginalTrackingURL());
		}
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
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_CartInfo $cart
	 * @return string[]|false
	 */
	public function getConfigurationBlockForCart($mode, $cart)
	{
		return false;
	}
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_CartInfo $cart
	 */
	public function completeCart($mode, $cart)
	{
		
	}
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_order $order
	 * @param order_CartInfo $cartInfo
	 * @param boolean $setDefault
	 * @return boolean true if shippingAddress property was set on order.
	 */
	public function setShippingAddress($mode, $order, $cartInfo, $setDefault = true)
	{
		if (!$setDefault)
		{
			return false;
		}
		
		$shippingAddress = $order->getShippingAddress();
		if ($cartInfo->getAddressInfo()->useSameAddressForBilling)
		{
			if ($shippingAddress !== $order->getBillingAddress())
			{
				$order->setShippingAddress(null);
			}
			return true;
		}

		if ($shippingAddress === null || $shippingAddress === $order->getBillingAddress())		
		{
			$shippingAddress = customer_AddressService::getNewDocumentInstance();
			$order->setShippingAddress($shippingAddress);
		}		
		$cartInfo->getAddressInfo()->exportShippingAddress($shippingAddress);
		$shippingAddress->setPublicationstatus('FILED');
		$shippingAddress->save();	
		$cartInfo->setShippingAddressId($shippingAddress->getId());
		return true;
	}
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 * @see order_ExpeditionService::shipExpedition
	 */
	public function completeExpeditionForShipping($mode, $expedition)
	{
		// Nothing to do by default.
		// Ici mettre à jour les propriétés de l'expédition. L'expédition sera sauvegardée juste après.
	}
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 * @return website_persistentdocument_page
	 */
	public function getDisplayPageForExpedition($mode, $expedition)
	{
		return null;
	}
	
	/**
	 * @param shipping_persistentdocument_mode $document
	 * @param string $errorMessage
	 */
	public function canBeFiled($document, &$errorMessage)
	{
		$ms = ModuleService::getInstance();
		if ($ms->moduleExists('catalog'))
		{
			$sfs = catalog_ShippingfilterService::getInstance();
			$query = $sfs->createQuery()->add(Restrictions::eq('mode', $document))->setProjection(Projections::rowCount('count'));
			if (f_util_ArrayUtils::firstElement($query->findColumn('count')) > 0)
			{
				$errorMessage = LocaleService::getInstance()->transBO('m.shipping.bo.general.used-in-filters');
				return false;
			}
		}
		return true;
	}
	
	/**	
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 * @param boolean $completlyShipped
	 * @return string
	 */
	public function sendShippedNotification($mode, $expedition, $completlyShipped = false)
	{
		$codeName = 'modules_order/expedition_shipped';
		$suffix = $expedition->getTransporteur();
		order_ModuleService::getInstance()->sendCustomerSuffixedNotification($codeName, $suffix, $expedition->getOrder(), $expedition->getBill(), $expedition);
	}

	/**
	 * @param catalog_persistentdocument_product|catalog_persistentdocument_declinedproduct $product
	 * @return bool
	 */
	public function canShipProductWithMode($product, $mode)
	{
		return true;
	}

	/**
	 * @param order_CartLineInfo[] $cartLines
	 * @return bool
	 */
	public function canShipCartLinesWithMode($cartLines, $mode)
	{
		foreach ($cartLines as $cartLine)
		{
			/* @var order_persistentdocument_orderline $orderLine */
			if (!$this->canShipProductWithMode($cartLine->getProduct(), $mode))
			{
				return false;
			}
		}
		return true;
	}
}