# SECURITY.md — Segurança do projeto Saulocoelho.com

Regras específicas para o tema WordPress + WooCommerce deste repositório.

## Princípios gerais

1. **Validar entrada, escapar saída** em todo PHP e template.
2. **Nonces** em ações admin, AJAX e formulários custom.
3. **Capabilities** — funções admin só para quem tem permissão.
4. **Nunca** commitar `.env`, `wp-config.php`, chaves de API ou senhas.
5. **Logs** não devem registrar CPF, senhas, tokens ou dados de cartão.

## WordPress — funções obrigatórias

| Ação | Funções |
|------|---------|
| Sanitizar texto | `sanitize_text_field`, `sanitize_email`, `absint` |
| Sanitizar HTML | `wp_kses_post`, `wp_kses` |
| Escapar output | `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` |
| SQL | `$wpdb->prepare()` — evitar SQL concatenado |
| AJAX | `check_ajax_referer`, `wp_verify_nonce` |
| Redirect | `wp_safe_redirect` + `exit` |
| Arquivos | `ABSPATH` guard no topo: `if ( ! defined( 'ABSPATH' ) ) exit;` |

## Áreas sensíveis neste projeto

### Checkout gate (`inc/module-checkout-gate.php`)
- Cadastro/login de usuários no fluxo de compra.
- Normalização de endereço e validação CPF/CNPJ.
- **Cuidado:** qualquer mudança afeta autenticação e dados pessoais (LGPD).

### AJAX quantidade no checkout (`functions.php`)
- Endpoint `saulocoelho_update_checkout_qty` registrado para usuários logados e `nopriv`.
- **Risco:** manipulação de carrinho sem verificação forte de nonce.
- **Recomendação:** issue futura para adicionar nonce e limitar abuso.

### Metaboxes (`inc/metaboxes.php`)
- Salvar post meta no admin — usar `current_user_can('edit_post', $post_id)` e nonces nos saves.
- Revisar qualquer novo campo com dados de URL (usar `esc_url_raw`).

### ViaCEP (JavaScript no checkout)
- Chamada a API externa `viacep.com.br` — apenas leitura de CEP público.
- Não confiar nos dados retornados sem revalidação server-side (WooCommerce já valida campos).

### Uploads e mídia
- Usar APIs WordPress (`wp_enqueue_media`) — não implementar upload custom sem validação MIME.

## WooCommerce

- Preferir hooks e APIs WC em vez de alterar core do plugin.
- Campos de checkout: filtros em `woocommerce_checkout_fields` — não remover validações obrigatórias legais (CPF etc.) sem análise.
- Páginas de obrigado/falha custom — garantir que IDs de pedido não vazem dados de terceiros.

## Dependências externas

| Recurso | Uso | Risco |
|---------|-----|-------|
| Google Fonts (Inter) | Tipografia | Privacidade/CDN — considerar self-host no futuro |
| Material Symbols | Ícones admin | Mesmo |
| Tailwind CDN | Estilos layout | Supply chain, CSP |
| ViaCEP | Autocomplete endereço | Disponibilidade API |

## Checklist antes de merge (mudanças com impacto)

- [ ] Entradas `$_POST`/`$_GET` sanitizadas?
- [ ] Output em templates escapado?
- [ ] Nonce em forms/AJAX novos ou alterados?
- [ ] Permissões verificadas em callbacks admin?
- [ ] Nenhum segredo no diff?
- [ ] Testado em staging com usuário logado e deslogado?

## Reportar vulnerabilidades

Registrar em issue privada ou contato direto com o mantenedor — não detalhar exploit publicamente antes de correção.
