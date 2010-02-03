<?php
/**
 * @package modules.shipping.tests
 */
abstract class shipping_tests_AbstractBaseIntegrationTest extends shipping_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->loadSQLResource('integration-test.sql', true, false);
	}
}