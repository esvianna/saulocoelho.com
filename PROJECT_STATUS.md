# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-09-16

## Onde paramos

**Portal avisos / sininho / Web Push (issue [#16](https://github.com/esvianna/saulocoelho.com/issues/16)):** Fase A+B+C no tema **1.3.31** — **no ar** (FTP). **In review** — validar fila + e-mail + silêncio.

- Fase A: admin **Avisos Portal**, inbox + sininho
- Fase B: VAPID + subscriptions + Conta opt-in + SW push
- Fase C: tabela `sc_portal_push_jobs`, WP-Cron lotes, checkbox e-mail, silenciar curso, coluna Envio
- Tabelas: `sc_portal_notices`, `sc_portal_notice_reads`, `sc_portal_push_subs`, `sc_portal_push_jobs`
- Vendor: `wp-content/themes/saulocoelho/vendor/` (`minishlink/web-push`)

**Convite LMS — handoff (ADR-014 / [#17](https://github.com/esvianna/saulocoelho.com/pull/17)):** tema **1.3.25** — **no ar**. **Validado**.

**Portal do Aluno (issue [#15](https://github.com/esvianna/saulocoelho.com/issues/15)):** **Done**. Tema **1.3.27** no ar (ícone PWA = logo SC do site; fallbacks «P» removidos).

**Leadership Academy Noite 1 (issue [#13](https://github.com/esvianna/saulocoelho.com/issues/13)):** **Done**. **vtis-quiz 1.3.33** + Ama **1.0.31** — no ar; Conselho validado.

**Leadership Academy (fase 1):** convite Ama **1.0.31** + tema **1.3.25** (handoff). **No ar.**

- URL: https://saulocoelho.com/inscricao/leadership-academy/

**Palestra Teresópolis (segunda 14/09):** captura de leads **e CRUD de palestras no ar** em produção.

- URL: https://saulocoelho.com/palestra/ (Teresópolis; também `/palestra/teresopolis/`)
- Admin WP: **Palestras** (criar/editar) e **Palestras → Leads**
- PDF privado; download só após cadastro
- PWA `app.saulocoelho.com` **não** entra neste fluxo

Quiz **8 Códigos de Reação** no plugin **vtis-quiz 1.3.23**, publicado em produção.

- URL: https://saulocoelho.com/quiz/8-codigos-de-reacao/
- 32 afirmações, escala 0–5, mapa em média /5, entrega imediata (Saulo testa)
- `saulo.vtis.com.br` redireciona para produção; o deploy efetivo foi em `saulocoelho.com`

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

## Entrega #12 (resumo)

- 40 MC + gabarito; 7 dimensões; faixas 0–23 / 24–29 / 30–35 / 36–40
- Lead gate + `result_delivery` **deferred**; timer **90 min**
- Nota prática no admin + certificação **60/40** (mín. 7,5)
- Embaralhar alternativas (`shuffle_options`)
- Save no admin **preserva** gabarito MC (1.3.22)
- Leads: títulos, órfãos, relatório HTML

## Próximos passos sugeridos

1. Validar checklist #16 **Fase C** (fila + e-mail + silêncio + coluna Envio).
2. Após OK: marcar #16 Done.
3. Commit/push do tema 1.3.31 (+ `vendor/`) para o `main` quando quiseres alinhar o git.

## Como retomar

- Palestra: admin **Palestras** (eventos) e **Palestras → Leads**. `/palestra/` permanece Teresópolis. Checklist em `TESTING.md`.
- Quiz 8 Códigos: testar o fluxo completo (32 itens + lead + mapa /5) e ajustar copy/CTA no admin.
