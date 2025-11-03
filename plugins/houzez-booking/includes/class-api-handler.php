<?php
if (!defined('ABSPATH')) {
    exit;
}

class HZB_API_Handler {
    
    public function register_routes() {
        register_rest_route('houzez-booking/v1', '/accommodation-types', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_accommodation_type'),
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));
    }

    public function create_accommodation_type($request) {
        $params = $request->get_params();
        
        $property_id = isset($params['property_id']) ? absint($params['property_id']) : 0;
        if (!$property_id) {
            return new WP_Error('invalid_property_id', 'Invalid property ID.', array('status' => 400));
        }
        
        $title = isset($params['title']) ? sanitize_text_field($params['title']) : '';
        if (empty($title)) {
            return new WP_Error('missing_title', 'Missing accommodation title.', array('status' => 400));
        }
        
        $existing_id = get_post_meta($property_id, '_motopress_accommodation_type_id', true);
        
        $post_data = array(
            'post_title' => $title,
            'post_content' => isset($params['description']) ? wp_kses_post($params['description']) : '',
            'post_excerpt' => isset($params['excerpt']) ? wp_kses_post($params['excerpt']) : '',
            'post_status' => 'publish',
            'post_type' => 'mphb_room_type',
        );
        
        if ($existing_id) {
            $post_data['ID'] = $existing_id;
            $accommodation_id = wp_update_post($post_data);
        } else {
            $accommodation_id = wp_insert_post($post_data);
        }
        
        if (is_wp_error($accommodation_id)) {
            return $accommodation_id;
        }
        
        update_post_meta($property_id, '_motopress_accommodation_type_id', $accommodation_id);
        
        $response_data = array(
            'id' => $accommodation_id,
            'message' => 'Accommodation type created/updated successfully.'
        );
        
        return new WP_REST_Response($response_data, 200);
    }
}