# ROADMAP.md — Melhorias futuras e issues sugeridas

Itens aqui **não estão aprovados** para implementação. Aguardam análise, priorização e movimentação para **Ready** no GitHub Projects.

## Prioridade sugerida

### P1 — Aprovada no backlog, aguarda Ready

#### Issue existente #3: Treinamento presencial — formulário, pagamento e painel admin
- **Status:** In Review (v1 implementada no tema v1.2.0).
- **Contexto:** Formação Coaching|Terapia e futuros cursos presenciais.
- **Escopo entregue:** Gateway «Acerto com Saulo», questionário pós-pedido (schema hardcoded), painel inscrições, estoque WC, check-in manual.
- **Nota:** Questionário editável via admin → issue **#4**.

#### Issue existente #4: CRUD formulários pós-inscrição por produto
- **Status:** In Review (v1.3.0).
- **Escopo entregue:** CRUD admin, vínculo produto, online + presencial, edição aluno, CSV dinâmico, migração seed coaching-terapia.
- **Decisões:** ADR-007, D19–D28 em `DECISIONS.md`.

#### Issue existente #1: Nova identidade visual
- **Contexto:** Atualizar fontes e cores para Playfair Display + azul escuro/dourado.
- **Escopo:** `tailwind.config.js`, `header.php`, `functions.php` (fonts), `style.css`, templates.
- **Fora de escopo:** Mudança de layout estrutural, novas páginas.
- **Critérios de aceite:** Fonte carregada corretamente; paleta aplicada de forma consistente; contraste acessível; staging validado.
- **Riscos:** Playfair Display é Google Font gratuita (OFL), mas verificar peso/uso; impacto em todo o tema.
- **Como testar:** Home, loja, checkout, mobile — comparar com mockups em `modelo/`.

### P2 — Sugestões para novas issues (aguardam aprovação)

#### Migrar Tailwind CDN para build local
- **Problema:** `header.php` usa CDN temporário; `dist/output.css` não existe no repo.
- **Objetivo:** Enfileirar CSS compilado, remover CDN, uma única fonte de config.
- **Escopo:** `npm run build`, enqueue em `functions.php`, remover script CDN.
- **Riscos:** Classes dinâmicas podem exigir safelist no Tailwind config.

#### Adicionar nonces ao AJAX de quantidade no checkout
- **Problema:** `saulocoelho_ajax_update_checkout_qty` aceita `nopriv` sem nonce explícito.
- **Objetivo:** Proteger endpoint com `check_ajax_referer` e validação de sessão/carrinho.
- **Riscos:** Regressão no fluxo de checkout se mal implementado.

#### Refatorar metaboxes em módulos menores
- **Problema:** `inc/metaboxes.php` concentra ~800 linhas.
- **Objetivo:** Separar por página/seção (`inc/metaboxes/home.php`, etc.) sem mudar comportamento.
- **Fora de escopo:** Alterar campos ou UX admin nesta etapa.

#### Extrair JavaScript inline do checkout
- **Problema:** Scripts de qty e ViaCEP embutidos em `functions.php`.
- **Objetivo:** Arquivos `.js` enfileirados com `wp_enqueue_script` + localize.
- **Benefício:** Versionamento, cache, testabilidade.

#### Criar PRD real do produto
- **Problema:** `product_requirements_document.md` é template vazio.
- **Objetivo:** Documentar requisitos de negócio, personas, KPIs, escopo MVP.

#### Pipeline de deploy / CI
- **Problema:** Deploy manual via Drive + servidor.
- **Objetivo:** GitHub Actions ou fluxo documentado staging → produção.
- **Dependências:** Acesso SSH/FTP, credenciais em secrets.

#### Testes automatizados mínimos
- **Objetivo:** PHPUnit para funções críticas (gate checkout, sanitização); smoke E2E opcional.
- **Prioridade:** Após estabilizar fluxo de governança.

#### Auditoria de segurança WooCommerce
- **Objetivo:** Revisar capabilities, nonces em forms custom, escape em templates, prepared statements se houver queries custom.

#### Documentar dependências de plugins
- **Objetivo:** Listar plugins obrigatórios (WooCommerce, CPF/CNPJ BR, etc.) e versões testadas.

## Melhorias de processo (sem issue ainda)

- Padronizar mensagens de commit (feat/fix/docs/chore).
- Branch por issue (`issue-1-identidade-visual`).
- PR obrigatório antes de merge em `main`.
- Checklist de teste manual em cada PR.

## Não fazer sem issue aprovada

- Refatorações amplas em `functions.php`.
- Mudanças de checkout/fluxo de vendas.
- Alterações de schema/banco (não há plugins custom com tabelas hoje).
- Criação automática de issues no GitHub.
