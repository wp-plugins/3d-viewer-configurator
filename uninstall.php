<?php
// Backup potential configurations
rename(WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)).'/data/', WP_PLUGIN_DIR.'/3d_viewer_configurator-databackup');

// Cleanup DB
global $wpdb;
$db = &$wpdb;

// Database table name
$table_name = $db->prefix.'vtpkonfigurator';

$sql = "DROP TABLE IF EXISTS " . $tablename;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
?>