<?php
/**
 * Admin settings page for Houzez Booking plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu
 */
function hzb_add_admin_menu() {
    add_menu_page(
        __('Houzez Booking', 'houzez-booking'),
        __('Houzez Booking', 'houzez-booking'),
        'manage_options',
        'houzez-booking',
        'hzb_settings_page_html',
        'dashicons-calendar-alt',
        30
    );
}
add_action('admin_menu', 'hzb_add_admin_menu');

/**
 * Register settings
 */
function hzb_settings_init() {
    register_setting('hzb_settings', 'hzb_sync_enabled');
    register_setting('hzb_settings', 'hzb_auto_sync');
    register_setting('hzb_settings', 'hzb_show_booking_widget');
    register_setting('hzb_settings', 'hzb_currency_symbol');

    add_settings_section(
        'hzb_general_section',
        __('Configuración General', 'houzez-booking'),
        'hzb_general_section_callback',
        'hzb_settings'
    );

    add_settings_field(
        'hzb_sync_enabled',
        __('Habilitar Sincronización', 'houzez-booking'),
        'hzb_sync_enabled_callback',
        'hzb_settings',
        'hzb_general_section'
    );

    add_settings_field(
        'hzb_auto_sync',
        __('Sincronización Automática', 'houzez-booking'),
        'hzb_auto_sync_callback',
        'hzb_settings',
        'hzb_general_section'
    );

    add_settings_field(
        'hzb_show_booking_widget',
        __('Mostrar Widget de Reservas', 'houzez-booking'),
        'hzb_show_booking_widget_callback',
        'hzb_settings',
        'hzb_general_section'
    );

    add_settings_field(
        'hzb_currency_symbol',
        __('Símbolo de Moneda', 'houzez-booking'),
        'hzb_currency_symbol_callback',
        'hzb_settings',
        'hzb_general_section'
    );
}
add_action('admin_init', 'hzb_settings_init');

/**
 * Section callback
 */
function hzb_general_section_callback() {
    echo '<p>' . __('Configura las opciones generales del plugin Houzez Booking.', 'houzez-booking') . '</p>';
}

/**
 * Field callbacks
 */
function hzb_sync_enabled_callback() {
    $value = get_option('hzb_sync_enabled', true);
    ?>
    <input type="checkbox" name="hzb_sync_enabled" value="1" <?php checked(1, $value, true); ?> />
    <label for="hzb_sync_enabled"><?php _e('Habilitar sincronización entre Houzez y MotoPress', 'houzez-booking'); ?></label>
    <?php
}

function hzb_auto_sync_callback() {
    $value = get_option('hzb_auto_sync', true);
    ?>
    <input type="checkbox" name="hzb_auto_sync" value="1" <?php checked(1, $value, true); ?> />
    <label for="hzb_auto_sync"><?php _e('Sincronizar automáticamente al guardar propiedades', 'houzez-booking'); ?></label>
    <?php
}

function hzb_show_booking_widget_callback() {
    $value = get_option('hzb_show_booking_widget', true);
    ?>
    <input type="checkbox" name="hzb_show_booking_widget" value="1" <?php checked(1, $value, true); ?> />
    <label for="hzb_show_booking_widget"><?php _e('Mostrar widget de disponibilidad en páginas de propiedades', 'houzez-booking'); ?></label>
    <?php
}

function hzb_currency_symbol_callback() {
    $value = get_option('hzb_currency_symbol', '$');
    ?>
    <input type="text" name="hzb_currency_symbol" value="<?php echo esc_attr($value); ?>" size="5" />
    <p class="description"><?php _e('Símbolo de moneda para mostrar en precios (ej: $, €, £)', 'houzez-booking'); ?></p>
    <?php
}

/**
 * Settings page HTML
 */
function hzb_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['settings-updated'])) {
        add_settings_error('hzb_messages', 'hzb_message', __('Configuración guardada', 'houzez-booking'), 'updated');
    }

    settings_errors('hzb_messages');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('hzb_settings');
            do_settings_sections('hzb_settings');
            submit_button(__('Guardar Cambios', 'houzez-booking'));
            ?>
        </form>
    </div>
    <?php
}