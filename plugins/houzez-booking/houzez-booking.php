<?php
/**
 * Plugin Name: Houzez Booking Integration
 * Description: Integrates Houzez with MotoPress Hotel Booking.
 * Version: 1.0.0
 * Author: Gemini
 */

if (!defined('ABSPATH')) {
    exit;
}

define('HBI_PLUGIN_DIR', plugin_dir_path(__FILE__));

class Houzez_Booking_Integration {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init();
    }

    private function load_dependencies() {
        require_once HBI_PLUGIN_DIR . 'includes/class-motopress-api-handler.php';
        require_once HBI_PLUGIN_DIR . 'includes/class-houzez-integration.php';
        require_once HBI_PLUGIN_DIR . 'includes/class-admin-menu.php';
    }

    private function init() {
        HZB_MotoPress_API_Handler::get_instance();
        HZB_Houzez_Integration::get_instance();
        HZB_Admin_Menu::get_instance();
    }
}

function houzez_booking_integration_init() {
    return Houzez_Booking_Integration::get_instance();
}

add_action('plugins_loaded', 'houzez_booking_integration_init');
