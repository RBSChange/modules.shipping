<?php
/**
 * @package modules.shipping.tests
 */
abstract class shipping_tests_AbstractBaseFunctionalTest extends shipping_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->loadSQLResource('functional-test.sql', true, false);
	}
}