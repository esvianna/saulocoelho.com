# CHANGELOG.md

Formato baseado em [Keep a Changelog](https://keepachangelog.com/). Versões do tema em `style.css`.

## [Unreleased]

### Added
- **Convite LMS (handoff):** após cadastro em `/inscricao/{slug}/` o aluno entra na hora (cookie), vai à sala e vê como definir senha — sem depender do e-mail. `inc/module-lms-invite-handoff.php`. Tema **1.3.25** em produção (FTP). ADR-014 · [#17](https://github.com/esvianna/saulocoelho.com/pull/17).
- **Leads da palestra** (Teresópolis): página `/palestra/`, cadastro obrigatório (nome, e-mail, WhatsApp, LGPD), PDF tokenizado + e-mail, admin CSV e QR. Tabela `{prefix}sc_palestra_leads`. ADR-009.
- **CRUD de palestras** (`sc_palestra`): cada evento tem data, local, textos, campos extra, ficheiros privados e imagem de fundo. `/palestra/` continua a ser a palestra principal; outras em `/palestra/{slug}/`.
- E-mail da palestra: remetente `From`/`Reply-To` = e-mail de contato do tema (`contato@saulocoelho.com`), em vez do default `wordpress@`.
- Quiz **8 Códigos de Reação** no `vtis-quiz` **1.3.23**, publicado: https://saulocoelho.com/quiz/8-codigos-de-reacao/
- Issue [#12](https://github.com/esvianna/saulocoelho.com/issues/12): Avaliação Final Neuropsicanálise — **Done**; `vtis-quiz` **1.3.22** em prod (`/quiz/neuropsicanalise/`); D37.
- Documentação do Quiz marketing multi-cliente (plugin `vtis-quiz`): ADR-008, `docs/vtis-quiz-spec.md`; issue [#6](https://github.com/esvianna/saulocoelho.com/issues/6).
- Skin do quiz no tema (`inc/module-vtis-quiz-skin.php`) — CSS variables Playfair/`#C5A059` via hook `vtis_quiz_enqueue_assets`.
- Plano issue [#7](https://github.com/esvianna/saulocoelho.com/issues/7): `docs/PLAN-vtis-quiz-escalas-faixas-aberta.md` (A escalas, B faixas dimensão, C texto aberto).
- Rascunho issue MAPA tipológico (2022) no Saulo: `docs/vtis-quiz-mapa-issue-draft.md` (D35–D36).
- Issue [#9](https://github.com/esvianna/saulocoelho.com/issues/9): MAPA tipológico no Project (In progress); implementação no plugin `vtis-quiz` **1.3.0** (local).

### Changed
- Issue #6 implementada no plugin externo https://github.com/esvianna/vtis-quiz (v1.0.0).
- Issue #7: plugin `vtis-quiz` **v1.1.0** — A+B+C + seed `codigo-da-lideranca` (DB 3).
- Decisão produto: MAPA versão **2022** destino **saulocoelho.com** (extensão tipológica do `vtis-quiz`).

## [1.3.0] — 2026-06-15

### Added (issue #4)
- **CRUD** de formulários pós-inscrição: WooCommerce → Formulários pós-inscrição (`administrator`).
- Tabelas `{prefix}sc_forms`, `sc_form_sections`, `sc_form_fields`.
- Metabox no produto: vínculo opcional `sc_post_order_form_id` (online e presencial).
- Migração automática do schema `coaching-terapia-2026-07` para o banco + vínculo em produtos presenciais.
- Snapshot `form_snapshot_json` por inscrição (versão imutável das perguntas no envio).
- Aluno pode **editar** respostas após envio; CSV e painel com colunas dinâmicas por pergunta.

### Changed
- Runtime do questionário lê BD/snapshot em vez de `form-schema.php` fixo.
- Inscrições também para produtos **online** com formulário vinculado.
- Painel WooCommerce → **Inscrições** (antes «Inscrições presenciais»); presença só em produtos presenciais.
- Tema v1.2.0 → v1.3.0; `SC_PRESENCIAL_DB_VERSION` 2.0.0.

## [1.2.0] — 2026-06-15

### Added (issue #3)
- Módulo **inscrições presenciais**: gateway WooCommerce «Pagamento direto com o Saulo», questionário pós-pedido (22 campos), endpoint Minha Conta, painel admin (inscritos/pagos/presença), export CSV.
- Tabela `{prefix}sc_presencial_enrollments`; e-mails à equipe (pedido on-hold e questionário completo).

### Changed
- Tema v1.1.0 → v1.2.0.

## [1.1.0] — 2026-06-10

### Added
- Estrutura de governança técnica (issue #2): `AGENTS.md`, `PROJECT_STATUS.md`, `ROADMAP.md`, etc.
- Regras do Cursor em `.cursor/rules/`.
- Linhas decorativas douradas nos cantos (referência Carta Pública).

### Changed
- Nova identidade visual (issue #1): paleta navy `#050A14` + dourado `#C5A059`.
- Playfair Display nos títulos; Inter mantida no corpo e UI.
- Hero com títulos em CAPS; demais seções em title-case normal.
- Tema atualizado de v1.0.9 → v1.1.0.

### Removed
- Cor primária azul `#137fec` substituída em todo o tema.

Versão atual declarada em `wp-content/themes/saulocoelho/style.css`. Histórico anterior não documentado neste arquivo.
