-- add table prefix if you have one
DROP TABLE IF EXISTS xcentia_vendors_vendor_comment;
DROP TABLE IF EXISTS xcentia_vendors_vendor_category;
DROP TABLE IF EXISTS xcentia_vendors_vendor;
DROP TABLE IF EXISTS xcentia_vendors_coverage;
DROP TABLE IF EXISTS xcentia_vendors_order;
DROP TABLE IF EXISTS xcentia_vendors_service_category;
DROP TABLE IF EXISTS xcentia_vendors_service;
DROP TABLE IF EXISTS xcentia_vendors_servicemap;
DROP TABLE IF EXISTS xcentia_vendors_vendorservice;
DELETE FROM core_resource WHERE code = 'xcentia_vendors_setup';
DELETE FROM core_config_data WHERE path like 'xcentia_vendors/%';