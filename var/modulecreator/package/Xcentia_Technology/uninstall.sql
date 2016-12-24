-- add table prefix if you have one
DROP TABLE IF EXISTS xcentia_technology_technology;
DELETE FROM core_resource WHERE code = 'xcentia_technology_setup';
DELETE FROM core_config_data WHERE path like 'xcentia_technology/%';