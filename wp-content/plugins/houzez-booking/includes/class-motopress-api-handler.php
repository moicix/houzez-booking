<?php

if (!defined('ABSPATH')) {
    exit;
}

class HZB_MotoPress_API_Handler {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Creates an accommodation type in MotoPress.
     */
    public function create_accommodation_type($data) {
        // TODO: Implement API call to POST /wp-json/mphb/v1/accommodation_types
    }

    /**
     * Creates an accommodation in MotoPress.
     */
    public function create_accommodation($data) {
        // TODO: Implement API call to POST /wp-json/mphb/v1/accommodations
    }

    /**
     * Creates a season in MotoPress.
     */
    public function create_season($data) {
        // TODO: Implement API call to POST /wp-json/mphb/v1/seasons
    }
}
