<?php
/**
 * shipping_patch_0300
 * @package modules.shipping
 */
class shipping_patch_0300 extends patch_BasePatch
{
//  by default, isCodePatch() returns false.
//  decomment the following if your patch modify code instead of the database structure or content.
    /**
     * Returns true if the patch modify code that is versionned.
     * If your patch modify code that is versionned AND database structure or content,
     * you must split it into two different patches.
     * @return Boolean true if the patch modify code that is versionned.
     */
//	public function isCodePatch()
//	{
//		return true;
//	}
 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
		/*
			order_persistentdocument_shippingmode -> shipping_persistentdocument_mode
			modules_order/shippingmode ->   modules_shipping/mode

			order_ShippingmodeService ->
		 */		
		$this->createNewtable();
		
		// Migration des mode de livraison
		$sql = "INSERT INTO `m_shipping_doc_mode` 
			(`document_id`, `document_model`, `document_label`, `document_author`, `document_authorid`, `document_creationdate`, `document_modificationdate`, `document_publicationstatus`, `document_lang`, `document_modelversion`, `document_version`, `document_startpublicationdate`, `document_endpublicationdate`, `document_metas`, 
			`visual`, `description`, `code`, `codereference`, `trackingurl`)
		SELECT
			 `document_id`, 'modules_shipping/mode', `document_label`, `document_author`, `document_authorid`, `document_creationdate`, `document_modificationdate`, `document_publicationstatus`, `document_lang`, '0.5', `document_version`, `document_startpublicationdate`, `document_endpublicationdate`, `document_metas`,
			 `visual`, `description`, `code`, `codereference`, `trackingurl`
			 FROM `m_synchro_doc_article` WHERE `document_model` = 'modules_order/shippingmode'";
		$this->executeSQLQuery($sql);

		$sql = "INSERT INTO `m_shipping_doc_mode_i18n` 
			(`document_id`, `lang_i18n`, `document_label_i18n`, `description_i18n`)
		SELECT
			 `document_id`, `document_lang`,  `document_label`, `description`
		FROM `m_shipping_doc_mode`";		
		$this->executeSQLQuery($sql);
		
		
		$sql = "UPDATE `m_shipping_doc_mode` SET `code` = `codereference` WHERE `code` IS NULL";
		$this->executeSQLQuery($sql);
		
		//Suppression des anciens mode

		$sql = "DELETE FROM `m_synchro_doc_article_i18n` WHERE `document_id` IN (SELECT `document_id` FROM `m_shipping_doc_mode`)";
		$this->executeSQLQuery($sql);
		
		
		$sql = "DELETE FROM `m_synchro_doc_article` WHERE `document_model` = 'modules_order/shippingmode'";
		$this->executeSQLQuery($sql);
		
		$sql = "DELETE FROM `f_relation` WHERE `document_model_id1` = 'modules_order/shippingmode' AND relation_name IN ('family', 'condition')";
		$this->executeSQLQuery($sql);
				
		// Update Model name.
		$this->executeSQLQuery("UPDATE f_document SET document_model = 'modules_shipping/mode' WHERE document_model = 'modules_order/shippingmode';");
		$this->executeSQLQuery("UPDATE f_relation SET document_model_id1 = 'modules_shipping/mode' WHERE document_model_id1 = 'modules_order/shippingmode';");
		$this->executeSQLQuery("UPDATE f_relation SET document_model_id2 = 'modules_shipping/mode' WHERE document_model_id2 = 'modules_order/shippingmode';");

	
		$ts = TreeService::getInstance();
		$newRootId = ModuleService::getInstance()->getRootFolderId('shipping');
		$rootNode = DocumentHelper::getDocumentInstance($newRootId);
		
		$modes = shipping_ModeService::getInstance()->createQuery()
			->add(Restrictions::eq('modelversion', '0.5'))
			->find();

		foreach ($modes as $mode)
		{
			// Move the document.
			$treeNode = $ts->getInstanceByDocument($mode);
			if ($treeNode !== null)
			{
				$ts->deleteNode($treeNode);
			}
			$ts->newLastChildForNode($ts->getInstanceByDocument($rootNode), $mode->getId());
		}
	}
	
	private function createNewtable()
	{
		$this->executeSQLQuery("CREATE TABLE IF NOT EXISTS `m_shipping_doc_mode` (
	`document_id` int(11) NOT NULL default '0',
	`document_model` varchar(50) NOT NULL default '',
	  `document_label` varchar(255),
	  `document_author` varchar(50),
	  `document_authorid` int(11),
	  `document_creationdate` datetime,
	  `document_modificationdate` datetime,
	  `document_publicationstatus` ENUM('DRAFT', 'CORRECTION', 'ACTIVE', 'PUBLICATED', 'DEACTIVATED', 'FILED', 'DEPRECATED', 'TRASH', 'WORKFLOW') NULL DEFAULT NULL,
	  `document_lang` varchar(2),
	  `document_modelversion` varchar(20),
	  `document_version` int(11),
	  `document_startpublicationdate` datetime,
	  `document_endpublicationdate` datetime,
	  `document_metas` text,
	  `visual` int(11) default NULL,
	  `description` text,
	  `code` varchar(20),
	  `codereference` varchar(50),
	  `trackingurl` varchar(255),
PRIMARY KEY  (`document_id`)
) TYPE=InnoDB CHARACTER SET utf8 COLLATE utf8_bin");
		
		$this->executeSQLQuery("CREATE TABLE IF NOT EXISTS `m_shipping_doc_mode_i18n` (
	`document_id` int(11) NOT NULL default '0',
	`lang_i18n` varchar(2) NOT NULL default 'fr',
	  `document_label_i18n` varchar(255),
	  `description_i18n` text,
PRIMARY KEY  (`document_id`, `lang_i18n`)
) TYPE=InnoDB CHARACTER SET utf8 COLLATE utf8_bin");
		
	}

	/**
	 * Returns the name of the module the patch belongs to.
	 *
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'shipping';
	}

	/**
	 * Returns the number of the current patch.
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0300';
	}
}