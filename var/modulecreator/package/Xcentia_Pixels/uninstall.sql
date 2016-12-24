-- add table prefix if you have one
DROP TABLE IF EXISTS xcentia_pixels_object;
DROP TABLE IF EXISTS xcentia_pixels_pixel;
DROP TABLE IF EXISTS xcentia_pixels_vote;
DELETE FROM core_resource WHERE code = 'xcentia_pixels_setup';
DELETE FROM core_config_data WHERE path like 'xcentia_pixels/%';