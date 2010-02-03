<?php
/**
 * shipping_ModeScriptDocumentElement
 * @package modules.shipping.persistentdocument.import
 */
class shipping_ModeScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return shipping_persistentdocument_mode
     */
    protected function initPersistentDocument()
    {
    	return shipping_ModeService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_shipping/mode');
	}
}