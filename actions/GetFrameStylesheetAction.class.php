<?php
/**
 * shipping_GetFrameStylesheetAction
 * @package modules.shipping.actions
 */
abstract class shipping_GetFrameStylesheetAction extends f_action_BaseAction
{
	
	abstract protected function getStylesheetName();
	
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + 86400) . " GMT");
		header('Content-type: text/css');
		$ss = StyleService::getInstance();
		echo $ss->getCSS($this->getStylesheetName());
		return View::NONE;
	}
	
	public function isSecure()
	{
		return false;
	}
}