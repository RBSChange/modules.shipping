<?php
/**
 * shipping_persistentdocument_mode
 * @package shipping.persistentdocument
 */
class shipping_persistentdocument_mode extends shipping_persistentdocument_modebase
{
	/**
	 * @param order_CartInfo $cart
	 * @return string 
	 */
	public function getResumeAsHtml($cart = null)
	{
		return $this->getLabelAsHtml();	
	}
}