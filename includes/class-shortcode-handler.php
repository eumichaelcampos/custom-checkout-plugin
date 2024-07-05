<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Shortcode_Handler {

    private static $instance = null;

    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode( 'custom_checkout_additional_info', array( $this, 'render_custom_checkout_additional_info' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_shortcode_styles' ) );
    }

    public function enqueue_shortcode_styles() {
        wp_enqueue_style( 'custom-checkout', CUSTOM_CHECKOUT_PLUGIN_URL . 'assets/css/custom-checkout.css' );
    }

    public function render_custom_checkout_additional_info( $atts ) {
        ob_start();
        echo '<form id="custom-checkout-additional-info-form" class="custom-checkout-form" enctype="multipart/form-data">';
        echo '<p><label for="billing_company">' . __( 'Empresa', 'custom-checkout' ) . '</label><input type="text" id="billing_company" name="billing_company" /></p>';
        echo '<p><label for="billing_address_1">' . __( 'Endereço 1', 'custom-checkout' ) . '</label><input type="text" id="billing_address_1" name="billing_address_1" /></p>';
        echo '<p><label for="billing_address_2">' . __( 'Endereço 2', 'custom-checkout' ) . '</label><input type="text" id="billing_address_2" name="billing_address_2" /></p>';
        echo '<p><label for="billing_city">' . __( 'Cidade', 'custom-checkout' ) . '</label><input type="text" id="billing_city" name="billing_city" /></p>';
        echo '<p><label for="billing_postcode">' . __( 'CEP', 'custom-checkout' ) . '</label><input type="text" id="billing_postcode" name="billing_postcode" /></p>';
        echo '<p><label for="billing_country">' . __( 'País', 'custom-checkout' ) . '</label><input type="text" id="billing_country" name="billing_country" /></p>';
        echo '<p><label for="billing_state">' . __( 'Estado', 'custom-checkout' ) . '</label><input type="text" id="billing_state" name="billing_state" /></p>';
        echo '<p><label for="identificacao">' . __( 'Envie seu comprovante de CPF (ID, CNH ou... )', 'custom-checkout' ) . '</label><input type="file" id="identificacao" name="identificacao" /></p>';
        echo '<p><label for="escolaridade">' . __( 'Comprovante de Escolaridade', 'custom-checkout' ) . '</label><input type="file" id="escolaridade" name="escolaridade" /></p>';
        echo '<p><label for="exp_profissional">' . __( 'Envie Comprovante de Experiência Profissional', 'custom-checkout' ) . '</label><input type="file" id="exp_profissional" name="exp_profissional" /></p>';
        echo '<p><label for="residencia">' . __( 'Comprovante de Residência', 'custom-checkout' ) . '</label><input type="file" id="residencia" name="residencia" /></p>';
        echo '<p><label for="certidao">' . __( 'Certidão de Nascimento ou Casamento', 'custom-checkout' ) . '</label><input type="file" id="certidao" name="certidao" /></p>';
        echo '<p><label for="eleitor">' . __( 'Título de Eleitor', 'custom-checkout' ) . '</label><input type="file" id="eleitor" name="eleitor" /></p>';
        echo '<p><input type="submit" value="' . __( 'Enviar', 'custom-checkout' ) . '"></p>';
        echo '</form>';

        return ob_get_clean();
    }
}
