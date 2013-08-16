<?php
/**
 * shipping_ShippingModeFilter
 * @package modules.shipping.persistentdocument.filters
 */
class shipping_ShippingModeFilter extends f_persistentdocument_DocumentFilterImpl
{

	public function __construct()
	{
        $dm = f_persistentdocument_PersistentDocumentModel::getInstance('shipping', 'mode');
        $shippingModels = $dm->getChildrenNames();

        // Récupérer la liste des modes de livraisons existants utilisés
        // => projection avec un groupby model
        $type = new BeanPropertyInfoImpl('type', 'String');
        $type->setLabelKey('partie de l\'adresse');
        $type->setListId('modules_shipping/modes');
        $paramType = new f_persistentdocument_DocumentFilterValueParameter($type);
        $this->setParameter('type', $paramType);
    }
	
	/**
	 * @return String
	 */
	public static function getDocumentModelName()
	{
		return 'modules_order/order';
	}
	
	/**
	 * Check a single given object. 
	 * @param unknown $value
	 * @return boolean
	 */
//	public function checkValue($value)
//	{
//	}

	/**
	 * Get the query to find documents matching this filter.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function getQuery()
	{
        $ds = f_persistentdocument_DocumentService::getInstanceByDocumentModelName($this->getParameter('type')->getValue());
        $query = $ds->createQuery();
        $query->setProjection(Projections::property("id"));
        $ids = array();
        foreach ($query->findColumn("id") as $id)
        {
            $ids[] = $id;
        }

        $query = order_OrderService::getInstance()->createQuery();
        $query->add(Restrictions::in('shippingModeId', $ids));
        return $query;
	}
}