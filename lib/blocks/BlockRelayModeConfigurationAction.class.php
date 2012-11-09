<?php
/**
 * shipping_BlockRelayModeConfigurationAction
 * @package modules.shipping.lib.blocks
 */
abstract class shipping_BlockRelayModeConfigurationAction extends website_BlockAction
{
	
	abstract protected function getRelayModeService();
	
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
		
		$paramSubmit = $request->getParameter('hiddenFieldName');
		$mode = $request->getParameter('mode');
		$modeId = $request->getParameter('modeId');
		$cart = $request->getParameter('cart');
		
		if ($cart instanceof order_CartInfo)
		{
			$service = $this->getRelayModeService();
			
			// hiddenFieldName will enable us to return to shipping step and validate/check shipping mode
			$cart->setProperties('hiddenFieldName', $request->getParameter('hiddenFieldName'));
			$cart->save();
			
			$frameUrl = $service->getFrameUrl($mode, $this->getShippingAddress($cart));
			$request->setAttribute("frameUrl", $frameUrl);
		}
		
		return $this->getView(website_BlockView::SUCCESS);
	}
	
	/**
	 * @param order_CartInfo $cart
	 * @return customer_AddressService
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
}