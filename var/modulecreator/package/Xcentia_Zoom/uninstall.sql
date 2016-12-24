-- add table prefix if you have one
DROP TABLE IF EXISTS xcentia_zoom_user;
DROP TABLE IF EXISTS xcentia_zoom_meeting;
DELETE FROM core_resource WHERE code = 'xcentia_zoom_setup';
DELETE FROM core_config_data WHERE path like 'xcentia_zoom/%';