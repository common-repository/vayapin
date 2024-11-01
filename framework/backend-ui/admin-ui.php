<?php

/**
 * Register the admin menu item and the settings page.
 */
function vayapin_woocommerce_add_admin_menu()
{
    add_menu_page(
        'Vayapin Settings', // Page title.
        'Vayapin', // Menu title.
        'manage_options', // Capability.
        'vayapin-woocommerce', // Menu slug.
        'vayapin_woocommerce_settings_page', // Function to render the settings page.
        'dashicons-location', // Icon URL.
        1000 // Position.
    );
}
add_action('admin_menu', 'vayapin_woocommerce_add_admin_menu');

/**
 * Callback for rendering the settings page.
 */
function vayapin_woocommerce_settings_page()
{
    $options = get_option('vayapin_woocommerce_options');
?>
    <div class="wrap">
        <h1>Vayapin WooCommerce Settings</h1>
        <form action="options.php" method="post">
            <?php
            settings_errors('vayapin_woocommerce_options');
            settings_fields('vayapin_woocommerce');
            do_settings_sections('vayapin-woocommerce');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
<?php
}

/**
 * Register the settings, sections, and fields.
 */
function vayapin_woocommerce_settings_init()
{
    register_setting('vayapin_woocommerce', 'vayapin_woocommerce_options', 'vayapin_woocommerce_options_validate');

    add_settings_section(
        'vayapin_woocommerce_section',
        __('VayaPin WordPress plugin settings and options', 'vayapin-woocommerce'),
        'vayapin_woocommerce_section_callback',
        'vayapin-woocommerce'
    );

    add_settings_field(
        'vayapin_woocommerce_api_key',
        __('API Key', 'vayapin-woocommerce'),
        'vayapin_woocommerce_api_key_render',
        'vayapin-woocommerce',
        'vayapin_woocommerce_section'
    );

    add_settings_field(
        'vayapin_woocommerce_button_bg_color',
        __('Button background color', 'vayapin-woocommerce'),
        'vayapin_woocommerce_button_bg_color_render',
        'vayapin-woocommerce',
        'vayapin_woocommerce_section'
    );

    add_settings_field(
        'vayapin_woocommerce_button_text_color',
        __('Button text color', 'vayapin-woocommerce'),
        'vayapin_woocommerce_button_text_color_render',
        'vayapin-woocommerce',
        'vayapin_woocommerce_section'
    );

    add_settings_field(
        'vayapin_woocommerce_button_border_radius',
        __('Button border radius', 'vayapin-woocommerce'),
        'vayapin_woocommerce_button_border_radius_render',
        'vayapin-woocommerce',
        'vayapin_woocommerce_section'
    );

    add_settings_field(
        'vayapin_woocommerce_label_generation',
        __('Label generation', 'vayapin-woocommerce'),
        'vayapin_woocommerce_label_generation_render',
        'vayapin-woocommerce',
        'vayapin_woocommerce_section'
    );
}
add_action('admin_init', 'vayapin_woocommerce_settings_init');

/**
 * Section callback functions.
 */
function vayapin_woocommerce_section_callback()
{
    echo '<p>' . esc_html__('Enter your settings below', 'vayapin-woocommerce') . '</p>';
}

/**
 * Render functions for the fields.
 */
function vayapin_woocommerce_api_key_render()
{
    $options = get_option('vayapin_woocommerce_options');
?>
    <input type='text' class="vayapin-input" name='vayapin_woocommerce_options[api_key]' value='<?php echo esc_attr($options['api_key'] ?? ''); ?>'>
    <?php
    if (isset($options['api_connected']) && $options['api_connected'] === true) {
    ?>
        <span class="dashicons dashicons-yes-alt vayapin-api-icon vayapin-api-icon-green"></span>
    <?php
    } else if (isset($options['api_connected']) && $options['api_connected'] === false) {
    ?>
        <span class="dashicons dashicons-dismiss vayapin-api-icon vayapin-api-icon-red"></span>
    <?php
    }
}

function vayapin_woocommerce_button_bg_color_render()
{
    $options = get_option('vayapin_woocommerce_options');
    ?>
    <input type='text' class="vayapin-input" name='vayapin_woocommerce_options[button_bg_color]' value='<?php echo esc_attr($options['button_bg_color'] ?? ''); ?>'>
<?php
}

function vayapin_woocommerce_button_text_color_render()
{
    $options = get_option('vayapin_woocommerce_options');
?>
    <input type='text' class="vayapin-input" name='vayapin_woocommerce_options[button_text_color]' value='<?php echo esc_attr($options['button_text_color'] ?? ''); ?>'>
<?php
}

function vayapin_woocommerce_button_border_radius_render()
{
    $options = get_option('vayapin_woocommerce_options');
?>
    <div class="vayapin-relative">
        <input type='text' class="vayapin-input vayapin-input" name='vayapin_woocommerce_options[button_border_radius]' value='<?php echo esc_attr($options['button_border_radius'] ?? ''); ?>'>
        <span class="vayapin-input-addon">px</span>
    </div>
<?php
}

function vayapin_woocommerce_label_generation_render()
{
    $options = get_option('vayapin_woocommerce_options');
    $isChecked = isset($options['label_generation']) && $options['label_generation'] === '1';
?>
    <input type='checkbox' class="vayapin-input" name='vayapin_woocommerce_options[label_generation]' <?php echo $isChecked ? 'checked' : ''; ?>>
    <span>Option for activating label generation in the WooCommerce order actions.</span>
<?php
}

/**
 * Validate and sanitize the input.
 */
function vayapin_woocommerce_options_validate($input)
{
    $new_input = [];

    if (!empty($input['api_key'])) {

        $new_input['api_key'] = sanitize_text_field($input['api_key']);

        $api_url = 'https://cs.vayapin.com/api/v0/pin/DK:NOHO';
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $new_input['api_key'],
                'Content-Type' => 'application/json',
            ]
        ];
        $response = wp_remote_get($api_url, $args);
        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code !== 200) {
            add_settings_error('vayapin_woocommerce_options', 'api_key_error', 'The API key is not valid. Please try again.', 'error');
            $new_input['api_connected'] = false;
        } else if ($response_code === 200) {
            add_settings_error('vayapin_woocommerce_options', 'api_key_success', 'The API key is valid.', 'success');
            $new_input['api_connected'] = true;
        }
    }

    if (isset($input['button_bg_color'])) {
        $new_input['button_bg_color'] = sanitize_text_field($input['button_bg_color']);
    }

    if (isset($input['button_text_color'])) {
        $new_input['button_text_color'] = sanitize_text_field($input['button_text_color']);
    }

    if (isset($input['button_border_radius'])) {
        $new_input['button_border_radius'] = sanitize_text_field($input['button_border_radius']);
    }

    if (isset($input['label_generation'])) {
        $new_input['label_generation'] = $input['label_generation'] === 'on' ? '1' : '0';
    }

    return $new_input;
}
