<?php
/**
 * Plugin Name: Devnetix Form Data Manager
 * Description: A custom plugin to manage and display data from a custom database table.
 * Version: 1.0
 * Author: DevNetix
 */

class DevnetixContactFormManager {
    /**
     * Registers activation and deactivation hooks for the plugin.
     *
     * - Activation hook: Calls the `activate` method to create the database table.
     * - Deactivation hook: Calls the `deactivate` method for potential cleanup tasks.
     */
    public function __construct() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        /**
         * Initializes the plugin on WordPress init action.
         *
         * This ensures the plugin loads its functionalities after WordPress core is ready.
         */
        add_action('init', array($this, 'initialize_plugin'));
        add_action('wp_enqueue_scripts', array($this, 'dcfm_enqueue_styles'));
    }
    /**
     * Handles plugin activation tasks.
     *
     * This method is called when the plugin is activated. It creates the necessary database table for storing contact form submissions.
     */
    public function activate() {
        require_once plugin_dir_path(__FILE__) . 'includes/class-dcfm-database.php';
        $db = new DCFM_Database();
        $db->create_table();
    }
    /**
     * Initializes required plugin components when WordPress is ready.
     *
     * This method is called on WordPress init action and loads essential functionalities:
     * - Shortcodes: Integrates the `DCFM_Shortcodes` class for contact form display.
     * - REST API: Instantiates the `DCFM_REST_API` class to manage contact data via API.
     */
    public function initialize_plugin() {
        require_once plugin_dir_path(__FILE__) . 'includes/class-dcfm-shortcodes.php';
        $shortcodes = new DCFM_Shortcodes();

        require_once plugin_dir_path(__FILE__) . 'includes/class-dcfm-rest-api.php';
        $rest_api = new DCFM_REST_API();
    }

    public function dcfm_enqueue_styles() {
        // Use plugins_url() to get the correct URL for the stylesheet
        wp_enqueue_style('dcfm-style', plugins_url('/includes/assets/css/style.css', __FILE__), array(), '1.0.0', 'all');
    }
    
}
/**
 * Creates an instance of the DevnetixContactFormManager class to manage the plugin.
 */
$devnetix_contact_form_manager = new DevnetixContactFormManager();
