# TESTING.md — Como testar o projeto

Não há suíte automatizada hoje. Validação é **manual** em staging antes de produção.

## Ambientes

| Ambiente | URL | Uso |
|----------|-----|-----|
| Staging | https://saulo.vtis.com.br | Testes principais |
| Produção | https://saulocoelho.com | Apenas após validação |

Após alterar arquivos locais: sincronizar com servidor e limpar cache WordPress/CDN.

## Setup local do tema (build CSS)

```bash
cd wp-content/themes/saulocoelho
npm install
npm run build    # gera dist/output.css
npm run watch    # desenvolvimento
```

## Testes por tipo de alteração

### Alteração visual (CSS, templates, Tailwind)

1. Home (`/`)
2. Sobre (`page-about`)
3. Programas / catálogo
4. Loja (`page-store`)
5. Página de curso / produto WooCommerce
6. Mobile (menu, hero, cards)
7. Contraste e legibilidade (dark mode)

### Alteração em metaboxes / conteúdo admin

1. Editar página no WP Admin — campos aparecem conforme template?
2. Salvar e recarregar — valores persistem?
3. Front exibe conteúdo salvo corretamente?

### Alteração em WooCommerce / checkout

1. **Visitante:** adicionar produto → redireciona para checkout gate (`/boas-vindas/`)?
2. **Cadastro/login** no gate funciona?
3. **Logado:** checkout direto; campos BR (CEP, CPF se plugin ativo)?
4. ViaCEP preenche endereço com CEP válido?
5. Alterar quantidade no checkout recalcula total?
6. Pedido concluído → página de obrigado custom?
7. Falha de pagamento → página de falha custom?
8. My Account — navegação e dashboard custom?

### Alteração em módulos (`inc/module-*.php`)

1. Identificar página/hook afetado
2. Testar fluxo feliz e erro (dados inválidos, sessão expirada)
3. Verificar console do browser (erros JS)
4. Verificar `debug.log` do WordPress se habilitado

### Alteração apenas em documentação

- Revisar links internos entre arquivos `.md`
- Confirmar que nenhum arquivo PHP foi alterado acidentalmente

## Regressão mínima (smoke test)

Executar antes de mover issue para **Done**:

- [ ] Home carrega sem erro 500
- [ ] Menu principal funciona (desktop + mobile)
- [ ] Loja lista produtos
- [ ] Adicionar ao carrinho → checkout (ou gate)
- [ ] Login admin intacto
- [ ] Uma página com metaboxes salva corretamente

## O que registrar na issue ao concluir

```
## Testes realizados
- [x] Item testado — resultado

## Testes pendentes
- [ ] Item não testado — motivo

## Como validar
1. Passo a passo para o revisor humano
```

## Testes automatizados (futuro)

Ver `ROADMAP.md` — PHPUnit para funções puras; E2E opcional para checkout. Não implementado nesta etapa.

## Validação antes de Done

Somente o mantenedor move para **Done** após smoke test em staging (ou produção, se acordado).
