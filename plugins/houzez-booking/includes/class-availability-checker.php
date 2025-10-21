<?php
/**
 * Verificador de disponibilidad para propiedades
 */

if (!defined('ABSPATH')) {
    exit;
}

class HZB_Availability_Checker {
    
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
        // Agregar widget de disponibilidad en propiedades
        add_action('houzez_single_property_content', array($this, 'add_availability_widget'), 15);
        
        // AJAX para verificar disponibilidad
        add_action('wp_ajax_hzb_check_availability', array($this, 'ajax_check_availability'));
        add_action('wp_ajax_nopriv_hzb_check_availability', array($this, 'ajax_check_availability'));
        
        // Shortcode para widget de disponibilidad
        add_shortcode('houzez_booking_availability', array($this, 'availability_shortcode'));
    }
    
    /**
     * Agregar widget de disponibilidad
     */
    public function add_availability_widget() {
        if (!get_option('hzb_show_booking_widget', true)) {
            return;
        }
        
        global $post;
        
        // Verificar si la propiedad tiene sincronización habilitada
        $sync_enabled = get_post_meta($post->ID, '_hzb_sync_enabled', true);
        $accommodation_id = get_post_meta($post->ID, '_motopress_accommodation_type_id', true);
        
        if ($sync_enabled !== '1' || !$accommodation_id) {
            return;
        }
        
        $this->render_availability_widget($post->ID);
    }
    
    /**
     * Renderizar widget de disponibilidad
     */
    public function render_availability_widget($property_id) {
        $accommodation_id = get_post_meta($property_id, '_motopress_accommodation_type_id', true);
        ?>
        <div class="hzb-availability-widget property-section">
            <div class="block-title-wrap">
                <h3 class="block-title">
                    <span><?php _e('Verificar Disponibilidad', 'houzez-booking'); ?></span>
                </h3>
            </div>
            
            <div class="hzb-availability-form">
                <form id="hzb-availability-form" data-property-id="<?php echo esc_attr($property_id); ?>" data-accommodation-id="<?php echo esc_attr($accommodation_id); ?>">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="hzb-check-in"><?php _e('Fecha de entrada', 'houzez-booking'); ?></label>
                            <input type="date" 
                                   id="hzb-check-in" 
                                   name="check_in_date" 
                                   class="form-control" 
                                   min="<?php echo date('Y-m-d'); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="hzb-check-out"><?php _e('Fecha de salida', 'houzez-booking'); ?></label>
                            <input type="date" 
                                   id="hzb-check-out" 
                                   name="check_out_date" 
                                   class="form-control" 
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="hzb-adults"><?php _e('Adultos', 'houzez-booking'); ?></label>
                            <select id="hzb-adults" name="adults" class="form-control">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php selected($i, 2); ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="hzb-children"><?php _e('Niños', 'houzez-booking'); ?></label>
                            <select id="hzb-children" name="children" class="form-control">
                                <?php for ($i = 0; $i <= 6; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="hzb-availability-result" style="display:none;"></div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="hzb-btn-text"><?php _e('Verificar Disponibilidad', 'houzez-booking'); ?></span>
                        <span class="hzb-btn-loading" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> <?php _e('Verificando...', 'houzez-booking'); ?>
                        </span>
                    </button>
                </form>
            </div>
        </div>
        
        <style>
        .hzb-availability-widget {
            margin: 30px 0;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .hzb-availability-form .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .hzb-availability-form .form-group {
            flex: 1;
        }
        
        .hzb-availability-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .hzb-availability-form .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .hzb-availability-result {
            margin: 20px 0;
            padding: 15px;
            border-radius: 4px;
        }
        
        .hzb-availability-result.available {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .hzb-availability-result.not-available {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .hzb-price-info {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .hzb-price-info strong {
            font-size: 18px;
            color: #00aaef;
        }
        
        @media (max-width: 768px) {
            .hzb-availability-form .form-row {
                flex-direction: column;
            }
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('#hzb-availability-form').on('submit', function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                var $btnText = $btn.find('.hzb-btn-text');
                var $btnLoading = $btn.find('.hzb-btn-loading');
                var $result = $form.find('.hzb-availability-result');
                
                var formData = {
                    action: 'hzb_check_availability',
                    nonce: '<?php echo wp_create_nonce('hzb_availability_nonce'); ?>',
                    property_id: $form.data('property-id'),
                    accommodation_id: $form.data('accommodation-id'),
                    check_in_date: $('#hzb-check-in').val(),
                    check_out_date: $('#hzb-check-out').val(),
                    adults: $('#hzb-adults').val(),
                    children: $('#hzb-children').val()
                };
                
                $btn.prop('disabled', true);
                $btnText.hide();
                $btnLoading.show();
                $result.hide();
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $result.html(response.data.html)
                                   .removeClass('not-available')
                                   .addClass('available')
                                   .fadeIn();
                        } else {
                            $result.html(response.data.html)
                                   .removeClass('available')
                                   .addClass('not-available')
                                   .fadeIn();
                        }
                    },
                    error: function() {
                        $result.html('<p><?php _e('Error al verificar disponibilidad. Intente nuevamente.', 'houzez-booking'); ?></p>')
                               .removeClass('available')
                               .addClass('not-available')
                               .fadeIn();
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btnText.show();
                        $btnLoading.hide();
                    }
                });
            });
            
            // Actualizar fecha mínima de salida cuando cambia la entrada
            $('#hzb-check-in').on('change', function() {
                var checkInDate = new Date($(this).val());
                var minCheckOut = new Date(checkInDate);
                minCheckOut.setDate(minCheckOut.getDate() + 1);
                
                var minCheckOutStr = minCheckOut.toISOString().split('T')[0];
                $('#hzb-check-out').attr('min', minCheckOutStr);
                
                // Si la fecha de salida es anterior, ajustarla
                var checkOutDate = new Date($('#hzb-check-out').val());
                if (checkOutDate <= checkInDate) {
                    $('#hzb-check-out').val(minCheckOutStr);
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX: Verificar disponibilidad
     */
    public function ajax_check_availability() {
        check_ajax_referer('hzb_availability_nonce', 'nonce');
        
        $property_id = absint($_POST['property_id']);
        $accommodation_id = absint($_POST['accommodation_id']);
        $check_in = sanitize_text_field($_POST['check_in_date']);
        $check_out = sanitize_text_field($_POST['check_out_date']);
        $adults = absint($_POST['adults']);
        $children = absint($_POST['children']);
        
        // Validar datos
        if (!$property_id || !$accommodation_id || !$check_in || !$check_out) {
            wp_send_json_error(array(
                'html' => '<p>' . __('Datos incompletos.', 'houzez-booking') . '</p>'
            ));
        }
        
        // Llamar a la API
        $api_handler = new HZB_API_Handler();
        $request = new WP_REST_Request('GET', '/houzez-booking/v1/property/' . $property_id . '/availability');
        $request->set_param('id', $property_id);
        $request->set_param('check_in_date', $check_in);
        $request->set_param('check_out_date', $check_out);
        $request->set_param('adults', $adults);
        $request->set_param('children', $children);
        
        $response = $api_handler->get_property_availability($request);
        
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'html' => '<p>' . __('No disponible para las fechas seleccionadas.', 'houzez-booking') . '</p>'
            ));
        }
        
        // Verificar disponibilidad en la respuesta
        $available = false;
        $price_info = '';
        
        if (isset($response['availability']) && is_array($response['availability'])) {
            foreach ($response['availability'] as $item) {
                if ($item['accommodation_type'] == $accommodation_id) {
                    $available = !empty($item['accommodations']);
                    if ($available && isset($item['base_price'])) {
                        $nights = $this->calculate_nights($check_in, $check_out);
                        $total = $item['base_price'] * $nights;
                        $currency = get_option('hzb_currency_symbol', '$');
                        
                        $price_info = sprintf(
                            '<div class="hzb-price-info"><strong>%s%s</strong> %s<br><small>%s: %s%s x %d %s</small></div>',
                            $currency,
                            number_format($total, 2),
                            __('Total', 'houzez-booking'),
                            __('Precio base', 'houzez-booking'),
                            $currency,
                            number_format($item['base_price'], 2),
                            $nights,
                            _n('noche', 'noches', $nights, 'houzez-booking')
                        );
                    }
                    break;
                }
            }
        }
        
        if ($available) {
            $html = '<p><strong><i class="fas fa-check-circle"></i> ' . __('¡Disponible para las fechas seleccionadas!', 'houzez-booking') . '</strong></p>';
            $html .= $price_info;
            $html .= '<p><a href="' . add_query_arg(array(
                'booking' => 'true',
                'check_in' => $check_in,
                'check_out' => $check_out,
                'adults' => $adults,
                'children' => $children
            ), get_permalink($property_id)) . '" class="btn btn-success btn-block">' . __('Reservar Ahora', 'houzez-booking') . '</a></p>';
            
            wp_send_json_success(array('html' => $html));
        } else {
            wp_send_json_error(array(
                'html' => '<p><strong><i class="fas fa-times-circle"></i> ' . __('No disponible para las fechas seleccionadas.', 'houzez-booking') . '</strong></p><p>' . __('Por favor, seleccione otras fechas.', 'houzez-booking') . '</p>'
            ));
        }
    }
    
    /**
     * Shortcode de disponibilidad
     */
    public function availability_shortcode($atts) {
        $atts = shortcode_atts(array(
            'property_id' => get_the_ID(),
        ), $atts);
        
        ob_start();
        $this->render_availability_widget($atts['property_id']);
        return ob_get_clean();
    }
    
    /**
     * Calcular número de noches
     */
    private function calculate_nights($check_in, $check_out) {
        $date1 = new DateTime($check_in);
        $date2 = new DateTime($check_out);
        $diff = $date1->diff($date2);
        return $diff->days;
    }
}