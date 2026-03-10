<?php
/**
 * Plugin Name: Multi-Step Forms Manager
 * Plugin URI: https://example.com/multi-step-forms
 * Description: A WordPress plugin to create and manage multi-step forms with Personal and Business account sections.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: multi-step-forms
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MSF_VERSION', '1.0.0');
define('MSF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MSF_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once MSF_PLUGIN_DIR . 'includes/class-msf-admin.php';
require_once MSF_PLUGIN_DIR . 'includes/class-msf-frontend.php';
require_once MSF_PLUGIN_DIR . 'includes/class-msf-form-builder.php';

// Activation hook
register_activation_hook(__FILE__, 'msf_activate');
function msf_activate() {
    // Create database tables if needed
    msf_create_tables();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'msf_deactivate');
function msf_deactivate() {
    // Cleanup if needed
}

// Uninstall hook
register_uninstall_hook(__FILE__, 'msf_uninstall');
function msf_uninstall() {
    // Remove database tables and options
    msf_drop_tables();
}

// Initialize the plugin
function msf_init() {
    $admin = new MSF_Admin();
    $frontend = new MSF_Frontend();
    $form_builder = new MSF_Form_Builder();
}
add_action('plugins_loaded', 'msf_init');

// Enqueue scripts and styles
function msf_enqueue_scripts() {
    if (!is_admin()) {
        // core styles
        wp_enqueue_style('msf-style', MSF_PLUGIN_URL . 'assets/css/msf-style.css', array(), MSF_VERSION);
        // font awesome for icons used in the template
        wp_enqueue_style('msf-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', array(), '6.5.0');
        wp_enqueue_script('msf-script', MSF_PLUGIN_URL . 'assets/js/msf-script.js', array('jquery'), MSF_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'msf_enqueue_scripts');

// Shortcode for displaying the form
function msf_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'personal', // personal or business
    ), $atts);

    // Check if type is set in URL
    if (isset($_GET['msf_type']) && in_array($_GET['msf_type'], array('personal', 'business'))) {
        $atts['type'] = $_GET['msf_type'];
    }

    ob_start();
    MSF_Frontend::display_form($atts['type']);
    return ob_get_clean();
}
add_shortcode('multi_step_form', 'msf_form_shortcode');

// Database functions
function msf_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'msf_forms';

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        form_type varchar(50) NOT NULL,
        form_data longtext NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function msf_drop_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'msf_forms';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}