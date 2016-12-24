-- add table prefix if you have one
DELETE FROM eav_attribute WHERE entity_type_id IN (SELECT entity_type_id FROM eav_entity_type WHERE entity_type_code = 'xcentia_response_form');
DELETE FROM eav_entity_type WHERE entity_type_code = 'xcentia_response_form';
DROP TABLE IF EXISTS xcentia_response_form_comment_store;
DROP TABLE IF EXISTS xcentia_response_form_comment;
DROP TABLE IF EXISTS xcentia_response_form_int;
DROP TABLE IF EXISTS xcentia_response_form_decimal;
DROP TABLE IF EXISTS xcentia_response_form_datetime;
DROP TABLE IF EXISTS xcentia_response_form_varchar;
DROP TABLE IF EXISTS xcentia_response_form_text;
DROP TABLE IF EXISTS xcentia_response_form;
DROP TABLE IF EXISTS xcentia_response_eav_attribute;
DELETE FROM core_resource WHERE code = 'xcentia_response_setup';
DELETE FROM core_config_data WHERE path like 'xcentia_response/%';