<?php
/**
 * shipping_VirtualmodeScriptDocumentElement
 * @package modules.shipping.persistentdocument.import
 */
class shipping_VirtualmodeScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return shipping_persistentdocument_virtualmode
	 */
	protected function initPersistentDocument()
	{
		return shipping_VirtualmodeService::getInstance()->getNewDocumentInstance();
	}
	
	/**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_shipping/virtualmode');
	}
}