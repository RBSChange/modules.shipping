<?php
/**
 * shipping_RelayModeService
 * @package shipping
 */
abstract class shipping_RelayModeService extends shipping_ModeService
{
	
	abstract protected function getDetailExpeditionPageTagName();
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 * @return website_persistentdocument_page
	 */
	public function getDisplayPageForExpedition($mode, $expedition)
	{
		$order = $expedition->getOrder();
		$website = $order->getWebsite();
		$page = TagService::getInstance()->getDocumentByContextualTag($this->getDetailExpeditionPageTagName(), $website, false);
		return $page !== null ? $page : false;
	}
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_order $order
	 * @param order_CartInfo $cart
	 * @param boolean $setDefault
	 * @return boolean true if shippingAddress property was set on order.
	 */
	public function setShippingAddress($mode, $order, $cart, $setDefault = true)
	{
		if ($setDefault)
		{
			$order->setShippingAddress(null);
		}
		
		$modeId = $mode->getId();
		
		$bool = false;
		$addressId = $order->getAddressIdByModeId($modeId);
		if ($addressId && $this->getPersistentProvider()->getDocumentModelName($addressId) == 'modules_order/shippingaddress')
		{
			$address = order_persistentdocument_shippingaddress::getInstanceById($addressId);
			$bool = true;
		}
		else
		{
			$address = order_ShippingaddressService::getInstance()->getNewDocumentInstance();
		}
		
		$service = order_ShippingModeConfigurationService::getInstance();
		
		$addressInfo = $cart->getAddressInfo();
		$shippingAddress = $addressInfo->shippingAddress;
		
		$address->setOrderId($order->getId());
		$address->setTargetId($modeId);
		$address->setLabel($service->getConfiguration($cart, $mode->getId(), 'relayCodeReference'));
		$address->setFirstname($shippingAddress->FirstName);
		$address->setLastname($shippingAddress->LastName);
		$address->setCompany($shippingAddress->Company);
		$address->setAddressLine1($shippingAddress->Addressline1);
		$address->setAddressLine2($shippingAddress->Addressline2);
		$address->setAddressLine3($shippingAddress->Addressline3);
		$address->setZipCode($shippingAddress->Zipcode);
		$address->setCity($shippingAddress->City);
		$address->setProvince($shippingAddress->Province);
		$address->setCountryid($shippingAddress->CountryId);
		$address->setPublicationstatus('FILED');
		$address->save();
		$order->setAddressIdByModeId($modeId, $address->getId());
		
		return $bool;
	}
	
	/**
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_CartInfo $cart
	 */
	public function updateCartShippingAddress($mode, $cart)
	{
		$service = order_ShippingModeConfigurationService::getInstance();
		
		$addressInfo = $cart->getAddressInfo();
		if ($addressInfo)
		{
			if ($addressInfo->useSameAddressForBilling)
			{
				$addressInfo->useSameAddressForBilling = false;
			}
			$shippingAddress = $addressInfo->shippingAddress;
			$shippingAddress->Title = null;
			$shippingAddress->FirstName = LocaleService::getInstance()->transFO('m.shipping.general.relay-label', array('ucf', 'lab'), array(
				'codeReference' => $service->getConfiguration($cart, $mode->getId(), 'relayCodeReference')));
			$shippingAddress->LastName = $service->getConfiguration($cart, $mode->getId(), 'relayName');
			$shippingAddress->Company = $service->getConfiguration($cart, $mode->getId(), 'relayName');
			$shippingAddress->Addressline1 = $service->getConfiguration($cart, $mode->getId(), 'relayAddressLine1');
			$shippingAddress->Addressline2 = $service->getConfiguration($cart, $mode->getId(), 'relayAddressLine2');
			$shippingAddress->Addressline3 = $service->getConfiguration($cart, $mode->getId(), 'relayAddressLine3');
			$shippingAddress->Zipcode = $service->getConfiguration($cart, $mode->getId(), 'relayZipCode');
			$shippingAddress->City = $service->getConfiguration($cart, $mode->getId(), 'relayCity');
			$shippingAddress->Province = null;
			
			$countryCode = strtoupper($service->getConfiguration($cart, $mode->getId(), 'relayCountryCode'));
			$country = zone_CountryService::getInstance()->getByCode($countryCode);
			if (null !== $country)
			{
				$shippingAddress->CountryId = $country->getId();
			}
		
		}
	}
}