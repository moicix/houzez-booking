<?php

if (!defined('ABSPATH')) {
    exit;
}

class HZB_Houzez_Integration {

    private static $instance = null;
    private $api_handler;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->api_handler = HZB_MotoPress_API_Handler::get_instance();
        add_action('save_post_property', array($this, 'property_created'), 10, 2);
    }

    /**
     * Called when a property is created or updated.
     */
    public function property_created($post_id, $post) {
        // Step 1: Create Accommodation Type
        $accommodation_type_data = array(
            'title' => $post->post_title,
            // TODO: Add other data from the property
        );
        $this->api_handler->create_accommodation_type($accommodation_type_data);

        // Step 2: Create Accommodation
        // TODO: Get the accommodation type ID from the previous step
        $accommodation_data = array(
            'accommodation_type_id' => 0, // Replace with the actual ID
            'title' => $post->post_title,
        );
        $this->api_handler->create_accommodation($accommodation_data);
    }
}
