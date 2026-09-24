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

### Alumni / galeria de turmas (issue #25 / tema ≥ 1.3.43)

1. Em **Cursos (ama_course)** → metabox «Fotos Alumni»: adicionar/remover fotos; gravar; recarregar.
2. Em **produto Woo** → Alumni: só textos + checkboxes de turmas; link «Editar fotos da turma»; contagem de fotos.
3. **Ferramentas → Alumni: migrar fotos** (uma vez em prod/staging se houver dados antigos) → galeria do produto continua a mostrar fotos.
4. Front do produto: `#alumni-galeria` com tabs; dois produtos com a mesma turma partilham as mesmas fotos.
5. Minha Conta → Minhas Turmas (se aplicável): fotos vêm do curso.
6. Produto sem turmas / turmas sem fotos → secção oculta.

### Alumni na sala do curso (issue #25 / tema ≥ 1.3.44 + Ama ≥ 1.0.52)

1. No curso: marcar **Exibir galeria na hub** (+ opcionalmente **alunos podem enviar**); gravar.
2. Aluno matriculado: card **Fotos** + secção `#ama-hub-fotos`; vê fotos oficiais e de colegas.
3. Com upload activo: **Enviar foto** (JPEG/PNG/WebP ≤ 5 MB) → aparece na grelha; autor pode remover a sua.
4. Sem a opção de upload: aluno vê mas não tem botão; mentor/admin ainda podem enviar.
5. Visitante / não matriculado: secção não aparece.
6. Desmarcar «Exibir na hub» → secção e card desaparecem.

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

### Leadership Academy — convite (`/inscricao/{slug}/`)

Requer **deploy** do plugin AmaEducacional ≥ **1.0.41** **e** do tema ≥ **1.3.36** (senha no form + contraste handoff). Token real da metabox Convite.

- [ ] Curso: metabox Convite activo; catálogo público desligado; “gratuito com conta” desligado
- [ ] Link com `?t=` abre o formulário com **senha + confirmação**; token inválido não matricula
- [ ] Conta nova: fica **logado**, chega à sala; **logout → login** com e-mail/senha do form (sem abrir e-mail)
- [ ] Conta nova: **não** mostra banner «Definir minha senha» (só contas antigas sem senha no form)
- [ ] Banner legado (se aparecer): texto legível no Portal dark
- [ ] E-mail já existente: matricula a vaga e **redireciona para Minha Conta** (senha do form ignorada)
- [ ] Conta nova: aparece em AmaEducacional → Alunos
- [ ] Checkout `/boas-vindas/` inalterado
- [ ] https://saulocoelho.com/curso/leadership-academy/ sem login: porta de convite (sem “Ir para a loja”)
- [ ] Aluno matriculado na mesma URL: sala com Aulas / Avaliações / Materiais / Certificado
- [ ] https://saulocoelho.com/privacidade/ abre o aviso (template Legal); convite e rodapé apontam para essa URL

### Minha Conta / Portal (regressão UI)

- [ ] Minha Conta mobile: menu acima e conteúdo (Meus Cursos) logo abaixo — sem faixa vazia enorme até ao rodapé
- [ ] Minha Conta desktop (≥1024px): sidebar à esquerda + conteúdo à direita
- [ ] Header mobile aberto: fundo opaco a cobrir a página; itens do menu WP + bloco Área do Aluno / Sair
- [ ] Header mobile: ícone Entrar / conta ao lado do carrinho; no hamburger, «Área do Aluno» (e «Sair» se logado)
- [ ] Minha Conta login: ícone olho dourado à direita da senha; toque mostra/oculta o texto
- [ ] Minha Conta senha errada: aviso em português **sem** repetir «N tentativa(s) restante(s)»
- [ ] Minha Conta logada: menu lateral sem linhas douradas por baixo dos ícones
- [ ] Minha Conta: avisos (senha temporária / erro) sem ícone sobreposto ao texto
- [ ] Minha Conta já logada: não mostrar aviso de bloqueio/tentativas de login

### Leadership Academy — Noite 1 (`vtis-quiz` 1.3.33 + AmaEducacional 1.0.31)

Requer **deploy** dos dois plugins. Após actualizar, seed Noite 1 + migração DB 22 (Conselho 5 campos) / DB 21 (uma resposta).

- [ ] Ex. 1: 24 campos (6 temas × 4 frases) + 2 reflexões finais — sem chip de tema único
- [ ] Completar um exercício no player → checkbox marcado e progresso sobe
- [ ] Reabrir o mesmo exercício: aparece a síntese (não o formulário vazio)
- [ ] Com refazer permitido: botão Refazer → novo envio substitui a resposta (admin/lista: uma submission)
- [ ] Curso com «Permitir refazer exercícios (vtis-quiz)» desmarcado: sem Refazer
- [ ] PDPA: 7 textos abertos (P/D/P/A + 3 de impacto) — sem chips inventados
- [ ] Espelho: comportamento, situação, 5 do Espelho, 3 do Observador; fecho em **lacunas** (inputs inline) e síntese com frase montada
- [ ] Matriz da Crença: inclui «ainda faz sentido?» + pergunta de confronto
- [ ] Conselho: **5** campos editáveis (desafio + 3 sínteses + decisão); sub-perguntas Passado/Presente/Futuro só como guia (sem inputs)
- [ ] Ex. 6: prompts iguais à apostila
- [ ] Skin Saulo aplica-se; login obrigatório
- [ ] Pendente opcional: Mapa da Origem (fecho da apostila)

### Leadership Academy — Noite 2 (`vtis-quiz` ≥ 1.3.39, Ama ≥ 1.0.44, issue #19)

Requer **deploy** vtis-quiz (DB **26** cria seed MAPA) + AmaEducacional **1.0.44** (seed grade `v2`).

- [ ] `/quiz/leadership-rpsp/` — 30 Likert + 5 desempates + final + 4 textos; resultado com dominante/secundário e mapa 0–30
- [ ] Empate forçado (totais iguais) → desempate por 5s / 4–5 / TieBreak / pergunta final
- [ ] `/quiz/leadership-radar-pressao/`, `decidir-antes`, `cmv-24h` — reflection `all_at_once`, login, uma resposta
- [ ] `/quiz/leadership-mapa-pressao/` — 10 campos do entregável (cenário → CMV 24h); síntese no fim
- [ ] Skin Saulo; OCD / app não afetados
- [ ] Grade Noite 2 com **5** aulas; MAPA como última
- [ ] Abrir módulo Noite 2 no player e concluir o MAPA (checkbox progresso)

### Leadership Academy — Noite 3 (`vtis-quiz` ≥ 1.3.40, Ama ≥ 1.0.48, issue #24)

Requer **deploy** vtis-quiz (DB **27**) + AmaEducacional **1.0.48** (seed grade `noite3_v1`).

- [ ] `/quiz/leadership-lider-tornar/` — 8 campos + síntese; reflection; login; uma resposta
- [ ] `/quiz/leadership-meta-master/` — 8 campos + frase final Meta Master
- [ ] `/quiz/leadership-mapa-lider/` — 10 campos do entregável
- [ ] Skin Saulo; Noite 1/2 / OCD não afetados
- [ ] Grade com módulo **Noite 3** e **3** aulas
- [ ] Abrir módulo Noite 3 no player e concluir o MAPA

### Painel do mentor (Ama ≥ 1.0.35, issue #20)

Requer **deploy** AmaEducacional **1.0.35**.

- [ ] Curso → metabox «Mentores / monitores»: adicionar/remover utilizadores
- [ ] Login como mentor → hub mostra «Painel da turma» (agregados + lista + % por aula)
- [ ] Labels: «Ativos», «Exercícios»; mobile empilha título/%; clique no exercício abre modal
- [ ] Tabela: Nome largo; Editar (nome/e-mail/telefone) e Remover da turma
- [ ] Aluno comum → hub **sem** painel
- [ ] Mentor **sem** matrícula → acede ao painel (não vê grade de aluno)
- [ ] Admin (`manage_options`) acede mesmo fora da lista
- [ ] REST `GET /wp-json/ama-lms/v1/courses/{id}/mentor/dashboard` com cookie/nonce → 200; sem auth → 401; aluno → 403
- [ ] Sem respostas abertas / scores RPSP no painel

### Portal — notificar ao publicar aula (#22, tema ≥ 1.3.40 + Ama ≥ 1.0.45)

Requer **deploy** tema + AmaEducacional.

- [ ] Editar `ama_lesson` associada a um curso: metabox «Notificar alunos (Portal)»
- [ ] Sem checkbox → gravar → nenhum aviso novo
- [ ] Com «Notificar» + aula publicada → aviso no sininho dos matriculados; link abre `?lesson_id=`
- [ ] Push marcado → fila/job (se VAPID ok); e-mail só se checkbox e-mail
- [ ] Segunda gravação com notificar (sem Forçar) → não duplica; mensagem de aviso
- [ ] «Forçar novo aviso» → cria segundo aviso
- [ ] Curso silenciado (#16) → inbox ok; push/e-mail bloqueados
- [ ] Aula em rascunho → mensagem a pedir publicar
- [ ] Login como **mentor**: Painel da turma → Conclusão por aula → **Notificar** → aluno vê sininho
- [ ] **Reenviar aviso** pede confirmação e cria novo aviso
- [ ] Aluno comum → hub sem painel / sem botão

### Portal — mentor envia PDF de apoio (#23, tema ≥ 1.3.41 + Ama ≥ 1.0.46)

Requer **deploy** tema + AmaEducacional.

- [ ] Mentor → Painel da turma → **Materiais de apoio** → escolher **Módulo** (ex. Noite 1) + título + PDF → Enviar
- [ ] Material aparece no módulo escolhido na grade (sidebar do player)
- [ ] Material aparece em Material de apoio do hub e na grade
- [ ] Com «Notificar» → sininho do aluno; link abre o material
- [ ] Sem «Notificar» → só na grade
- [ ] **Reenviar aviso** no material listado
- [ ] Ficheiro não-PDF rejeitado
- [ ] Aluno comum sem formulário de upload

### Portal do Aluno — avisos / sininho (#16 Fase A, tema ≥ 1.3.26)

Requer **deploy do tema** (cria tabelas no 1.º `after_setup_theme`).

- [ ] WP Admin → **Avisos Portal**: criar aviso publicado para curso Leadership Academy
- [ ] Aluno matriculado logado no Portal: badge no sininho; abre painel e vê o aviso
- [ ] Aluno **não** matriculado / audience `user:` errada: **não** vê o aviso
- [ ] Clicar no aviso marca como lido; badge desce
- [ ] Home / palestra / visitante: sem sininho
- [ ] Shell Portal / PWA install sem regressão

### Portal do Aluno — Web Push (#16 Fase B, tema ≥ 1.3.29)

Requer **deploy do tema incluindo `vendor/`** (minishlink/web-push) e HTTPS.

- [ ] Conta → «Ativar notificações» → permissão do navegador → estado ativo
- [ ] iOS: sem PWA instalada → dica; com PWA + permissão → subscribe OK
- [ ] Admin: publicar aviso com «Enviar push agora» → aluno recebe notificação do SO
- [ ] Clique na notificação abre a URL do aviso (ou Minha Conta)
- [ ] Quem negou permissão: só sininho (sem crash)
- [ ] Subscription 410: removida na próxima tentativa de envio
- [ ] `app.saulocoelho.com` (OCD) não afetado

### Portal do Aluno — fila / e-mail (#16 Fase C, tema ≥ 1.3.31)

Requer **deploy do tema** (cria `sc_portal_push_jobs` no upgrade option push ≥ 2).

- [ ] Publicar aviso com «Enviar push agora» → coluna **Envio** mostra job (Concluído / A enviar…)
- [ ] Turma grande: 1.º lote imediato; resto completa via cron (~1 min)
- [ ] Checkbox e-mail → destinatários da audiência recebem `wp_mail` (assunto = título)
- [ ] Silenciar curso → novo aviso `course:{id}` **não** enfileira (mensagem no admin); sininho continua
- [ ] Audiência `all` / `user:` não é bloqueada pelo mute de curso
- [ ] Quem negou push: e-mail (se marcado) ainda pode chegar; sininho intacto
- [ ] OCD / `app.saulocoelho.com` não afetado
### Portal do Aluno — issue #15 (tema ≥ 1.3.23)

Requer **deploy do tema**. Após 1.ª carga, rewrite `/portal-aluno/` é gravado (ou regenerar permalinks se 404).

- [ ] Logado em `/minha-conta/`: topbar «Portal do Aluno», sem hamburger/loja/WhatsApp/footer marketing
- [ ] Header marketing (logado): botão **Área do Aluno** (não «Área do Cliente»)
- [ ] Tabs: Cursos | Certificados | Conta — conteúdo correcto
- [ ] Tab Cursos (browser, não PWA): banner «Instale o Portal» **acima** de «Olá, …»; «Agora não» oculta; Instalar no Android quando o browser permitir
- [ ] Abrir como app instalada (`standalone`): banner **não** aparece
- [ ] Conta: lista detalhes, endereços, pedidos (+ questionário / turmas / pagamento se existirem) + Sair + CTA instalar
- [ ] Visitante / home / palestra / blog: sem CTA instalar nem shell
- [ ] Android: CTA / `beforeinstallprompt`; iOS: dica Partilhar → Ecrã inicial
- [ ] Abrir curso LMS a partir de Cursos: topbar + tabs do portal mantêm-se; conteúdo do curso/player no centro
- [ ] Visitante (não logado) em `/curso/…`: header/footer de marketing (sem shell do portal)
- [ ] Separado de `app.saulocoelho.com`

### Portal PWA — sessão persistente (#21, tema ≥ 1.3.32)

- [ ] Login em Minha Conta: checkbox «Manter-me ligado neste aparelho» vem **marcado**
- [ ] Mesmo desmarcando o checkbox, o cookie fica com validade (não só sessão) — login front
- [ ] PWA instalada: login → fechar app completamente → reabrir → continua logado
- [ ] `/wp-login.php` (admin): comportamento nativo (não forçado pelo módulo)
- [ ] Checkout gate (registo rápido): entra com cookie persistente

### Portal PWA — retomar última página (tema ≥ 1.3.34)

- [ ] Abrir uma aula no PWA → fechar app → reabrir pelo ícone → volta à mesma aula (`?lesson_id=`)
- [ ] Em Minha Conta (tab Cursos) de propósito → fechar → reabre em Cursos (não força aula antiga se foi a última)
- [ ] Sair (logout) limpa a posição guardada
- [ ] Abrir aviso/push com URL específica não é sobrescrito pelo resume (não é cold start na home)

## Testes automatizados (futuro)

Ver `ROADMAP.md` — PHPUnit para funções puras; E2E opcional para checkout. Não implementado nesta etapa.

## Validação antes de Done

Somente o mantenedor move para **Done** após smoke test em staging (ou produção, se acordado).
