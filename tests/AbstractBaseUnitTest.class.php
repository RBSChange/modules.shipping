<?php
/**
 * @package modules.shipping.tests
 */
abstract class shipping_tests_AbstractBaseUnitTest extends shipping_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->resetDatabase();
	}
}