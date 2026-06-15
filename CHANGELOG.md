# CHANGELOG.md

Formato baseado em [Keep a Changelog](https://keepachangelog.com/). Versões do tema em `style.css`.

## [Unreleased]

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
