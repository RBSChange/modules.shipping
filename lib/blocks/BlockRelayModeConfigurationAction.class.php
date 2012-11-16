<?php
/**
 * shipping_BlockRelayModeConfigurationAction
 * @package modules.shipping.lib.blocks
 */
class shipping_BlockRelayModeConfigurationAction extends order_BlockShippingModeConfigurationBaseAction
{
	
	protected $param = array();
	
	/**
	 * @return string[]
	 */
	public function getRequestModuleNames()
	{
		$modules = parent::getRequestModuleNames();
		$modules[] = 'order';
		return $modules;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		
		// The following parameters are transmitted by the request
		// cart cart; mode mode; modeId modeId; hiddenFieldName hiddenFieldName
		$params = $request->getParameters();
		foreach ($request->getParameters() as $name => $value)
		{
			$request->setAttribute($name, $value);
		}
		
		$cart = $request->getParameter('cart');
		
		if ($cart instanceof order_CartInfo)
		{
			$sAddr = $this->getShippingAddress($cart);
			$address = $sAddr->getAddressLine1() . ' ' . $sAddr->getAddressLine2() . ' ' . $sAddr->getAddressLine3();
			$zipCode = $sAddr->getZipCode();
			$city = $sAddr->getCity();
			$country = $sAddr->getCountryid();
			$countryLabel = $sAddr->getCountryidLabel();
			
			$this->param['address'] = $address;
			$this->param['zipcode'] = $zipCode;
			$this->param['city'] = $city;
			$this->param['country'] = $country;
			$this->param['countryCode'] = $sAddr->getCountryCode();
			$this->param['mode'] = $request->getParameter('mode');
			$this->param['shippingAddress'] = $sAddr;
			
			$frameUrl = $this->buildFrameUrl();
			if ($frameUrl == null)
			{
				$request->setAttribute('address', $address);
				$request->setAttribute('zipcode', $zipCode);
				$request->setAttribute('city', $city);
				$request->setAttribute('country', $country);
				
				$location = $address . ' ' . $zipCode . ' ' . $city . ' ' . $countryLabel;
				
				list ($latitude, $longitude) = gmaps_ModuleService::getInstance()->getCoordinatesForAddress($location);
				$request->setAttribute('mapCenter', array('latitude' => $latitude, 'longitude' => $longitude));
				
				$request->setAttribute('mapzoom', 11);
				$request->setAttribute('mapHeight', '400px');
				$request->setAttribute('mapWidth', '100%');
				
				$relays = $this->buildRelayList();
				$request->setAttribute('relays', $relays);
				
				$request->setAttribute('action', $this->selectRelayActionUrl());
			}
			else
			{
				$request->setAttribute("frameUrl", $frameUrl);
			}
			
			// hiddenFieldName will enable us to return to shipping step and validate/check shipping mode
			$cart->setProperties('hiddenFieldName', $request->getParameter('hiddenFieldName'));
			$cart->save();
		}
		
		return $this->getView(website_BlockView::SUCCESS);
	
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function executeFilter($request, $response)
	{
		$params = $request->getParameters();
		foreach ($request->getParameters() as $name => $value)
		{
			$request->setAttribute($name, $value);
		}
		
		$cart = order_CartService::getInstance()->getDocumentInstanceFromSession();
		$this->setRequestParams($request, $cart);
		
		$modeId = $request->getParameter('modeId');
		$mode = DocumentHelper::getDocumentInstance($modeId);
		
		$address = trim($request->getParameter('address'));
		$zipCode = trim($request->getParameter('zipcode'));
		$city = trim($request->getParameter('city'));
		$countryId = $request->getParameter('country');
		if ($countryId != null)
		{
			$country = zone_persistentdocument_country::getInstanceById($countryId);
			$countryCode = $country->getCode();
			$countryLabel = $country->getLabel();
		}
		else
		{
			$countryCode = $request->getParameter('countrycode');
			if ($countryCode != null)
			{
				$country = zone_CountryService::getInstance()->getByCode($countryCode);
			}
			$countryLabel = $countryCode;
		}
		
		$this->param['address'] = $address;
		$this->param['zipcode'] = $zipCode;
		$this->param['city'] = $city;
		$this->param['country'] = $countryId;
		$this->param['countryCode'] = $countryCode;
		$this->param['mode'] = $mode;
		
		$location = $address . ' ' . $zipCode . ' ' . $city . ' ' . $countryLabel;
		
		list ($latitude, $longitude) = gmaps_ModuleService::getInstance()->getCoordinatesForAddress($location);
		
		$request->setAttribute('mapCenter', array('latitude' => $latitude, 'longitude' => $longitude));
		$request->setAttribute('mapHeight', '400px');
		$request->setAttribute('mapWidth', '100%');
		
		$relays = $this->buildRelayList();
		$request->setAttribute('relays', $relays);
		
		$request->setAttribute('action', $this->selectRelayActionUrl());
		
		return $this->getView(website_BlockView::SUCCESS);
	}
	
	/**
	 * @param order_CartInfo $cart
	 * @return customer_persistentdocument_address
	 */
	protected function getShippingAddress($cart)
	{
		$addressInfo = $cart->getAddressInfo();
		if (null !== $addressInfo && isset($addressInfo->shippingAddress))
		{
			// Because $addressInfo->shippingAddress is a order_AddressBean
			$shippingAddress = customer_AddressService::getInstance()->getNewDocumentInstance();
			$addressInfo->shippingAddress->export($shippingAddress);
		}
		else
		{
			$shippingAddress = $cart->getCustomer()->getDefaultAddress();
		}
		
		return $shippingAddress;
	}
	
	/**
	 * @param $shortViewName
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
		$templateName = 'Shipping-Block-RelayModeConfiguration-' . $shortViewName;
		return $this->getTemplateByFullName('modules_shipping', $templateName);
	}
	
	/**
	 * Get the action url to select a relay
	 * @return Ambigous <string, NULL>
	 */
	protected function selectRelayActionUrl()
	{
		return LinkHelper::getActionUrl('shipping', 'SelectRelay');
	}
	
	/**
	 * Must be override
	 * Return the list of shipping_Relay
	 * @return array<shipping_Relay>
	 */
	protected function buildRelayList()
	{
		return null;
	}
	
	/**
	 * Must be override
	 * Return the url of iframe
	 * @return string
	 */
	protected function buildFrameUrl()
	{
		return null;
	}

}