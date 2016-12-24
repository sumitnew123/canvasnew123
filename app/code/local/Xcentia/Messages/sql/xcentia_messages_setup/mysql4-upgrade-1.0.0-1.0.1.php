<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$installer->getTable('xcentia_messages/message')}
ADD COLUMN `type`  varchar(255) ");
$installer->endSetup();
?>