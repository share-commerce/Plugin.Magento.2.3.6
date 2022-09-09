<?php
 
 /*
 * Created by ShareCommerce
 * Date 8 Sept 2022
 * Create ShareCommerce require table in database when plugin/module is installed in Magento-2
 */

namespace SCPay\SCPayment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;

		/**
     * Prepare database for install
    */
		$installer->startSetup();

        try {
          $statusTable = $installer->getTable('sales_order_status');
          $statusStateTable = $installer->getTable('sales_order_status_state');

          $installer->getConnection()->insertArray(
            $statusTable,
            array('status','label'),
            array(array('status' => 'Pending_SCPay', 'label' => 'Pending SCPay'))
            );

          $installer->getConnection()->insertArray(
            $statusStateTable,
            array(
              'status',
              'state',
              'is_default',
              'visible_on_front'
              ),
            array(
              array(
                'status' => 'Pending_SCPay',
                'state' => 'Pending_SCPay',
                'is_default' => 0,
                'visible_on_front' => 1
                )
              )
            );
      } catch (Exception $e) {}

		$installer->endSetup();
	}
}

?>