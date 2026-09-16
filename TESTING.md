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

### Quiz 8 Códigos de Reação (`vtis-quiz` 1.3.23)

URL: https://saulocoelho.com/quiz/8-codigos-de-reacao/

- [ ] Intro carrega; 32 afirmações; escala 0–5 (Nunca … Sempre)
- [ ] Lead (nome, e-mail, WhatsApp, consentimento) antes do resultado
- [ ] Mapa: oito códigos com média **x,x / 5** (ex. 16 pontos brutos → 4,0/5), sem selo de predominante
- [ ] Faixa por barra (Não aparece … Código dominante)
- [ ] E-mail/relatório também em /5
- [ ] Disclaimer de não-diagnóstico visível
- [ ] Admin: quiz publicado; Entrega imediata; Pontuação nas barras = média

Regressão: `/quiz/neuropsicanalise/`, `/quiz/mapa/`, `/quiz/codigo-da-lideranca/` continuam a mostrar soma bruta nas barras.

### Palestra Teresópolis (`/palestra/`)

Requer **deploy FTP do tema** em produção (autorização expressa). Depois:

- [x] https://saulocoelho.com/palestra/ abre o formulário (mobile)
- [x] Sem cadastro, o PDF não é acessível por URL direta em `/wp-content/themes/saulocoelho/private/`
- [x] Envio válido → botão **Baixar PDF** e e-mail com anexo ou link (48 h)
- [x] Recarregar `/palestra/` sem token volta ao formulário (sem ficheiro)
- [x] Link expirado / inventado → recusa
- [ ] Admin **Palestras**: criar/editar evento (data, local, textos, campos, ficheiros, fundo); `/palestra/` continua a palestra principal
- [ ] Nova palestra em `/palestra/{slug}/` com QR próprio
- [ ] Admin **Palestras → Leads**: filtro por evento + CSV
- [ ] Checkbox LGPD obrigatório; honeypot não cria lead visível

O PWA https://app.saulocoelho.com não deve receber estes dados.

### Convite LMS — handoff sem e-mail (`/inscricao/{slug}/`)

Requer **deploy do tema**. Token real da metabox Convite (Leadership Academy).

- [ ] Token inválido: continua a mensagem de erro; **sem** CTA de “inscrição confirmada”
- [ ] Cadastro novo (e-mail inédito): após enviar, o aluno fica **logado** e chega à sala `/curso/leadership-academy/` (ou vê botões Entrar na sala / Definir senha)
- [ ] Sem abrir o e-mail, consegue estudar; em Minha Conta aparece o aviso para definir senha
- [ ] Gravar senha em Detalhes da conta remove o aviso
- [ ] E-mail já existente: continua o fluxo do plugin (login em Minha Conta); dica “Esqueci a senha” se `redirect_to` for o curso
- [ ] Checkout `/boas-vindas/` e cadastro da loja **inalterados**
- [ ] E-mail de acesso do plugin, se chegar, continua válido (backup)

## Testes automatizados (futuro)

Ver `ROADMAP.md` — PHPUnit para funções puras; E2E opcional para checkout. Não implementado nesta etapa.

## Validação antes de Done

Somente o mantenedor move para **Done** após smoke test em staging (ou produção, se acordado).
