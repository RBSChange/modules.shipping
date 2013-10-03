<?php
/**
 * shipping_BlockRelayModeConfigurationAction
 * @package modules.shipping
 */
class shipping_BlockRelayModeConfigurationAction extends order_BlockShippingModeConfigurationBaseAction
{
	const P_ADDRESS = 'address';
	const P_ZIP_CODE = 'zipcode';
	const P_CITY = 'city';
	const P_COUNTRY = 'country';
	const P_COUNTRY_CODE = 'countryCode';
	const P_MODE = 'mode';
	const P_SHIPPING_ADDRESS = 'shippingAddress';
	const P_LANG = 'lang';

	/**
	 * @var array
	 */
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
	 * @return string
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

			$this->param[self::P_ADDRESS] = $address;
			$this->param[self::P_ZIP_CODE] = $zipCode;
			$this->param[self::P_CITY] = $city;
			$this->param[self::P_COUNTRY] = $country;
			$this->param[self::P_COUNTRY_CODE] = $sAddr->getCountryCode();
			$this->param[self::P_MODE] = $request->getParameter('mode');
			$this->param[self::P_SHIPPING_ADDRESS] = $sAddr;
			
			$frameUrl = $this->buildFrameUrl();
			if ($frameUrl == null)
			{
				$request->setAttribute('address', $address);
				$request->setAttribute('zipcode', $zipCode);
				$request->setAttribute('city', $city);
				$request->setAttribute('country', $country);

				$this->setMapParams($request, $this->param);
				$relays = $this->buildRelayList();
				$request->setAttribute('relays', $relays);
				
				$request->setAttribute('action', $this->selectRelayActionUrl());
			}
			else
			{
				$request->setAttribute('frameUrl', $frameUrl);
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
	 * @return string
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
		}
		else
		{
			$countryCode = $request->getParameter('countrycode');
			if ($countryCode != null)
			{
				$country = zone_CountryService::getInstance()->getByCode($countryCode);
			}
		}
		
		$this->param[self::P_ADDRESS] = $address;
		$this->param[self::P_ZIP_CODE] = $zipCode;
		$this->param[self::P_CITY] = $city;
		$this->param[self::P_COUNTRY] = $countryId;
		$this->param[self::P_COUNTRY_CODE] = $countryCode;
		$this->param[self::P_MODE] = $mode;
		
		$this->setMapParams($request, $this->param);
		$relays = $this->buildRelayList();
		$request->setAttribute('relays', $relays);
		
		$request->setAttribute('action', $this->selectRelayActionUrl());
		
		return $this->getView(website_BlockView::SUCCESS);
	}

	/**
	 * Set map params
	 * @param f_mvc_Request $request
	 * @param String[] $parameters
	 */
	protected function setMapParams($request, $parameters)
	{
		$address = $parameters[self::P_ADDRESS];
		$zipCode = $parameters[self::P_ZIP_CODE];
		$city = $parameters[self::P_CITY];
		$countryId = $parameters[self::P_COUNTRY];
		$countryCode = $parameters[self::P_COUNTRY_CODE];

		$csi = zone_CountryService::getInstance();
		if (!f_util_StringUtils::isEmpty($countryId))
		{
			$country = $csi->getDocumentInstance($countryId);
		}
		if ($country == null && !f_util_StringUtils::isEmpty($countryCode))
		{
			$country = $csi->getByCode($countryCode);
		}
		if ($country != null)
		{
			$countryLabel = $country->getLabel();
		}
		else if (!f_util_StringUtils::isEmpty($countryCode))
		{
			$countryLabel = $countryCode;
		}

		$location = $address . ' ' . $zipCode . ' ' . $city . ' ' . $countryLabel;

		list ($latitude, $longitude) = gmaps_ModuleService::getInstance()->getCoordinatesForAddress($location);

		$request->setAttribute('mapzoom', $this->getDefaultMapZoom());
		$request->setAttribute('mapCenter', array('latitude' => $latitude, 'longitude' => $longitude));
		$request->setAttribute('mapHeight', '400px');
		$request->setAttribute('mapWidth', '100%');
	}

	/**
	 * Get the default zoom for map
	 * @return Integer
	 */
	protected function getDefaultMapZoom()
	{
		return 11;
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
	 * @return string|null
	 */
	protected function selectRelayActionUrl()
	{
		return LinkHelper::getActionUrl('shipping', 'SelectRelay');
	}
	
	/**
	 * Must be overrided
	 * Return the list of shipping_Relay
	 * @return shipping_Relay[]|null
	 */
	protected function buildRelayList()
	{
		return null;
	}
	
	/**
	 * Must be overrided
	 * Return the url of iframe
	 * @return string
	 */
	protected function buildFrameUrl()
	{
		return null;
	}
}