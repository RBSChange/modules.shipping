<?php
/**
 * shipping_ListModesService
 * @package modules.shipping.lib.services
 */
class shipping_ListModesService extends BaseService implements list_ListItemsService
{
	/**
	 * @var shipping_ListModesService
	 */
	private static $instance;

	/**
	 * @return shipping_ListModesService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

    /**
     * @see list_persistentdocument_dynamiclist::getItems()
     * @return list_Item[]
     */
    public function getItems()
    {
        $items = array();
        $dm = f_persistentdocument_PersistentDocumentModel::getInstance('shipping', 'mode');
        $ls = LocaleService::getInstance();
        $shippingModels = $dm->getChildrenNames();
        foreach ($shippingModels as $model)
        {
            $module = substr($model, strpos($model, '_')+1, strpos($model,'/')-strlen('modules_'));
            $mName = substr($model, strpos($model,'/')+1);
            $locKey = "m.$module.document.$mName.document-name";
            $items[] = new list_Item($ls->transBO($locKey, array('ucf')), $model);
        }

        return $items;
    }

	/**
	 * @var Array
	 */
	private $parameters = array();
	
	/**
	 * @see list_persistentdocument_dynamiclist::getListService()
	 * @param array $parameters
	 */
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
	}
}