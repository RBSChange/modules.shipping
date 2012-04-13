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
	
	/**
	 * @param integer $maxCount
	 * @return string
	 */
	public function getShortDescriptionAsHtml($maxCount = 80)
	{
		if ($this->hasLongDescription($maxCount))
		{
			$desc = f_util_HtmlUtils::htmlToText($this->getDescription(), false, true);
			$desc = f_util_StringUtils::shortenString($desc, $maxCount);
			return f_util_HtmlUtils::textToHtml($desc);
		}
		else
		{
			return $this->getDescription();
		}
	}
	
	/**
	 * @param integer $maxCount
	 * @return boolean
	 */
	public function hasLongDescription($maxCount = 80)
	{
		return f_util_StringUtils::strlen($this->getDescription()) > $maxCount;
	}
}