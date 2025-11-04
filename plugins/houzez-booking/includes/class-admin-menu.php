<?php

if (!defined('ABSPATH')) {
    exit;
}

class HZB_Admin_Menu {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_menu'));
    }

    public function add_menu() {
        add_menu_page(
            __('Houzez Booking', 'houzez-booking'),
            __('Houzez Booking', 'houzez-booking'),
            'manage_options',
            'houzez-booking',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        // TODO: Create the settings page UI
        echo '<h1>Houzez Booking Settings</h1>';
    }
}
