# Custom Checkout Plugin

## Descrição

O plugin **Custom Checkout** é um plugin de WordPress para WooCommerce que personaliza a página de checkout, adicionando campos específicos de informações do cliente, como Nome Completo, Email, WhatsApp, CPF, Data de Nascimento, Senha e Confirmação de Senha. Além disso, o plugin permite a inclusão de documentos adicionais após a conclusão do pedido e envia um e-mail com todas as informações para o administrador.

## Funcionalidades

### Personalização do Checkout

- **Remoção de campos não necessários:** Remove campos padrão do WooCommerce que não são necessários.
- **Adição de campos personalizados obrigatórios:** Inclui campos personalizados e obrigatórios como:
  - Nome Completo
  - Email
  - WhatsApp
  - CPF
  - Data de Nascimento
  - Senha
  - Confirmar Senha

### Página de Agradecimento

- **Formulário para informações adicionais:** Exibe um formulário para o cliente enviar informações adicionais após a conclusão do pedido.
- **Campos obrigatórios na página de agradecimento:**
  - Endereço
  - Cidade
  - CEP
  - País
  - Estado
  - WhatsApp
  - Celular/Telefone
  - CPF
  - RG
  - UF RG
  - Órgão Expedidor
  - Nome da Mãe
  - Naturalidade
  - Estado Civil

### Upload de Documentos

- **Campos para upload de documentos:**
  - Comprovante de CPF (ID, CNH ou similar)
  - Comprovante de Escolaridade
  - Comprovante de Experiência Profissional
  - Comprovante de Residência
  - Certidão de Nascimento ou Casamento
  - Título de Eleitor

### Notificações por Email

- **Envio de email para o administrador:** Envia um e-mail para o administrador com as informações adicionais e documentos enviados pelo cliente.

### Integração com o Perfil do Usuário

- **Campos adicionais no perfil do usuário:** Os campos de informações adicionais e upload de documentos estão disponíveis na página de perfil do usuário.

### Configurações de Segurança

- **Utilização de `wp_nonce`:** Segurança nas requisições AJAX.
- **Sanitização de todos os campos de entrada de dados:** Garantia de que os dados são seguros.

## Instalação

1. Clone o repositório para o diretório de plugins do WordPress:
   ```sh
   git clone https://github.com/eumichaelcampos/custom-checkout.git wp-content/plugins/custom-checkout
   ```

2. Ative o plugin no painel de administração do WordPress.

## Uso

### Como Adicionar ou Remover Campos

#### Adicionar Campos Personalizados

Os campos personalizados adicionados no checkout podem ser alterados no método `customize_checkout_fields` da classe `Custom_Checkout`.

```php
public function customize_checkout_fields( $fields ) {
    // Exemplo de campo personalizado
    $fields['billing']['billing_custom_field'] = array(
        'label'       => __( 'Nome do Campo', 'custom-checkout' ),
        'required'    => true,
        'class'       => array( 'form-row-wide' ),
        'priority'    => 80,
    );
    return $fields;
}
```

#### Remover Campos Padrão

Para remover campos padrão do WooCommerce, utilize a função `unset` no método `customize_checkout_fields`.

```php
public function customize_checkout_fields( $fields ) {
    unset($fields['billing']['billing_company']); // Exemplo de remoção de campo
    return $fields;
}
```

### Tipos de Campos Suportados

Você pode adicionar diferentes tipos de campos no checkout. Alguns exemplos de tipos de campos suportados são:

- Texto (`type` => `text`)
- Email (`type` => `email`)
- Número (`type` => `number`)
- Data (`type` => `date`)
- Senha (`type` => `password`)
- Seleção (`type` => `select`)
- Arquivo (`type` => `file`)

### Alterar Campos Adicionais na Página de Agradecimento

Os campos adicionais na página de agradecimento podem ser modificados no método `additional_info_form` da classe `Custom_Checkout`.

```php
public function additional_info_form( $order_id ) {
    echo '<p><label for="billing_custom_field">' . __( 'Nome do Campo', 'custom-checkout' ) . '</label><input type="text" id="billing_custom_field" name="billing_custom_field" value="" required /></p>';
}
```

### Alterar Campos do Perfil do Usuário

Os campos personalizados no perfil do usuário podem ser alterados nos métodos `custom_user_profile_fields` e `custom_edit_account_form` da classe `Custom_Checkout`.

```php
public function custom_user_profile_fields($user) {
    // Adicionar campos personalizados no perfil do usuário
}
```

### Personalizar Emails

O método `send_admin_email` é responsável pelo envio de email para o administrador com as informações adicionais do cliente. Você pode personalizar o conteúdo do email neste método.

```php
private function send_admin_email($user_id, $attachments) {
    $email_content = "Informações do Cliente:\n\n";
    $email_content .= "Nome Completo: " . get_user_meta($user_id, 'billing_first_name', true) . "\n";
    // Adicione mais campos conforme necessário
    // Envie o email
}
```

## Estrutura do Projeto

```
custom-checkout/
├── assets/
│   ├── css/
│   │   └── custom-checkout.css
│   └── js/
│       └── custom-checkout.js
├── custom-checkout.php
├── includes/
│   └── class-custom-checkout.php
└── readme.md
```

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## Autor

- **Michael Campos** - (https://github.com/eumichaelcampos)
```
