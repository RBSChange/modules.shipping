<?php
/**
 * @package modules.shipping
 * @method shipping_ListVirtualmodesService getInstance()
 */
class shipping_ListVirtualmodesService extends change_BaseService implements list_ListItemsService
{
	/**
	 * @return list_Item[]
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