<?php
/**
 * shipping_DeleteJSONAction
 * @package modules.shipping.actions
 */
class shipping_DeleteJSONAction extends generic_DeleteJSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$document = DocumentHelper::getByCorrection($this->getDocumentInstanceFromRequest($request));
		// Do not really delete nodes, just file them and remove them form tree.
		if ($document instanceof shipping_persistentdocument_mode)
		{
			$service = $document->getDocumentService();
			$errorMessage = '';
			if (!$service->canBeFiled($document, $errorMessage))
			{
				return $this->sendJSONError($errorMessage);
			}
			
			$ts = TreeService::getInstance();
			$node = $ts->getInstanceByDocument($document);
			if ($node !== null)
			{
				$document->setCode('FILED_' . $document->getCode());
				$document->setCodeReference(null);
				$service->file($document->getId());
				$ts->deleteNode($node);
			}
			$this->logCustomDelete($document, array('documentlabel' => $document->getLabel()));
			return $this->sendJSON(array('id' => 0));
		}
		return parent::_execute($context, $request);
	}
}