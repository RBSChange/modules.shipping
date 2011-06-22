<?php
/**
 * shipping_patch_0351
 * @package modules.shipping
 */
class shipping_patch_0351 extends patch_BasePatch
{
 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$this->log('Add addressRequired field...');
		$newPath = f_util_FileUtils::buildWebeditPath('modules/shipping/persistentdocument/mode.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'shipping', 'mode');
		$newProp = $newModel->getPropertyByName('addressRequired');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('shipping', 'mode', $newProp);
		
		$query = "UPDATE `m_shipping_doc_mode` SET `addressrequired` = 1 WHERE `document_model` <> 'modules_shipping/virtualmode'";
		$this->executeSQLQuery($query);
		
		$this->log('Add deliveryZone field...');
		$newPath = f_util_FileUtils::buildWebeditPath('modules/shipping/persistentdocument/mode.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'shipping', 'mode');
		$newProp = $newModel->getPropertyByName('deliveryZone');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('shipping', 'mode', $newProp);
		$this->execChangeCommand('compile-db-schema');
		
		$this->execChangeCommand('compile-locales', array('shipping'));
	}
}