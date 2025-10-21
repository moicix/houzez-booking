<?php
/**
 * Clase para sincronizar propiedades de Houzez con alojamientos de MotoPress
 */

if (!defined('ABSPATH')) {
    exit;
}

class HZB_Accommodation_Sync {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Agregar metabox en propiedades de Houzez
        add_action('add_meta_boxes', array($this, 'add_sync_metabox'));
        
        // Guardar configuración de sincronización
        add_action('save_post_property', array($this, 'save_sync_settings'), 20, 3);
        
        // AJAX para sincronización manual
        add_action('wp_ajax_hzb_sync_property', array($this, 'ajax_sync_property'));
    }
    
    /**
     * Agregar metabox de sincronización
     */
    public function add_sync_metabox() {
        add_meta_box(
            'hzb_sync_metabox',
            __('Houzez Booking - Sincronización', 'houzez-booking'),
            array($this, 'render_sync_metabox'),
            'property',
            'side',
            'default'
        );
    }
    
    /**
     * Renderizar metabox
     */
    public function render_sync_metabox($post) {
        wp_nonce_field('hzb_sync_nonce', 'hzb_sync_nonce');
        
        $sync_enabled = get_post_meta($post->ID, '_hzb_sync_enabled', true);
        $accommodation_id = get_post_meta($post->ID, '_motopress_accommodation_type_id', true);
        $last_sync = get_post_meta($post->ID, '_hzb_last_sync', true);
        $sync_status = get_post_meta($post->ID, '_hzb_sync_status', true);
        
        ?>
        <div class="hzb-sync-container">
            <p>
                <label>
                    <input type="checkbox" name="hzb_sync_enabled" value="1" <?php checked($sync_enabled, '1'); ?>>
                    <?php _e('Habilitar reservas para esta propiedad', 'houzez-booking'); ?>
                </label>
            </p>
            
            <?php if ($accommodation_id): ?>
                <div class="hzb-sync-info">
                    <p>
                        <strong><?php _e('Estado:', 'houzez-booking'); ?></strong><br>
                        <span class="hzb-status hzb-status-<?php echo esc_attr($sync_status); ?>">
                            <?php echo $this->get_status_label($sync_status); ?>
                        </span>
                    </p>
                    <p>
                        <strong><?php _e('ID de Alojamiento:', 'houzez-booking'); ?></strong><br>
                        #<?php echo esc_html($accommodation_id); ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if ($last_sync): ?>
                <p class="hzb-last-sync">
                    <strong><?php _e('Última sincronización:', 'houzez-booking'); ?></strong><br>
                    <small><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_sync)); ?></small>
                </p>
            <?php endif; ?>
            
            <p class="hzb-sync-actions">
                <button type="button" class="button button-secondary hzb-sync-now" data-property-id="<?php echo esc_attr($post->ID); ?>">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('Sincronizar ahora', 'houzez-booking'); ?>
                </button>
                <span class="spinner"></span>
            </p>
            
            <div class="hzb-sync-result" style="display:none;"></div>
            
            <?php if ($accommodation_id): ?>
            <p class="hzb-view-bookings">
                <a href="<?php echo admin_url('edit.php?post_type=mphb_booking&accommodation_type=' . $accommodation_id); ?>" class="button button-small">
                    <?php _e('Ver reservas', 'houzez-booking'); ?>
                </a>
            </p>
            <?php endif; ?>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.hzb-sync-now').on('click', function() {
                var $btn = $(this);
                var $spinner = $btn.next('.spinner');
                var $result = $('.hzb-sync-result');
                var propertyId = $btn.data('property-id');
                
                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                $result.hide();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'hzb_sync_property',
                        property_id: propertyId,
                        nonce: '<?php echo wp_create_nonce('hzb_sync_ajax'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $result.html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>').fadeIn();
                            
                            if (response.data.reload) {
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            }
                        } else {
                            $result.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>').fadeIn();
                        }
                    },
                    error: function() {
                        $result.html('<div class="notice notice-error inline"><p><?php _e('Error al sincronizar', 'houzez-booking'); ?></p></div>').fadeIn();
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                    }
                });
            });
        });
        </script>
        
        <style>
        .hzb-sync-container { padding: 10px 0; }
        .hzb-sync-container p { margin: 10px 0; }
        .hzb-sync-result { margin-top: 10px; }
        .hzb-sync-result .notice { margin: 0; padding: 5px 10px; }
        .hzb-sync-info { background: #f0f0f1; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .hzb-status { 
            display: inline-block; 
            padding: 3px 8px; 
            border-radius: 3px; 
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .hzb-status-synced { background: #00a32a; color: #fff; }
        .hzb-status-pending { background: #f0b849; color: #fff; }
        .hzb-status-error { background: #d63638; color: #fff; }
        .hzb-sync-actions .dashicons { font-size: 16px; width: 16px; height: 16px; margin-right: 3px; }
        .hzb-last-sync { color: #666; }
        .hzb-view-bookings { border-top: 1px solid #dcdcde; padding-top: 10px; margin-top: 10px; }
        </style>
        <?php
    }
    
    /**
     * Guardar configuración de sincronización
     */
    public function save_sync_settings($post_id, $post, $update) {
        // Verificar nonce
        if (!isset($_POST['hzb_sync_nonce']) || !wp_verify_nonce($_POST['hzb_sync_nonce'], 'hzb_sync_nonce')) {
            return;
        }
        
        // Verificar permisos
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Evitar auto-guardados
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Guardar estado de sincronización
        $sync_enabled = isset($_POST['hzb_sync_enabled']) ? '1' : '0';
        update_post_meta($post_id, '_hzb_sync_enabled', $sync_enabled);
        
        // Si la sincronización está habilitada, sincronizar ahora
        if ($sync_enabled === '1' && get_option('hzb_auto_sync', true)) {
            $this->sync_property($post_id);
        }
    }
    
    /**
     * AJAX: Sincronizar propiedad
     */
    public function ajax_sync_property() {
        check_ajax_referer('hzb_sync_ajax', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array(
                'message' => __('No tienes permisos para realizar esta acción', 'houzez-booking')
            ));
        }
        
        $property_id = absint($_POST['property_id']);
        
        if (!$property_id) {
            wp_send_json_error(array(
                'message' => __('ID de propiedad no válido', 'houzez-booking')
            ));
        }
        
        $result = $this->sync_property($property_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Sincronización completada exitosamente', 'houzez-booking'),
                'reload' => true
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error al sincronizar la propiedad', 'houzez-booking')
            ));
        }
    }
    
    /**
     * Sincronizar propiedad con MotoPress
     */
    public function sync_property($property_id) {
        // Verificar que la sincronización esté habilitada
        if (get_post_meta($property_id, '_hzb_sync_enabled', true) !== '1') {
            return false;
        }
        
        $property = get_post($property_id);
        if (!$property || $property->post_type !== 'property') {
            return false;
        }
        
        // Marcar como pendiente
        update_post_meta($property_id, '_hzb_sync_status', 'pending');
        
        // Preparar datos del alojamiento
        $accommodation_data = $this->prepare_accommodation_data($property_id);
        
        if (!$accommodation_data) {
            update_post_meta($property_id, '_hzb_sync_status', 'error');
            return false;
        }
        
        // Verificar si ya existe un alojamiento vinculado
        $existing_id = get_post_meta($property_id, '_motopress_accommodation_type_id', true);
        
        $api_handler = new HZB_API_Handler();
        
        if ($existing_id) {
            // Actualizar alojamiento existente
            $accommodation_data['id'] = $existing_id;
        }
        
        // Realizar la llamada a la API interna
        $request = new WP_REST_Request('POST', '/houzez-booking/v1/accommodation-types');
        $request->set_body_params($accommodation_data);
        
        $response = $api_handler->create_accommodation_type($request);
        
        if (!is_wp_error($response)) {
            update_post_meta($property_id, '_hzb_sync_status', 'synced');
            update_post_meta($property_id, '_hzb_last_sync', current_time('timestamp'));
            return true;
        } else {
            update_post_meta($property_id, '_hzb_sync_status', 'error');
            return false;
        }
    }
    
    /**
     * Preparar datos de alojamiento desde propiedad de Houzez
     */
    private function prepare_accommodation_data($property_id) {
        $property = get_post($property_id);
        
        if (!$property) {
            return false;
        }
        
        // Datos básicos
        $data = array(
            'property_id' => $property_id,
            'title' => $property->post_title,
            'description' => $property->post_content,
            'excerpt' => $property->post_excerpt,
        );
        
        // Capacidad de huéspedes
        $guests = get_post_meta($property_id, 'fave_property_guests', true);
        if ($guests) {
            $data['adults'] = absint($guests);
            $data['total_capacity'] = absint($guests);
        }
        
        // Dormitorios
        $bedrooms = get_post_meta($property_id, 'fave_property_bedrooms', true);
        if ($bedrooms) {
            $data['bed_type'] = sprintf(__('%s dormitorios', 'houzez-booking'), $bedrooms);
        }
        
        // Tamaño
        $size = get_post_meta($property_id, 'fave_property_size', true);
        if ($size) {
            $data['size'] = floatval($size);
        }
        
        // Vista/ubicación
        $address = get_post_meta($property_id, 'fave_property_address', true);
        if ($address) {
            $data['view'] = sanitize_text_field($address);
        }
        
        // Amenidades de Houzez a términos de MotoPress
        $amenities = wp_get_post_terms($property_id, 'property_feature', array('fields' => 'ids'));
        if (!empty($amenities) && !is_wp_error($amenities)) {
            $data['amenities'] = $amenities;
        }
        
        return $data;
    }
    
    /**
     * Obtener etiqueta de estado
     */
    private function get_status_label($status) {
        $labels = array(
            'synced' => __('Sincronizado', 'houzez-booking'),
            'pending' => __('Pendiente', 'houzez-booking'),
            'error' => __('Error', 'houzez-booking'),
        );
        
        return isset($labels[$status]) ? $labels[$status] : __('Sin sincronizar', 'houzez-booking');
    }
}