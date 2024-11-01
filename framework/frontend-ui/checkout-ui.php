<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vayapin_woocommerce_add_checkout_button()
{
    $options = get_option('vayapin_woocommerce_options');
    $styleString = '';

    if (!empty($options['button_bg_color'])) {
        $styleString .= 'background-color: ' . esc_attr($options['button_bg_color']) . ';';
    }
    if (!empty($options['button_text_color'])) {
        $styleString .= 'color: ' . esc_attr($options['button_text_color']) . ';';
    }
    if (!empty($options['button_border_radius'])) {
        $styleString .= 'border-radius: ' . esc_attr($options['button_border_radius']) . 'px;';
    }

    if (!empty($options['api_key'])) {
        $imgUrl = esc_url(plugin_dir_url(__FILE__) . '../../public/img/vayapin-icon.svg');
        $logoUrl = esc_url(plugin_dir_url(__FILE__) . '../../public/img/logo_green_black.svg');
?>
        <div class="vayapin-overlay" style="display: none;"></div>
        <div class="vayapin-container">
            <div class="vayapin-checkout-container">
                <div class="text-center">
                    <button type="button" class="vayapin-checkout-button" style="<?php echo esc_attr($styleString) ?>">
                        <img src="<?php echo esc_url($imgUrl) ?>" alt="Checkout with VayaPin" class="vayapin-button-icon"> Checkout with VayaPin
                    </button>
                    <a href="https://id.vayapin.com/sign-up" target="_blank" class="vayapin-learn-more">Get a PRECISE address with VayaPin</a>
                </div>
            </div>
            <div class="vayapin-checkout-dropdown" style="display: none;">
                <div class="vayapin-checkout-dropdown-content">
                </div>
                <div class="vayapin-checkout-dropdown-body">
                    <span class="vayapin-checkout-dropdown-close">&times;</span>
                    <p class="vayapin-description">Ensure precise delivery the first time, EVERY time!</p>
                    <form class="vayapin-form" method="post">
                        <small>Enter VayaPin ID without XX:</small>
                        <div class="flex">
                            <input id="country_selector" name="vayapin_country" class="vayapin-country" type="text">
                            <input id="vayapin_input" name="vayapin" class="vayapin-input vayapin-input-name" type="text" placeholder="VayaPin ID">
                        </div>
                        <p class="vayapin-error-text mt-5" style="display: none;">Not found. Please enter a Valid VayaPin.</p>
                        <button type="button" class="vayapin-checkout-button-inside mt-20" id="vayapin-fields-button" style="<?php echo esc_attr($styleString) ?>">
                            <img src="<?php echo esc_url($imgUrl) ?>" alt="Fill out fields with VayaPin" class="vayapin-button-icon"> Fill out fields
                        </button>
                        <div class="text-center mt-10">
                            <img src="<?php echo esc_url($logoUrl) ?>" alt="VayaPin" class="vayapin-logo-checkout">
                            <p>The most precise address in the world</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php
    }
}

add_action('woocommerce_checkout_before_customer_details', 'vayapin_woocommerce_add_checkout_button', 10, 0);
