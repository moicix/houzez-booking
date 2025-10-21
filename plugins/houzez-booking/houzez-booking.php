<?php
/**
 * Plugin Name: Houzez Booking
 * Plugin URI: https://tupagina.com/mi-plugin
* Description: Integra capacidades de reservas y gestión de alojamientos en Houzez usando MotoPress Hotel Booking
 * Version: 1.0.0
 * Author: Tu Nombre
 * Author URI: https://tupagina.com
 * License: GPL2
 * Text Domain: houzez-booking
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('HZB_VERSION', '1.0.0');
define('HZB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HZB_PLUGIN_URL', plugin_dir_url(__FILE__));

class Houzez_Booking {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->check_dependencies();
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Verificar que MotoPress y Houzez estén activos
     */
    private function check_dependencies() {
        add_action('admin_init', array($this, 'verify_dependencies'));
    }
    
    public function verify_dependencies() {
        $motopress_active = class_exists('HotelBookingPlugin');
        $houzez_active = wp_get_theme()->get('TextDomain') === 'houzez' || wp_get_theme()->get('Template') === 'houzez';
        
        if (!$motopress_active || !$houzez_active) {
            add_action('admin_notices', array($this, 'dependency_notice'));
            deactivate_plugins(plugin_basename(__FILE__));
        }
    }
    
    public function dependency_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Houzez Booking requiere que MotoPress Hotel Booking esté activo y el tema Houzez instalado.', 'houzez-booking'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Cargar clases necesarias
     */
    private function load_dependencies() {
        require_once HZB_PLUGIN_DIR . 'includes/class-api-handler.php';
        require_once HZB_PLUGIN_DIR . 'includes/class-accommodation-sync.php';
        require_once HZB_PLUGIN_DIR . 'includes/class-availability-checker.php';
        
        if (is_admin()) {
            require_once HZB_PLUGIN_DIR . 'admin/settings.php';
        }
    }
    
    /**
     * Inicializar hooks
     */
    private function init_hooks() {
        add_action('rest_api_init', array($this, 'register_custom_endpoints'));
        add_action('init', array($this, 'init_components'));
        
        // Hook para sincronizar propiedades de Houzez con MotoPress
        add_action('save_post_property', array($this, 'sync_property_to_motopress'), 10, 3);
        
        // Agregar scripts y estilos en el frontend
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Agregar scripts y estilos en el admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Registrar endpoints personalizados de la API
     */
    public function register_custom_endpoints() {
        $api_handler = new HZB_API_Handler();
        $api_handler->register_routes();
    }
    
    /**
     * Inicializar componentes
     */
    public function init_components() {
        HZB_Accommodation_Sync::get_instance();
        HZB_Availability_Checker::get_instance();
    }
    
    /**
     * Sincronizar propiedades cuando se guarden
     */
    public function sync_property_to_motopress($post_id, $post, $update) {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        
        $sync = HZB_Accommodation_Sync::get_instance();
        $sync->sync_property($post_id);
    }
    
    /**
     * Cargar assets del frontend
     */
    public function enqueue_frontend_assets() {
        if (!is_singular('property')) {
            return;
        }
        
        wp_enqueue_style(
            'houzez-booking',
            HZB_PLUGIN_URL . 'assets/css/houzez-booking.css',
            array(),
            HZB_VERSION
        );
        
        wp_enqueue_script(
            'houzez-booking',
            HZB_PLUGIN_URL . 'assets/js/houzez-booking.js',
            array('jquery'),
            HZB_VERSION,
            true
        );
        
        wp_localize_script('houzez-booking', 'houzezBooking', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('houzez-booking/v1/'),
            'nonce' => wp_create_nonce('houzez_booking_nonce'),
            'propertyId' => get_the_ID(),
            'i18n' => array(
                'checkAvailability' => __('Verificar disponibilidad', 'houzez-booking'),
                'loading' => __('Cargando...', 'houzez-booking'),
                'available' => __('Disponible', 'houzez-booking'),
                'notAvailable' => __('No disponible', 'houzez-booking'),
                'selectDates' => __('Seleccione las fechas', 'houzez-booking'),
            )
        ));
    }
    
    /**
     * Cargar assets del admin
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;
        
        if ($post_type !== 'property') {
            return;
        }
        
        wp_enqueue_style(
            'houzez-booking-admin',
            HZB_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            HZB_VERSION
        );
        
        wp_enqueue_script(
            'houzez-booking-admin',
            HZB_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            HZB_VERSION,
            true
        );
    }
}

// Inicializar el plugin
function houzez_booking_init() {
    return Houzez_Booking::get_instance();
}

add_action('plugins_loaded', 'houzez_booking_init');

// Hooks de activación y desactivación
register_activation_hook(__FILE__, 'houzez_booking_activate');
register_deactivation_hook(__FILE__, 'houzez_booking_deactivate');

function houzez_booking_activate() {
    // Crear opciones iniciales
    add_option('hzb_version', HZB_VERSION);
    add_option('hzb_sync_enabled', true);
    add_option('hzb_auto_sync', true);
    add_option('hzb_show_booking_widget', true);
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

function houzez_booking_deactivate() {
    // Limpiar tareas programadas si las hay
    wp_clear_scheduled_hook('hzb_daily_sync');
    
    flush_rewrite_rules();
}

/**
 * Función helper para obtener la instancia
 */
function houzez_booking() {
    return Houzez_Booking::get_instance();
}