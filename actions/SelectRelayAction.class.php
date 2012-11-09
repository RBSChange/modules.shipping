<?php
/**
 * shipping_SelectRelayAction
 * @package modules.shipping.actions
 */
abstract class shipping_SelectRelayAction extends f_action_BaseAction
{
	
	abstract protected function getMode($modeId);
	
	abstract protected function getRelayCodeParamName();
	abstract protected function getRelayCountryCodeParamName();
	abstract protected function getRelayNameParamName();
	abstract protected function getRelayAddress1ParamName();
	abstract protected function getRelayAddress2ParamName();
	abstract protected function getRelayAddress3ParamName();
	abstract protected function getRelayZipCodeParamName();
	abstract protected function getRelayCityParamName();
	
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$cs = order_CartService::getInstance();
		$cart = $cs->getDocumentInstanceFromSession();
		
		$modeId = $request->getParameter('modeId');
		$hiddenFieldName = $cart->getProperties('hiddenFieldName');
		
		$service = order_ShippingModeConfigurationService::getInstance();
		$service->setConfiguration($cart, $modeId, 'relayCodeReference', f_util_StringUtils::cleanString($request->getParameter($this->getRelayCodeParamName(), null)));
		$service->setConfiguration($cart, $modeId, 'relayCountryCode', f_util_StringUtils::cleanString($request->getParameter($this->getRelayCountryCodeParamName(), null)));
		$service->setConfiguration($cart, $modeId, 'relayName', f_util_StringUtils::cleanString($request->getParameter($this->getRelayNameParamName(), null)));
		$service->setConfiguration($cart, $modeId, 'relayAddressLine1', f_util_StringUtils::cleanString($request->getParameter($this->getRelayAddress1ParamName(), null)));
		$service->setConfiguration($cart, $modeId, 'relayAddressLine2', f_util_StringUtils::cleanString($request->getParameter($this->getRelayAddress2ParamName(), null)));
		$service->setConfiguration($cart, $modeId, 'relayAddressLine3', f_util_StringUtils::cleanString($request->getParameter($this->getRelayAddress3ParamName(), null)));
		$service->setConfiguration($cart, $modeId, 'relayZipCode', f_util_StringUtils::cleanString($request->getParameter($this->getRelayZipCodeParamName(), null)));
		$service->setConfiguration($cart, $modeId, 'relayCity', f_util_StringUtils::cleanString($request->getParameter($this->getRelayCityParamName(), null)));
		
		$mode = $this->getMode($modeId);
		
		if ($mode != null)
		{
			$mode->getDocumentService()->updateCartShippingAddress($mode, $cart);
		}
		
		$service->addCheckedModeId($cart, $modeId);
		
		$redirectParams = array();
		if (f_util_StringUtils::isNotEmpty($hiddenFieldName))
		{
			$redirectParams[] = "$hiddenFieldName=true";
		}
		
		// Redirect to Shipping Step
		$op = $cart->getOrderProcess();
		$shippingStepUrl = $op->getStepURL('Shipping');
		$paramString = join('&', $redirectParams);
		$redirectUrl = f_util_StringUtils::isEmpty($paramString) ? $shippingStepUrl : $shippingStepUrl . '?' . $paramString;
		
		HttpController::getInstance()->redirectToUrl($redirectUrl);
		
		return View::NONE;
	}
	
	public function isSecure()
	{
		return false;
	}
}