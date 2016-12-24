-- add table prefix if you have one
DROP TABLE IF EXISTS xwalker_metadetails_meta;
DELETE FROM core_resource WHERE code = 'xwalker_metadetails_setup';
DELETE FROM core_config_data WHERE path like 'xwalker_metadetails/%';