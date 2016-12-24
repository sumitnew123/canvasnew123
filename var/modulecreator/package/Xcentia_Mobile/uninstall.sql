-- add table prefix if you have one
DROP TABLE IF EXISTS xcentia_mobile_device;
DELETE FROM core_resource WHERE code = 'xcentia_mobile_setup';
DELETE FROM core_config_data WHERE path like 'xcentia_mobile/%';