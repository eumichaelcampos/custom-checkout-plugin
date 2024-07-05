<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Custom_Checkout {

    private static $instance = null;

    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'woocommerce_checkout_fields', array( $this, 'customize_checkout_fields' ) );
        add_action( 'woocommerce_thankyou', array( $this, 'additional_info_form' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_nopriv_custom_checkout_file_upload', array( $this, 'handle_file_upload' ) );
        add_action( 'wp_ajax_custom_checkout_file_upload', array( $this, 'handle_file_upload' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_thankyou_styles' ) );
        add_action( 'show_user_profile', array( $this, 'custom_user_profile_fields' ) );
        add_action( 'edit_user_profile', array( $this, 'custom_user_profile_fields' ) );
        add_action( 'personal_options_update', array( $this, 'save_custom_user_profile_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_custom_user_profile_fields' ) );
        add_action( 'woocommerce_edit_account_form', array( $this, 'custom_edit_account_form' ) );
        add_action( 'woocommerce_save_account_details', array( $this, 'custom_save_account_details' ) );
        add_action( 'woocommerce_before_checkout_form', array( $this, 'display_coupon_form' ) ); // Adiciona a exibição do campo de cupom no checkout
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'custom-checkout', CUSTOM_CHECKOUT_PLUGIN_URL . 'assets/js/custom-checkout.js', array( 'jquery' ), '1.0', true );
        wp_localize_script( 'custom-checkout', 'customCheckout', array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        ));
    }

    public function enqueue_thankyou_styles() {
        if ( is_order_received_page() ) {
            wp_enqueue_style( 'custom-checkout', CUSTOM_CHECKOUT_PLUGIN_URL . 'assets/css/custom-checkout.css' );
        }
    }

    public function customize_checkout_fields( $fields ) {
        // Removendo todos os campos não necessários
        unset($fields['billing']['billing_company']);
        unset($fields['billing']['billing_address_1']);
        unset($fields['billing']['billing_address_2']);
        unset($fields['billing']['billing_city']);
        unset($fields['billing']['billing_postcode']);
        unset($fields['billing']['billing_country']);
        unset($fields['billing']['billing_state']);
        unset($fields['billing']['billing_last_name']);
        unset($fields['billing']['billing_phone']);

        // Adicionando os campos necessários
        $fields['billing']['billing_first_name'] = array(
            'label'       => __( 'Nome Completo', 'custom-checkout' ),
            'required'    => true,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 10,
        );

        $fields['billing']['billing_email'] = array(
            'label'       => __( 'Email', 'custom-checkout' ),
            'required'    => true,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 20,
        );

        $fields['billing']['billing_whatsapp'] = array(
            'label'       => __( 'WhatsApp', 'custom-checkout' ),
            'required'    => true,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 30,
        );

        $fields['billing']['billing_cpf'] = array(
            'label'       => __( 'CPF', 'custom-checkout' ),
            'required'    => true,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 40,
        );

        $fields['billing']['billing_birthdate'] = array(
            'label'       => __( 'Data de Nascimento', 'custom-checkout' ),
            'required'    => true,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 50,
            'type'        => 'date',
        );

        $fields['account']['account_password'] = array(
            'label'       => __( 'Senha', 'custom-checkout' ),
            'required'    => true,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 60,
            'type'        => 'password',
        );

        $fields['account']['account_confirm_password'] = array(
            'label'       => __( 'Confirme sua Senha', 'custom-checkout' ),
            'required'    => true,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 70,
            'type'        => 'password',
        );

        return $fields;
    }

    public function additional_info_form( $order_id ) {
        $user_id = get_current_user_id();
        $user_meta = get_user_meta($user_id);

        echo '<h2>' . __( 'Informações Adicionais', 'custom-checkout' ) . '</h2>';
        echo '<form id="additional-info-form" class="custom-checkout-form" enctype="multipart/form-data">';
        wp_nonce_field('custom_checkout_nonce', 'custom_checkout_nonce_field');

        echo '<h3>' . __( 'Informações Pessoais', 'custom-checkout' ) . '</h3>';
        echo '<p><label for="billing_address">' . __( 'Endereço', 'custom-checkout' ) . '</label><input type="text" id="billing_address" name="billing_address" value="' . esc_attr(isset($user_meta['billing_address'][0]) ? $user_meta['billing_address'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_city">' . __( 'Cidade', 'custom-checkout' ) . '</label><input type="text" id="billing_city" name="billing_city" value="' . esc_attr(isset($user_meta['billing_city'][0]) ? $user_meta['billing_city'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_postcode">' . __( 'CEP', 'custom-checkout' ) . '</label><input type="text" id="billing_postcode" name="billing_postcode" value="' . esc_attr(isset($user_meta['billing_postcode'][0]) ? $user_meta['billing_postcode'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_country">' . __( 'País', 'custom-checkout' ) . '</label><input type="text" id="billing_country" name="billing_country" value="' . esc_attr(isset($user_meta['billing_country'][0]) ? $user_meta['billing_country'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_state">' . __( 'Estado', 'custom-checkout' ) . '</label><input type="text" id="billing_state" name="billing_state" value="' . esc_attr(isset($user_meta['billing_state'][0]) ? $user_meta['billing_state'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_whatsapp">' . __( 'WhatsApp', 'custom-checkout' ) . '</label><input type="text" id="billing_whatsapp" name="billing_whatsapp" value="' . esc_attr(isset($user_meta['billing_whatsapp'][0]) ? $user_meta['billing_whatsapp'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_celular">' . __( 'Celular/Telefone', 'custom-checkout' ) . '</label><input type="text" id="billing_celular" name="billing_celular" value="' . esc_attr(isset($user_meta['billing_celular'][0]) ? $user_meta['billing_celular'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_cpf">' . __( 'CPF', 'custom-checkout' ) . '</label><input type="text" id="billing_cpf" name="billing_cpf" value="' . esc_attr(isset($user_meta['billing_cpf'][0]) ? $user_meta['billing_cpf'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_rg">' . __( 'RG', 'custom-checkout' ) . '</label><input type="text" id="billing_rg" name="billing_rg" value="' . esc_attr(isset($user_meta['billing_rg'][0]) ? $user_meta['billing_rg'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_uf_rg">' . __( 'UF RG', 'custom-checkout' ) . '</label>
        <select id="billing_uf_rg" name="billing_uf_rg" required>
            <option value="">' . __( 'Selecione', 'custom-checkout' ) . '</option>
            <option value="AC" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'AC', false ) . '>AC</option>
            <option value="AL" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'AL', false ) . '>AL</option>
            <option value="AP" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'AP', false ) . '>AP</option>
            <option value="AM" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'AM', false ) . '>AM</option>
            <option value="BA" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'BA', false ) . '>BA</option>
            <option value="CE" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'CE', false ) . '>CE</option>
            <option value="DF" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'DF', false ) . '>DF</option>
            <option value="ES" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'ES', false ) . '>ES</option>
            <option value="GO" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'GO', false ) . '>GO</option>
            <option value="MA" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'MA', false ) . '>MA</option>
            <option value="MT" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'MT', false ) . '>MT</option>
            <option value="MS" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'MS', false ) . '>MS</option>
            <option value="MG" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'MG', false ) . '>MG</option>
            <option value="PA" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'PA', false ) . '>PA</option>
            <option value="PB" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'PB', false ) . '>PB</option>
            <option value="PR" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'PR', false ) . '>PR</option>
            <option value="PE" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'PE', false ) . '>PE</option>
            <option value="PI" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'PI', false ) . '>PI</option>
            <option value="RJ" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'RJ', false ) . '>RJ</option>
            <option value="RN" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'RN', false ) . '>RN</option>
            <option value="RS" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'RS', false ) . '>RS</option>
            <option value="RO" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'RO', false ) . '>RO</option>
            <option value="RR" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'RR', false ) . '>RR</option>
            <option value="SC" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'SC', false ) . '>SC</option>
            <option value="SP" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'SP', false ) . '>SP</option>
            <option value="SE" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'SE', false ) . '>SE</option>
            <option value="TO" ' . selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'TO', false ) . '>TO</option>
        </select></p>';
        echo '<p><label for="billing_orgao_expedidor">' . __( 'Órgão Expedidor', 'custom-checkout' ) . '</label>
        <select id="billing_orgao_expedidor" name="billing_orgao_expedidor" required>
            <option value="">' . __( 'Selecione', 'custom-checkout' ) . '</option>
            <option value="SSP" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'SSP', false ) . '>SSP</option>
            <option value="DETRAN" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'DETRAN', false ) . '>DETRAN</option>
            <option value="PF" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'PF', false ) . '>PF</option>
            <option value="POM" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'POM', false ) . '>POM</option>
            <option value="SEJUSP" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'SEJUSP', false ) . '>SEJUSP</option>
            <option value="SDS" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'SDS', false ) . '>SDS</option>
            <option value="CGP" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'CGP', false ) . '>CGP</option>
            <option value="RG" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'RG', false ) . '>RG</option>
            <option value="IPF" ' . selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'IPF', false ) . '>IPF</option>
        </select></p>';
        echo '<p><label for="billing_nome_mae">' . __( 'Nome da Mãe', 'custom-checkout' ) . '</label><input type="text" id="billing_nome_mae" name="billing_nome_mae" value="' . esc_attr(isset($user_meta['billing_nome_mae'][0]) ? $user_meta['billing_nome_mae'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_naturalidade">' . __( 'Naturalidade', 'custom-checkout' ) . '</label><input type="text" id="billing_naturalidade" name="billing_naturalidade" value="' . esc_attr(isset($user_meta['billing_naturalidade'][0]) ? $user_meta['billing_naturalidade'][0] : '') . '" required /></p>';
        echo '<p><label for="billing_estado_civil">' . __( 'Estado Civil', 'custom-checkout' ) . '</label>
        <select id="billing_estado_civil" name="billing_estado_civil" required>
            <option value="">' . __( 'Selecione', 'custom-checkout' ) . '</option>
            <option value="solteiro" ' . selected( isset($user_meta['billing_estado_civil'][0]) ? $user_meta['billing_estado_civil'][0] : '', 'solteiro', false ) . '>Solteiro(a)</option>
            <option value="casado" ' . selected( isset($user_meta['billing_estado_civil'][0]) ? $user_meta['billing_estado_civil'][0] : '', 'casado', false ) . '>Casado(a)</option>
            <option value="separado" ' . selected( isset($user_meta['billing_estado_civil'][0]) ? $user_meta['billing_estado_civil'][0] : '', 'separado', false ) . '>Separado(a)</option>
            <option value="divorciado" ' . selected( isset($user_meta['billing_estado_civil'][0]) ? $user_meta['billing_estado_civil'][0] : '', 'divorciado', false ) . '>Divorciado(a)</option>
            <option value="viuvo" ' . selected( isset($user_meta['billing_estado_civil'][0]) ? $user_meta['billing_estado_civil'][0] : '', 'viuvo', false ) . '>Viúvo(a)</option>
        </select></p>';

        echo '<h3>' . __( 'Documentos', 'custom-checkout' ) . '</h3>';
        $this->render_file_upload_field('identificacao', 'Envie seu comprovante de CPF (ID, CNH ou...)', $user_meta);
        $this->render_file_upload_field('escolaridade', 'Comprovante de Escolaridade', $user_meta);
        $this->render_file_upload_field('exp_profissional', 'Envie Comprovante de Experiência Profissional', $user_meta);
        $this->render_file_upload_field('residencia', 'Comprovante de Residência', $user_meta);
        $this->render_file_upload_field('certidao', 'Certidão de Nascimento ou Casamento', $user_meta);
        $this->render_file_upload_field('eleitor', 'Título de Eleitor', $user_meta);

        echo '<p><input type="submit" value="' . __( 'Enviar', 'custom-checkout' ) . '"></p>';
        echo '</form>';
    }

    private function render_file_upload_field($field_name, $label, $user_meta) {
        $file_url = isset($user_meta[$field_name][0]) ? $user_meta[$field_name][0] : '';
        echo '<p><label for="' . $field_name . '">' . __($label, 'custom-checkout') . '</label>';
        if ($file_url) {
            echo '<a href="' . esc_url($file_url) . '" target="_blank" class="botao-baixar">' . __('Baixar arquivo atual', 'custom-checkout') . '</a><br />';
        }
        echo '<input type="file" id="' . $field_name . '" name="' . $field_name . '" /></p>';
    }

    public function handle_file_upload() {
        check_ajax_referer( 'custom_checkout_nonce', 'security' );

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $user_id = get_current_user_id();
        $uploaded_files = $_FILES;
        $upload_overrides = array( 'test_form' => false );
        $attachments = array();

        // Processar arquivos enviados
        foreach ($uploaded_files as $file_field_name => $file) {
            if ($file['size'] > 0) {
                $movefile = wp_handle_upload($file, $upload_overrides);
                if ($movefile && !isset($movefile['error'])) {
                    update_user_meta($user_id, $file_field_name, $movefile['url']);
                    $attachments[] = $movefile['file']; // Adiciona o caminho do arquivo para os anexos do email
                }
            }
        }

        // Processar campos de texto
        $fields_to_update = [
            'billing_address', 'billing_city', 'billing_postcode', 'billing_country', 'billing_state',
            'billing_whatsapp', 'billing_celular', 'billing_cpf', 'billing_rg', 'billing_uf_rg', 
            'billing_orgao_expedidor', 'billing_nome_mae', 'billing_naturalidade', 'billing_estado_civil'
        ];

        foreach ($fields_to_update as $field) {
            if (isset($_POST[$field])) {
                update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Enviar email para o administrador
        $this->send_admin_email($user_id, $attachments);

        wp_send_json_success();
    }

    private function send_admin_email($user_id, $attachments) {
        $user_info = get_userdata($user_id);
        $email_content = "Informações do Cliente:\n\n";
        $email_content .= "Nome Completo: " . $user_info->first_name . " " . $user_info->last_name . "\n";
        $email_content .= "Email: " . $user_info->user_email . "\n";
        $email_content .= "WhatsApp: " . get_user_meta($user_id, 'billing_whatsapp', true) . "\n";
        $email_content .= "CPF: " . get_user_meta($user_id, 'billing_cpf', true) . "\n";
        $email_content .= "Data de Nascimento: " . get_user_meta($user_id, 'billing_birthdate', true) . "\n";
        $email_content .= "Endereço: " . get_user_meta($user_id, 'billing_address', true) . "\n";
        $email_content .= "Cidade: " . get_user_meta($user_id, 'billing_city', true) . "\n";
        $email_content .= "CEP: " . get_user_meta($user_id, 'billing_postcode', true) . "\n";
        $email_content .= "País: " . get_user_meta($user_id, 'billing_country', true) . "\n";
        $email_content .= "Estado: " . get_user_meta($user_id, 'billing_state', true) . "\n";
        $email_content .= "Celular/Telefone: " . get_user_meta($user_id, 'billing_celular', true) . "\n";
        $email_content .= "RG: " . get_user_meta($user_id, 'billing_rg', true) . "\n";
        $email_content .= "UF RG: " . get_user_meta($user_id, 'billing_uf_rg', true) . "\n";
        $email_content .= "Órgão Expedidor: " . get_user_meta($user_id, 'billing_orgao_expedidor', true) . "\n";
        $email_content .= "Nome da Mãe: " . get_user_meta($user_id, 'billing_nome_mae', true) . "\n";
        $email_content .= "Naturalidade: " . get_user_meta($user_id, 'billing_naturalidade', true) . "\n";
        $email_content .= "Estado Civil: " . get_user_meta($user_id, 'billing_estado_civil', true) . "\n";

        // Enviar email usando o template padrão do WooCommerce
        $mailer = WC()->mailer();
        $email_heading = 'Informações adicionais do cliente';
        $email_body = $mailer->wrap_message($email_heading, nl2br($email_content));
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $admin_email = get_option('admin_email');
        $mailer->send($admin_email, $email_heading, $email_body, $headers, $attachments);
    }

    public function custom_user_profile_fields($user) {
        ?>
        <h3><?php _e('Informações Adicionais', 'custom-checkout'); ?></h3>
        <table class="form-table">
            <?php
            $this->render_user_profile_file_field($user, 'identificacao', 'Envie seu comprovante de CPF (ID, CNH ou...)');
            $this->render_user_profile_file_field($user, 'escolaridade', 'Comprovante de Escolaridade');
            $this->render_user_profile_file_field($user, 'exp_profissional', 'Envie Comprovante de Experiência Profissional');
            $this->render_user_profile_file_field($user, 'residencia', 'Comprovante de Residência');
            $this->render_user_profile_file_field($user, 'certidao', 'Certidão de Nascimento ou Casamento');
            $this->render_user_profile_file_field($user, 'eleitor', 'Título de Eleitor');
            ?>
        </table>
        <?php
    }

    private function render_user_profile_file_field($user, $field_name, $label) {
        $file_url = get_the_author_meta($field_name, $user->ID);
        ?>
        <tr>
            <th><label for="<?php echo $field_name; ?>"><?php _e($label, 'custom-checkout'); ?></label></th>
            <td>
                <?php if ($file_url) : ?>
                    <a href="<?php echo esc_url($file_url); ?>" target="_blank"><?php _e('Baixar arquivo atual', 'custom-checkout'); ?></a><br />
                <?php endif; ?>
                <input type="file" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" /><br />
                <span class="description"><?php _e('Envie um novo arquivo para substituir o atual.', 'custom-checkout'); ?></span>
            </td>
        </tr>
        <?php
    }

    public function save_custom_user_profile_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        foreach (['identificacao', 'escolaridade', 'exp_profissional', 'residencia', 'certidao', 'eleitor'] as $field_name) {
            if (isset($_FILES[$field_name]) && !empty($_FILES[$field_name]['name'])) {
                $uploadedfile = $_FILES[$field_name];
                $upload_overrides = array('test_form' => false);

                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile && !isset($movefile['error'])) {
                    update_user_meta($user_id, $field_name, $movefile['url']);
                }
            }
        }
    }

    public function custom_edit_account_form() {
        $user_id = get_current_user_id();
        $user_meta = get_user_meta($user_id);
        ?>

        <h3><?php _e('Informações Adicionais', 'custom-checkout'); ?></h3>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_address"><?php _e('Endereço', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_address" id="billing_address" value="<?php echo esc_attr(isset($user_meta['billing_address'][0]) ? $user_meta['billing_address'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_city"><?php _e('Cidade', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_city" id="billing_city" value="<?php echo esc_attr(isset($user_meta['billing_city'][0]) ? $user_meta['billing_city'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_postcode"><?php _e('CEP', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_postcode" id="billing_postcode" value="<?php echo esc_attr(isset($user_meta['billing_postcode'][0]) ? $user_meta['billing_postcode'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_country"><?php _e('País', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_country" id="billing_country" value="<?php echo esc_attr(isset($user_meta['billing_country'][0]) ? $user_meta['billing_country'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_state"><?php _e('Estado', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_state" id="billing_state" value="<?php echo esc_attr(isset($user_meta['billing_state'][0]) ? $user_meta['billing_state'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_whatsapp"><?php _e('WhatsApp', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_whatsapp" id="billing_whatsapp" value="<?php echo esc_attr(isset($user_meta['billing_whatsapp'][0]) ? $user_meta['billing_whatsapp'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_celular"><?php _e('Celular/Telefone', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_celular" id="billing_celular" value="<?php echo esc_attr(isset($user_meta['billing_celular'][0]) ? $user_meta['billing_celular'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_cpf"><?php _e('CPF', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_cpf" id="billing_cpf" value="<?php echo esc_attr(isset($user_meta['billing_cpf'][0]) ? $user_meta['billing_cpf'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_rg"><?php _e('RG', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_rg" id="billing_rg" value="<?php echo esc_attr(isset($user_meta['billing_rg'][0]) ? $user_meta['billing_rg'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_uf_rg"><?php _e('UF RG', 'custom-checkout'); ?></label>
            <select id="billing_uf_rg" name="billing_uf_rg" required>
                <option value=""><?php _e('Selecione', 'custom-checkout'); ?></option>
                <option value="AC" <?php selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'AC'); ?>>AC</option>
                <option value="AL" <?php selected( isset($user_meta['billing_uf_rg'][0]) ? $user_meta['billing_uf_rg'][0] : '', 'AL'); ?>>AL</option>
                <!-- Adicione todas as outras siglas de estados brasileiros aqui -->
            </select>
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_orgao_expedidor"><?php _e('Órgão Expedidor', 'custom-checkout'); ?></label>
            <select id="billing_orgao_expedidor" name="billing_orgao_expedidor" required>
                <option value=""><?php _e('Selecione', 'custom-checkout'); ?></option>
                <option value="SSP" <?php selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'SSP'); ?>>SSP</option>
                <option value="DETRAN" <?php selected( isset($user_meta['billing_orgao_expedidor'][0]) ? $user_meta['billing_orgao_expedidor'][0] : '', 'DETRAN'); ?>>DETRAN</option>
                <!-- Adicione todos os outros órgãos expedidores disponíveis no Brasil -->
            </select>
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_nome_mae"><?php _e('Nome da Mãe', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_nome_mae" id="billing_nome_mae" value="<?php echo esc_attr(isset($user_meta['billing_nome_mae'][0]) ? $user_meta['billing_nome_mae'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_naturalidade"><?php _e('Naturalidade', 'custom-checkout'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_naturalidade" id="billing_naturalidade" value="<?php echo esc_attr(isset($user_meta['billing_naturalidade'][0]) ? $user_meta['billing_naturalidade'][0] : ''); ?>" required />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_estado_civil"><?php _e('Estado Civil', 'custom-checkout'); ?></label>
            <select id="billing_estado_civil" name="billing_estado_civil" required>
                <option value=""><?php _e('Selecione', 'custom-checkout'); ?></option>
                <option value="solteiro" <?php selected( isset($user_meta['billing_estado_civil'][0]) ? $user_meta['billing_estado_civil'][0] : '', 'solteiro'); ?>>Solteiro(a)</option>
                <option value="casado" <?php selected( isset($user_meta['billing_estado_civil'][0]) ? $user_meta['billing_estado_civil'][0] : '', 'casado'); ?>>Casado(a)</option>
                <!-- Adicione todas as outras opções de estado civil -->
            </select>
        </p>

        <h3><?php _e('Documentos', 'custom-checkout'); ?></h3>
        <?php
        $this->render_edit_account_file_upload_field('identificacao', 'Envie seu comprovante de CPF (ID, CNH ou...)', $user_meta);
        $this->render_edit_account_file_upload_field('escolaridade', 'Comprovante de Escolaridade', $user_meta);
        $this->render_edit_account_file_upload_field('exp_profissional', 'Envie Comprovante de Experiência Profissional', $user_meta);
        $this->render_edit_account_file_upload_field('residencia', 'Comprovante de Residência', $user_meta);
        $this->render_edit_account_file_upload_field('certidao', 'Certidão de Nascimento ou Casamento', $user_meta);
        $this->render_edit_account_file_upload_field('eleitor', 'Título de Eleitor', $user_meta);
    }

    private function render_edit_account_file_upload_field($field_name, $label, $user_meta) {
        $file_url = isset($user_meta[$field_name][0]) ? $user_meta[$field_name][0] : '';
        ?>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="<?php echo $field_name; ?>"><?php _e($label, 'custom-checkout'); ?></label>
            <?php if ($file_url) : ?>
                <a href="<?php echo esc_url($file_url); ?>" target="_blank"><?php _e('Baixar arquivo atual', 'custom-checkout'); ?></a><br />
            <?php endif; ?>
            <input type="file" class="woocommerce-Input woocommerce-Input--file input-file" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" /><br />
            <span class="description"><?php _e('Envie um novo arquivo para substituir o atual.', 'custom-checkout'); ?></span>
        </p>
        <?php
    }

    public function custom_save_account_details($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        $fields_to_update = [
            'billing_address', 'billing_city', 'billing_postcode', 'billing_country', 'billing_state',
            'billing_whatsapp', 'billing_celular', 'billing_cpf', 'billing_rg', 'billing_uf_rg', 
            'billing_orgao_expedidor', 'billing_nome_mae', 'billing_naturalidade', 'billing_estado_civil'
        ];

        foreach ($fields_to_update as $field) {
            if (isset($_POST[$field])) {
                update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        foreach (['identificacao', 'escolaridade', 'exp_profissional', 'residencia', 'certidao', 'eleitor'] as $field_name) {
            if (isset($_FILES[$field_name]) && !empty($_FILES[$field_name]['name'])) {
                $uploadedfile = $_FILES[$field_name];
                $upload_overrides = array('test_form' => false);

                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                if ($movefile && !isset($movefile['error'])) {
                    update_user_meta($user_id, $field_name, $movefile['url']);
                }
            }
        }
    }

    public function display_coupon_form() {
        if ( wc_coupons_enabled() ) {
            echo '<div class="woocommerce-form-coupon-toggle cupom-checkout">';
            echo '<div class="woocommerce-info">';
            echo 'Tem um cupom? <a href="#" class="showcoupon">' . __( 'Clique aqui para inserir seu código', 'woocommerce' ) . '</a>';
            echo '</div>';
            echo '</div>';

            woocommerce_checkout_coupon_form();
        }
    }
}

Custom_Checkout::get_instance();
