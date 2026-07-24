# Plano — Escalas, faixas por dimensão e pergunta aberta (vtis-quiz)

**Data:** 2026-07-24  
**Issue:** (a criar no Project Saulo #6)  
**Plugin:** https://github.com/esvianna/vtis-quiz  
**Motivação:** modelo real [AVALIAÇÃO - O CODIGO DA LIDERANÇA](../docs/AVALIAÇÃO%20-%20O%20CODIGO%20DA%20LIDERANÇA.docx) (escala 1–5, 8 áreas, faixas globais + por área, pergunta aberta).

**Status deste plano:** implementado (issue #7) — plugin v1.1.0 / DB 3.

---

## Objectivo

Tornar o plugin flexível para quizzes distintos (ex. Bylevel 3 opções 0–2 **e** Código da Liderança Likert 1–5), sem hardcode de escala, com classificação por dimensão e campo de texto livre opcional.

---

## Escopo proposto (3 entregas encadeadas)

### Entrega A — Escalas de resposta reutilizáveis (prioridade 1)

**Problema:** save do quiz força sempre 3 opções (0/1/2).

**Proposta:**
1. Tabela `vtis_quiz_scales` (ou CPT/option) — id, title, slug, `options_json` `[{label, points}, …]`.
2. Admin: CRUD de escalas + seeds (`likert-3-0a2`, `likert-5-1a5`).
3. No quiz: campo `scale_id` (ou `default_scale_id` em settings) aplicado a todas as perguntas do quiz na gravação.
4. Admin do quiz: escolher escala; opcionalmente sobrescrever opções por pergunta (fase 1.1 se necessário).
5. Front: renderiza N opções dinamicamente (já quase pronto via `options` no payload).

**Aceite A:**
- Criar escala 1–5 e associar a um quiz.
- Front mostra 5 botões com pontos 1–5.
- Total/máximo calculados correctamente (ex. 40×5 = 200).

### Entrega B — Faixas de resultado por dimensão (prioridade 2)

**Problema:** só existem faixas no **total** global.

**Proposta:**
1. Estender `vtis_quiz_result_bands` com `scope` = `global` | `dimension` + `dimension_key` (nullable).
2. Admin: secção “Faixas por área” (min/max/título/corpo + dimensão).
3. No submit: além da faixa global, calcular faixa por cada dimensão presente no score.
4. Front (mapa/score): mostrar rótulo da faixa por barra (ex. “Área crítica”) e/ou ecrã intermédio.

**Aceite B:**
- Quiz com 8 dimensões e faixas 5–10 / 11–15 / 16–20 / 21–25 por área.
- Resultado devolve `dimensions[].band` + `band` global.

### Entrega C — Pergunta aberta (prioridade 3)

**Problema:** só existem perguntas de escala pontuada.

**Proposta:**
1. `questions.question_type` = `scale` | `text` (default `scale`).
2. Perguntas `text` têm **pontos fixos** configuráveis no admin (default **0**).
   - Se a resposta tiver texto não vazio → soma os pontos fixos ao total (e ao máximo, se > 0).
   - Se vazia → contribui **0**.
   - Caso típico «Código da Liderança»: pontos = 0 (só qualitativo para devolutiva).
3. Resposta guardada em `answers_json` (ex. `{ "qid": { "type":"text", "value":"...", "points": 0 } }`).
4. Admin leads/CSV: coluna/campo para textos abertos.
5. Front: após última escala (ou posição na ordem), ecrã textarea + continuar → lead gate (**default:** aberta **antes** do lead).

**Aceite C:**
- Uma pergunta aberta no fim; com pontos = 0 o score das Likert não muda; com pontos = N e resposta preenchida, o total reflecte N; texto visível no detalhe do lead.

---

## Fora de escopo (esta issue)

- Geração de PDF / áudio de devolutiva (fase futura).
- Condicionais entre perguntas.
- Integração CRM/Woo.
- Importação automática do .docx (conteúdo continua manual no admin ou seed SQL).

---

## Ordem de implementação sugerida

1. **A** (desbloqueia o modelo Liderança)  
2. **B** (classificação por área do doc)  
3. **C** (pergunta final aberta)  
4. Seed opcional `codigo-da-lideranca` (conteúdo do doc) — só após A+B+C aprovados e copy validado.

Versão alvo: **1.1.0** (feature release).

---

## Riscos

- Quizzes já publicados: migração deve preservar `options_json` actual; criar escala “legado 0-2” e associar.
- Faixas sobrepostas / buracos: validação admin (aviso) sem bloquear save na v1.
- UX front com muitas opções (5): manter layout de lista vertical actual.

---

## Como testar (após Ready)

1. Criar escala Likert 1–5; quiz de 2 dimensões × 2 perguntas; faixas globais e por área.
2. Completar fluxo + lead; verificar total, max, bands.
3. Adicionar pergunta aberta; confirmar CSV/detalhe.
4. Regressão: quiz seed `avaliacao-exemplo` (escala 0–2) continua a funcionar.

---

## Decisão pedida ao aprovador

- Aprovar entregas **A+B+C** nesta issue (recomendado), ou só **A** primeiro?
- Seed do “Código da Liderança” nesta issue ou issue seguinte?
