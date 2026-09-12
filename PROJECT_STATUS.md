# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-09-12

## Onde paramos

**Palestra Teresópolis (segunda 14/09):** captura de leads **e CRUD de palestras no ar** em produção.

- URL: https://saulocoelho.com/palestra/ (Teresópolis; também `/palestra/teresopolis/`)
- Admin WP: **Palestras** (criar/editar) e **Palestras → Leads**
- PDF privado; download só após cadastro
- PWA `app.saulocoelho.com` **não** entra neste fluxo

Quiz **8 Códigos de Reação** no plugin **vtis-quiz 1.3.23**, publicado em produção.

- URL: https://saulocoelho.com/quiz/8-codigos-de-reacao/
- 32 afirmações, escala 0–5, mapa em média /5, entrega imediata (Saulo testa)
- `saulo.vtis.com.br` redireciona para produção; o deploy efetivo foi em `saulocoelho.com`

Issue GitHub: não criada (sem pedido). Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

## Entrega #12 (resumo)

- 40 MC + gabarito; 7 dimensões; faixas 0–23 / 24–29 / 30–35 / 36–40
- Lead gate + `result_delivery` **deferred**; timer **90 min**
- Nota prática no admin + certificação **60/40** (mín. 7,5)
- Embaralhar alternativas (`shuffle_options`)
- Save no admin **preserva** gabarito MC (1.3.22)
- Leads: títulos, órfãos, relatório HTML

## Próximos passos sugeridos

1. QR/link da palestra: https://saulocoelho.com/palestra/ — conferir e-mail do PDF e CSV no admin **Palestras → Leads**.
2. CTA URL da mentoria quando existir (campo no admin do quiz).
3. Quando for uso só de mentorado: Entrega diferida no admin.
4. Push do repo `vtis-quiz` se pedido.

## Como retomar

- Palestra: admin **Palestras** (eventos) e **Palestras → Leads**. `/palestra/` permanece Teresópolis. Checklist em `TESTING.md`.
- Quiz 8 Códigos: testar o fluxo completo (32 itens + lead + mapa /5) e ajustar copy/CTA no admin.
