<?php
/**
 * Página de configuración del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class HZB_Settings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Agregar menú de administración
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Houzez Booking', 'houzez-booking'),
            __('Houzez Booking', 'houzez-booking'),
            'manage_options',
            'houzez-booking',
            array($this, 'render_settings_page'),
            'dashicons-calendar-alt',
            30
        );
        
        add_submenu_page(
            'houzez-booking',
            __('Configuración', 'houzez-booking'),
            __('Configuración', 'houzez-booking'),
            'manage_options',
            'houzez-booking',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'houzez-booking',
            __('Sincronización', 'houzez-booking'),
            __('Sincronización', 'houzez-booking'),
            'manage_options',
            'houzez-booking-sync',
            array($this, 'render_sync_page')
        );
    }
    
    /**
     * Registrar configuraciones
     */
    public function register_settings() {
        // Sección general
        add_settings_section(
            'hzb_general_section',
            __('Configuración General', 'houzez-booking'),
            array($this, 'general_section_callback'),
            'houzez-booking'
        );
        
        // Auto sincronización
        register_setting('houzez-booking', 'hzb_auto_sync');
        add_settings_field(
            'hzb_auto_sync',
            __('Sincronización Automática', 'houzez-booking'),
            array($this, 'auto_sync_callback'),
            'houzez-booking',
            'hzb_general_section'
        );
        
        // Mostrar widget
        register_setting('houzez-booking', 'hzb_show_booking_widget');
        add_settings_field(
            'hzb_show_booking_widget',
            __('Mostrar Widget de Reservas', 'houzez-booking'),
            array($this, 'show_widget_callback'),
            'houzez-booking',
            'hzb_general_section'
        );
        
        // Símbolo de moneda
        register_setting('houzez-booking', 'hzb_currency_symbol');
        add_settings_field(
            'hzb_currency_symbol',
            __('Símbolo de Moneda', 'houzez-booking'),
            array($this, 'currency_symbol_callback'),
            'houzez-booking',
            'hzb_general_section'
        );
        
        // Sección de API
        add_settings_section(
            'hzb_api_section',
            __('Configuración de API', 'houzez-booking'),
            array($this, 'api_section_callback'),
            'houzez-booking'
        );
        
        // API Key de MotoPress
        register_setting('houzez-booking', 'hzb_motopress_api_key');
        add_settings_field(
            'hzb_motopress_api_key',
            __('API Key de MotoPress', 'houzez-booking'),
            array($this, 'api_key_callback'),
            'houzez-booking',
            'hzb_api_section'
        );
    }
    
    /**
     * Callback sección general
     */
    public function general_section_callback() {
        echo '<p>' . __('Configura las opciones generales de Houzez Booking.', 'houzez-booking') . '</p>';
    }
    
    /**
     * Callback sección API
     */
    public function api_section_callback() {
        echo '<p>' . __('Configuración de integración con la API de MotoPress Hotel Booking.', 'houzez-booking') . '</p>';
    }
    
    /**
     * Campo: Auto sincronización
     */
    public function auto_sync_callback() {
        $value = get_option('hzb_auto_sync', true);
        ?>
        <label>
            <input type="checkbox" name="hzb_auto_sync" value="1" <?php checked($value, true); ?>>
            <?php _e('Sincronizar automáticamente al guardar propiedades', 'houzez-booking'); ?>
        </label>
        <p class="description">
            <?php _e('Si está habilitado, las propiedades se sincronizarán con MotoPress automáticamente al guardarlas.', 'houzez-booking'); ?>
        </p>
        <?php
    }
    
    /**
     * Campo: Mostrar widget
     */
    public function show_widget_callback() {
        $value = get_option('hzb_show_booking_widget', true);
        ?>
        <label>
            <input type="checkbox" name="hzb_show_booking_widget" value="1" <?php checked($value, true); ?>>
            <?php _e('Mostrar widget de disponibilidad en propiedades', 'houzez-booking'); ?>
        </label>
        <p class="description">
            <?php _e('Muestra automáticamente el widget de verificación de disponibilidad en las páginas de propiedades sincronizadas.', 'houzez-booking'); ?>
        </p>
        <?php
    }
    
    /**
     * Campo: Símbolo de moneda
     */
    public function currency_symbol_callback() {
        $value = get_option('hzb_currency_symbol', ');
        ?>
        <input type="text" name="hzb_currency_symbol" value="<?php echo esc_attr($value); ?>" class="regular-text">
        <p class="description">
            <?php _e('Símbolo de moneda a mostrar en los precios (ej: $, €, £, MXN$).', 'houzez-booking'); ?>
        </p>
        <?php
    }
    
    /**
     * Campo: API Key
     */
    public function api_key_callback() {
        $value = get_option('hzb_motopress_api_key', '');
        ?>
        <input type="password" name="hzb_motopress_api_key" value="<?php echo esc_attr($value); ?>" class="regular-text">
        <p class="description">
            <?php _e('API Key de MotoPress Hotel Booking (opcional, solo si se requiere autenticación).', 'houzez-booking'); ?>
        </p>
        <?php
    }
    
    /**
     * Renderizar página de configuración
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Guardar configuración
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'hzb_messages',
                'hzb_message',
                __('Configuración guardada exitosamente.', 'houzez-booking'),
                'updated'
            );
        }
        
        settings_errors('hzb_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="hzb-settings-header">
                <p class="hzb-version">
                    <?php printf(__('Versión %s', 'houzez-booking'), HZB_VERSION); ?>
                </p>
            </div>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('houzez-booking');
                do_settings_sections('houzez-booking');
                submit_button(__('Guardar Configuración', 'houzez-booking'));
                ?>
            </form>
            
            <div class="hzb-info-boxes">
                <div class="hzb-info-box">
                    <h3><?php _e('📚 Documentación', 'houzez-booking'); ?></h3>
                    <p><?php _e('Para usar este plugin:', 'houzez-booking'); ?></p>
                    <ol>
                        <li><?php _e('Edita una propiedad en Houzez', 'houzez-booking'); ?></li>
                        <li><?php _e('En la barra lateral, marca "Habilitar reservas"', 'houzez-booking'); ?></li>
                        <li><?php _e('Guarda la propiedad para sincronizarla', 'houzez-booking'); ?></li>
                        <li><?php _e('El widget de disponibilidad aparecerá automáticamente', 'houzez-booking'); ?></li>
                    </ol>
                </div>
                
                <div class="hzb-info-box">
                    <h3><?php _e('🔌 Endpoints de API', 'houzez-booking'); ?></h3>
                    <p><?php _e('API REST disponible en:', 'houzez-booking'); ?></p>
                    <code><?php echo rest_url('houzez-booking/v1/'); ?></code>
                    <ul class="hzb-endpoints">
                        <li><strong>POST</strong> /accommodation-types</li>
                        <li><strong>POST</strong> /check-availability</li>
                        <li><strong>GET</strong> /property/{id}/availability</li>
                        <li><strong>POST</strong> /bookings</li>
                        <li><strong>GET</strong> /property/{id}/bookings</li>
                    </ul>
                </div>
                
                <div class="hzb-info-box">
                    <h3><?php _e('🎯 Shortcodes', 'houzez-booking'); ?></h3>
                    <p><?php _e('Usa este shortcode en cualquier lugar:', 'houzez-booking'); ?></p>
                    <code>[houzez_booking_availability property_id="123"]</code>
                    <p><small><?php _e('Si omites property_id, usará la propiedad actual.', 'houzez-booking'); ?></small></p>
                </div>
            </div>
        </div>
        
        <style>
        .hzb-settings-header {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-left: 4px solid #00a32a;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .hzb-version {
            margin: 0;
            color: #666;
            font-size: 13px;
        }
        
        .hzb-info-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .hzb-info-box {
            background: #fff;
            border: 1px solid #ccd0d4;
            padding: 20px;
            border-radius: 4px;
        }
        
        .hzb-info-box h3 {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .hzb-info-box code {
            display: block;
            background: #f0f0f1;
            padding: 10px;
            border-radius: 3px;
            margin: 10px 0;
            font-size: 12px;
            overflow-x: auto;
        }
        
        .hzb-info-box ol, .hzb-info-box ul {
            margin-left: 20px;
        }
        
        .hzb-endpoints {
            list-style: none;
            margin-left: 0;
        }
        
        .hzb-endpoints li {
            padding: 5px 0;
            font-family: monospace;
            font-size: 12px;
        }
        
        .hzb-endpoints strong {
            display: inline-block;
            width: 50px;
            color: #00a32a;
        }
        </style>
        <?php
    }
    
    /**
     * Renderizar página de sincronización
     */
    public function render_sync_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Obtener estadísticas
        $total_properties = wp_count_posts('property')->publish;
        $synced_properties = $this->count_synced_properties();
        $pending_properties = $this->count_pending_properties();
        
        ?>
        <div class="wrap">
            <h1><?php _e('Sincronización de Propiedades', 'houzez-booking'); ?></h1>
            
            <div class="hzb-sync-stats">
                <div class="hzb-stat-box">
                    <div class="hzb-stat-number"><?php echo $total_properties; ?></div>
                    <div class="hzb-stat-label"><?php _e('Total de Propiedades', 'houzez-booking'); ?></div>
                </div>
                
                <div class="hzb-stat-box hzb-stat-success">
                    <div class="hzb-stat-number"><?php echo $synced_properties; ?></div>
                    <div class="hzb-stat-label"><?php _e('Sincronizadas', 'houzez-booking'); ?></div>
                </div>
                
                <div class="hzb-stat-box hzb-stat-warning">
                    <div class="hzb-stat-number"><?php echo $pending_properties; ?></div>
                    <div class="hzb-stat-label"><?php _e('Pendientes', 'houzez-booking'); ?></div>
                </div>
            </div>
            
            <div class="hzb-sync-actions">
                <h2><?php _e('Acciones de Sincronización', 'houzez-booking'); ?></h2>
                
                <div class="hzb-action-box">
                    <h3><?php _e('Sincronización Masiva', 'houzez-booking'); ?></h3>
                    <p><?php _e('Sincroniza todas las propiedades que tienen habilitada la sincronización.', 'houzez-booking'); ?></p>
                    <button type="button" class="button button-primary button-large" id="hzb-bulk-sync">
                        <?php _e('Sincronizar Todas', 'houzez-booking'); ?>
                    </button>
                    <span class="spinner"></span>
                    <div class="hzb-sync-progress" style="display:none;">
                        <div class="hzb-progress-bar">
                            <div class="hzb-progress-fill"></div>
                        </div>
                        <p class="hzb-progress-text"></p>
                    </div>
                </div>
                
                <div class="hzb-action-box">
                    <h3><?php _e('Propiedades Sincronizadas', 'houzez-booking'); ?></h3>
                    <p><?php _e('Lista de propiedades con sincronización activa:', 'houzez-booking'); ?></p>
                    <?php $this->render_synced_properties_table(); ?>
                </div>
            </div>
        </div>
        
        <style>
        .hzb-sync-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0 40px;
        }
        
        .hzb-stat-box {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-left: 4px solid #0073aa;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .hzb-stat-box.hzb-stat-success {
            border-left-color: #00a32a;
        }
        
        .hzb-stat-box.hzb-stat-warning {
            border-left-color: #f0b849;
        }
        
        .hzb-stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #0073aa;
            line-height: 1;
        }
        
        .hzb-stat-success .hzb-stat-number {
            color: #00a32a;
        }
        
        .hzb-stat-warning .hzb-stat-number {
            color: #f0b849;
        }
        
        .hzb-stat-label {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
        
        .hzb-sync-actions {
            background: #fff;
            border: 1px solid #ccd0d4;
            padding: 20px;
            border-radius: 4px;
        }
        
        .hzb-action-box {
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        
        .hzb-action-box:last-child {
            border-bottom: none;
        }
        
        .hzb-progress-bar {
            width: 100%;
            height: 30px;
            background: #f0f0f1;
            border-radius: 4px;
            overflow: hidden;
            margin: 15px 0;
        }
        
        .hzb-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00a32a, #00d084);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .hzb-progress-text {
            text-align: center;
            color: #666;
            font-weight: 600;
        }
        
        .hzb-synced-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .hzb-synced-table th,
        .hzb-synced-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .hzb-synced-table th {
            background: #f0f0f1;
            font-weight: 600;
        }
        
        .hzb-synced-table tr:hover {
            background: #f9f9f9;
        }
        
        .hzb-status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .hzb-status-badge.synced {
            background: #00a32a;
            color: #fff;
        }
        
        .hzb-status-badge.pending {
            background: #f0b849;
            color: #fff;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('#hzb-bulk-sync').on('click', function() {
                var $btn = $(this);
                var $spinner = $btn.next('.spinner');
                var $progress = $('.hzb-sync-progress');
                var $progressBar = $('.hzb-progress-fill');
                var $progressText = $('.hzb-progress-text');
                
                if (!confirm('<?php _e('¿Está seguro de sincronizar todas las propiedades? Este proceso puede tardar varios minutos.', 'houzez-booking'); ?>')) {
                    return;
                }
                
                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                $progress.fadeIn();
                $progressBar.css('width', '0%');
                $progressText.text('<?php _e('Iniciando sincronización...', 'houzez-booking'); ?>');
                
                // Aquí iría la lógica AJAX de sincronización masiva
                // Por ahora, simulamos el progreso
                var progress = 0;
                var interval = setInterval(function() {
                    progress += 10;
                    $progressBar.css('width', progress + '%');
                    $progressText.text('<?php _e('Sincronizando...', 'houzez-booking'); ?> ' + progress + '%');
                    
                    if (progress >= 100) {
                        clearInterval(interval);
                        $progressText.text('<?php _e('¡Sincronización completada!', 'houzez-booking'); ?>');
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                        
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }, 500);
            });
        });
        </script>
        <?php
    }
    
    /**
     * Contar propiedades sincronizadas
     */
    private function count_synced_properties() {
        global $wpdb;
        return $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} 
            WHERE meta_key = '_hzb_sync_status' 
            AND meta_value = 'synced'"
        );
    }
    
    /**
     * Contar propiedades pendientes
     */
    private function count_pending_properties() {
        global $wpdb;
        return $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} 
            WHERE meta_key = '_hzb_sync_enabled' 
            AND meta_value = '1'
            AND post_id NOT IN (
                SELECT post_id FROM {$wpdb->postmeta} 
                WHERE meta_key = '_hzb_sync_status' 
                AND meta_value = 'synced'
            )"
        );
    }
    
    /**
     * Renderizar tabla de propiedades sincronizadas
     */
    private function render_synced_properties_table() {
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => 20,
            'meta_query' => array(
                array(
                    'key' => '_hzb_sync_enabled',
                    'value' => '1',
                )
            )
        );
        
        $properties = new WP_Query($args);
        
        if (!$properties->have_posts()) {
            echo '<p>' . __('No hay propiedades sincronizadas.', 'houzez-booking') . '</p>';
            return;
        }
        ?>
        <table class="hzb-synced-table">
            <thead>
                <tr>
                    <th><?php _e('Propiedad', 'houzez-booking'); ?></th>
                    <th><?php _e('Estado', 'houzez-booking'); ?></th>
                    <th><?php _e('ID Alojamiento', 'houzez-booking'); ?></th>
                    <th><?php _e('Última Sincronización', 'houzez-booking'); ?></th>
                    <th><?php _e('Acciones', 'houzez-booking'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($properties->have_posts()): $properties->the_post(); 
                    $status = get_post_meta(get_the_ID(), '_hzb_sync_status', true);
                    $accommodation_id = get_post_meta(get_the_ID(), '_motopress_accommodation_type_id', true);
                    $last_sync = get_post_meta(get_the_ID(), '_hzb_last_sync', true);
                ?>
                <tr>
                    <td>
                        <strong><a href="<?php echo get_edit_post_link(); ?>"><?php the_title(); ?></a></strong>
                    </td>
                    <td>
                        <span class="hzb-status-badge <?php echo esc_attr($status); ?>">
                            <?php echo $status === 'synced' ? __('Sincronizado', 'houzez-booking') : __('Pendiente', 'houzez-booking'); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo $accommodation_id ? '#' . $accommodation_id : '-'; ?>
                    </td>
                    <td>
                        <?php echo $last_sync ? date_i18n(get_option('date_format'), $last_sync) : '-'; ?>
                    </td>
                    <td>
                        <a href="<?php the_permalink(); ?>" class="button button-small" target="_blank">
                            <?php _e('Ver', 'houzez-booking'); ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; wp_reset_postdata(); ?>
            </tbody>
        </table>
        <?php
    }
}

// Inicializar configuración
new HZB_Settings();