# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-09-16

## Onde paramos

**Convite LMS — handoff sem e-mail (ADR-014 / [#17](https://github.com/esvianna/saulocoelho.com/pull/17) merged):** tema **1.3.25** — **no ar** (FTP 2026-09-16). **Validado**. No `main` GitHub (squash merge 2026-09-16).

**Portal do Aluno (issue [#15](https://github.com/esvianna/saulocoelho.com/issues/15)):** tema **1.3.24→1.3.25** — **no ar**. **Validado**. Código a entrar no `main` via sync git (este PR).

- App-shell só com utilizador logado em Minha Conta **e** no curso/player LMS (`/curso/…`)
- Tabs: Cursos · Certificados · Conta; hero oculto; KPIs em **3 colunas** (ícone + número + rótulo)
- Hub do curso: cards Aulas / Avaliações / Materiais em **linha (3 colunas)** também no mobile
- Player: só «Voltar ao curso» (oculto «Catálogo de cursos» em PWA/mobile/desktop)
- Sem saudação Woo «não é …? Sair» no fim do dashboard (Sair na tab Conta)
- PWA: ícone = **ícone do site** (Customizer); CTA instalar **acima das boas-vindas** + tab Conta; oculto se `standalone`; copy pt-BR
- Header: **Área do Aluno** (logado)
- Separado do OCD (`app.saulocoelho.com`)
- Deploy FTP 2026-09-15 (até **1.3.24**)
- **Próximo:** avisos + sininho + Web Push — [#16](https://github.com/esvianna/saulocoelho.com/issues/16) (**Backlog**) · ADR-013

**Leadership Academy Noite 1 (issue [#13](https://github.com/esvianna/saulocoelho.com/issues/13)):** **vtis-quiz 1.3.33** + **AmaEducacional 1.0.31** + tema **1.3.23**. **No ar.**

- Grade **Noite 1** com os **6 exercícios** na ordem da apostila
- **[#14](https://github.com/esvianna/saulocoelho.com/issues/14):** formulário completo (rever/editar antes de enviar) nos 6 exercícios
- Tipo **`blanks`**: Espelho com «Quando… eu tendo a…» e «custa…» em lacunas (frase montada na síntese)
- **Uma resposta por aluno** + opção Refazer (setting do quiz + política do curso no Ama)
- **Conselho (pedido Saulo 15/09):** 5 respostas + guias Passado/Presente/Futuro — deploy FTP 2026-09-15 (`1.3.33` / DB 22); **validado** no player
- Tema 1.3.12+: menu mobile com drawer fora do header (`sticky`/`backdrop-blur`); validado no browser (itens Programas/Agenda/Blog/Contato + Entrar)

**Leadership Academy (fase 1):** convite no AmaEducacional **1.0.31** + tema **1.3.25**. **No ar.**

- URL convite: https://saulocoelho.com/inscricao/leadership-academy/
- Fix 1.0.30: e-mail já cliente → redireciona para Minha Conta (`redirect_to` do curso)
- Handoff 1.3.25: conta nova → login imediato + sala (sem depender do e-mail)
- **Aviso de privacidade (modelo):** https://saulocoelho.com/privacidade/ — editar em Páginas no WP; completar CNPJ/DPO com a equipe
- Admin do curso: metabox Convite → copiar link/`?t=` e baixar QR
- Ocultar do catálogo público; não ligar “gratuito com conta”

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

1. ~~Handoff / Conselho / Portal~~ — validados 2026-09-16.
2. ~~PR [#17](https://github.com/esvianna/saulocoelho.com/pull/17)~~ — **merged** (ADR-014 no `main`).
3. ~~Alinhar `main` com o tema real~~ — PR [#18](https://github.com/esvianna/saulocoelho.com/pull/18) **merged** — `main` alinhado ao tema 1.3.25 de produção.
4. Quando priorizar: [#16](https://github.com/esvianna/saulocoelho.com/issues/16) → **Ready** (sininho).
5. Opcional: marcar [#15](https://github.com/esvianna/saulocoelho.com/issues/15) / [#13](https://github.com/esvianna/saulocoelho.com/issues/13) como **Done**.

## Como retomar

- Palestra: admin **Palestras** (eventos) e **Palestras → Leads**. `/palestra/` permanece Teresópolis. Checklist em `TESTING.md`.
- Quiz 8 Códigos: testar o fluxo completo (32 itens + lead + mapa /5) e ajustar copy/CTA no admin.
