=== Custom Checkout ===
Contributors: michaelcampos
Donate link: https://michaelcampos.com.br/
Tags: custom checkout, woocommerce, additional fields, file upload
Requires at least: 5.0
Tested up to: 5.7
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

O plugin Custom Checkout para WooCommerce permite adicionar campos personalizados no checkout, incluindo uploads de arquivos e informações adicionais do usuário. Ele também permite mover o campo de cupom para dentro do resumo do pedido.

== Installation ==

1. Faça o upload dos arquivos do plugin para o diretório `/wp-content/plugins/custom-checkout` ou instale o plugin diretamente através da tela de plugins do WordPress.
2. Ative o plugin através da tela 'Plugins' no WordPress.
3. Configure os campos adicionais na página de configurações do WooCommerce, se necessário.

== Frequently Asked Questions ==

= Como eu adiciono ou removo novos campos? =

Para adicionar ou remover campos, você pode editar a função `customize_checkout_fields` no arquivo principal do plugin. Adicione novos campos ao array `$fields['billing']` ou remova campos existentes com a função `unset`.

= Quais tipos de campos podem ser usados? =

Você pode usar diversos tipos de campos como texto, email, número, telefone, data e upload de arquivos. Abaixo estão alguns exemplos:

```php
$fields['billing']['billing_novo_campo'] = array(
    'label'       => __( 'Novo Campo', 'custom-checkout' ),
    'required'    => true,
    'class'       => array( 'form-row-wide' ),
    'priority'    => 80,
    'type'        => 'text', // ou 'email', 'number', 'tel', 'date'
);
```

= Como alterar as informações do plugin? =

Para alterar as informações do plugin, como campos adicionais ou configurações de upload de arquivos, edite o arquivo principal do plugin localizado em `wp-content/plugins/custom-checkout/custom-checkout.php`.

== Screenshots ==

1. Página de checkout personalizada com campos adicionais.
2. Formulário de informações adicionais exibido na página de agradecimento.
3. Seção de upload de arquivos no perfil do usuário.

== Changelog ==

= 1.0 =
* Primeira versão do plugin com campos personalizados no checkout e upload de arquivos.

== Upgrade Notice ==

= 1.0 =
Primeira versão estável do plugin.

== Documentation ==

### Funcionalidades do Plugin

1. **Campos Personalizados no Checkout**: Adiciona novos campos ao checkout do WooCommerce, como nome completo, email, WhatsApp, CPF, data de nascimento, senha e confirmação de senha.
2. **Formulário de Informações Adicionais**: Exibe um formulário de informações adicionais na página de agradecimento do WooCommerce.
3. **Uploads de Arquivos**: Permite que os usuários façam upload de arquivos como comprovantes de residência, escolaridade, experiência profissional, entre outros.
4. **Campos Obrigatórios**: Todos os campos são obrigatórios e devem ser preenchidos pelos usuários.
5. **Validação de Saída de Página**: Adiciona uma mensagem de confirmação ao tentar sair da página de agradecimento sem preencher todas as informações.
6. **Mover Campo de Cupom**: Move o campo de cupom para dentro da área do resumo do pedido no checkout.

### Adicionar ou Remover Campos

Para adicionar ou remover campos no checkout, edite a função `customize_checkout_fields`:

```php
public function customize_checkout_fields( $fields ) {
    // Adicionar campos
    $fields['billing']['billing_novo_campo'] = array(
        'label'       => __( 'Novo Campo', 'custom-checkout' ),
        'required'    => true,
        'class'       => array( 'form-row-wide' ),
        'priority'    => 80,
        'type'        => 'text', // ou 'email', 'number', 'tel', 'date'
    );

    // Remover campos
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_1']);

    return $fields;
}
```

### Tipos de Campos Suportados

Você pode adicionar os seguintes tipos de campos ao checkout:
- `text`
- `email`
- `number`
- `tel`
- `date`
- `password`

### Alterar Informações do Plugin

Para alterar informações como campos adicionais ou configurações de upload de arquivos, edite o arquivo principal do plugin localizado em `wp-content/plugins/custom-checkout/custom-checkout.php`.

Siga as instruções no código para adicionar ou modificar funcionalidades conforme necessário.

Se precisar de mais assistência, consulte a [documentação do WooCommerce](https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/) ou entre em contato com o suporte.

```
