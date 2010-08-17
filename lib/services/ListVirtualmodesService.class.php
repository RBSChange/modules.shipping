<?php
class shipping_ListVirtualmodesService implements list_ListItemsService
{
    /**
     * @var shipping_ListVirtualmodesService
     */
	private static $instance = null;

	/**
	 * @return shipping_ListVirtualmodesService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new shipping_ListVirtualmodesService();
		}
		return self::$instance;
	}

	/**
	 * @return Array<list_Item>
	 */
	public function getItems()
	{
		$itemArray = array();
	    foreach (shipping_VirtualmodeService::getInstance()->createQuery()
	    	->add(Restrictions::published())
	    	->addOrder(Order::asc('document_label'))
	    	->find() as $virtualModes)
	    {
	    	$itemArray[] = new list_Item($virtualModes->getLabel(), $virtualModes->getId());
	    }
		return $itemArray;
	}
}