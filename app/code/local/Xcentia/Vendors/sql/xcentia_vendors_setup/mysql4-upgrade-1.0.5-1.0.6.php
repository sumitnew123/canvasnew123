<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$installer->getTable('xcentia_vendors/vendor')}
ADD COLUMN `website`  varchar(255) ");
$installer->endSetup();
?>