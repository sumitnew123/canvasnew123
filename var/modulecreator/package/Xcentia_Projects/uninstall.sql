-- add table prefix if you have one
DROP TABLE IF EXISTS xcentia_projects_project;
DROP TABLE IF EXISTS xcentia_projects_bid;
DROP TABLE IF EXISTS xcentia_projects_message;
DROP TABLE IF EXISTS xcentia_projects_attachment;
DELETE FROM core_resource WHERE code = 'xcentia_projects_setup';
DELETE FROM core_config_data WHERE path like 'xcentia_projects/%';