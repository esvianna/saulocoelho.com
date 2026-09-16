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

### Leadership Academy — convite (`/inscricao/{slug}/`)

Requer **deploy** do plugin AmaEducacional **e** do tema ≥ **1.3.25** (handoff). Token real da metabox Convite.

- [ ] Curso: metabox Convite activo; catálogo público desligado; “gratuito com conta” desligado
- [ ] Link com `?t=` abre o formulário; token inválido não matricula (**sem** CTA “inscrição confirmada”)
- [ ] E-mail já existente: matricula a vaga e **redireciona para Minha Conta** (com `redirect_to` do curso); dica “Esqueci a senha” no login
- [ ] Conta nova (e-mail inédito): fica **logado**, chega à sala `/curso/leadership-academy/` (ou vê Entrar na sala / Definir senha) **sem abrir o e-mail**
- [ ] Em Minha Conta / sala: aviso para definir senha; gravar Detalhes da conta remove o aviso
- [ ] Conta nova: aparece em AmaEducacional → Alunos; e-mail do plugin, se chegar, continua válido (backup)
- [ ] Checkout `/boas-vindas/` inalterado
- [ ] Minha Conta mobile: menu acima e conteúdo (Meus Cursos) logo abaixo — sem faixa vazia enorme até ao rodapé
- [ ] Minha Conta desktop (≥1024px): sidebar à esquerda + conteúdo à direita
- [ ] Header mobile aberto: fundo opaco a cobrir a página (não ver título «Minha Conta» por baixo); itens do menu WP + bloco Área do Aluno / Sair
- [ ] Header mobile: ícone Entrar / conta ao lado do carrinho; no hamburger, «Área do Aluno» (e «Sair» se logado) no fundo; scroll até ver todos os itens
- [ ] Minha Conta login: ícone olho dourado à direita da senha; toque mostra/oculta o texto
- [ ] Minha Conta senha errada: aviso em português **sem** repetir «N tentativa(s) restante(s)»
- [ ] Minha Conta logada: menu lateral sem linhas douradas por baixo dos ícones
- [ ] Minha Conta: avisos (senha temporária / erro) sem ícone sobreposto ao texto
- [ ] Minha Conta já logada: não mostrar aviso de bloqueio/tentativas de login
- [ ] https://saulocoelho.com/curso/leadership-academy/ sem login: porta de convite (sem “Ir para a loja”)
- [ ] Aluno matriculado na mesma URL: sala com Aulas / Avaliações / Materiais / Certificado
- [ ] https://saulocoelho.com/privacidade/ abre o aviso (template Legal); convite e rodapé apontam para essa URL

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

## Testes automatizados (futuro)

Ver `ROADMAP.md` — PHPUnit para funções puras; E2E opcional para checkout. Não implementado nesta etapa.

## Validação antes de Done

Somente o mantenedor move para **Done** após smoke test em staging (ou produção, se acordado).
