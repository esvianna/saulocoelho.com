# Rascunho de issue — MAPA no saulocoelho.com (vtis-quiz tipológico)

**Não criar no GitHub sem aprovação explícita.**  
Project: [esvianna/projects/6](https://github.com/users/esvianna/projects/6) · Repo plugin: `esvianna/vtis-quiz`  
Status sugerido ao criar: **Backlog** (spec) → **Ready** após aceite  
Decisões: **D35–D36** em `DECISIONS.md`

---

## Título sugerido

```
vtis-quiz: modo tipológico MAPA (60 itens, 16 tipos) no saulocoelho.com
```

## Corpo (copiar para a issue)

### Contexto

O **M.A.P.A.** (Método de Avaliação da Personalidade Ativa) é um inventário de personalidade usado no ecossistema HERO/SCCR, baseado em 4 dicotomias (estilo MBTI) → código de 4 letras → 16 perfis. Destino de produto: **saulocoelho.com** (plugin `vtis-quiz` + skin do tema).

Versão canónica: **2022** (`Teste MAPA.xlsx` / `Revisão do MAPA.xlsx` + PDF de perguntas + pasta `PERSONALIDADES` + capa modelo).

O motor actual do `vtis-quiz` (Likert multi-área, faixas, tips) serve ao *Código da Liderança*, **não** ao MAPA sem extensão tipológica (D36).

### Objectivo

1. Extender `vtis-quiz` com **scoring tipológico** (dicotomias bipolares, pontos com sinal, % clareza, `type_code`).
2. Seed / import do quiz **MAPA** (60 perguntas, 15×4 eixos).
3. Resultado front + e-mail: tipo em destaque + 4 barras de clareza + texto do perfil (16 tipos).
4. Publicar em **saulocoelho.com** (staging → prod) com shortcode/página e skin existente.

### Dentro do escopo (MVP tipológico)

- `scoring_mode` (ou equivalente) = typology no quiz.
- Escala Likert com pontos negativos/positivos; polaridade por item se necessário.
- Derivação E/I, N/S, T/F, J/P + percentagens (como na planilha 2022).
- Conteúdo dos 16 tipos (HTML a partir dos `.docx` PERSONALIDADES, com revisão editorial).
- Lead gate + relatório HTML (reutilizar `ReportService`).
- Seed `mapa` + página/shortcode no Saulo.

### Fora do escopo (MVP)

- PDF/capa no estilo `CAPA-MAPA-MODELO` (fase 2).
- Temperamento NT/ST/NF/SF (opcional fase 2).
- Afirmar/licenciar **MBTI®** oficial.
- Forced-choice versão 2018 (só referência histórica).

### Critérios de aceite

- [ ] Respostas de um caso da planilha 2022 produzem o **mesmo** `type_code` e letras/clareza (±1% arredondamento aceitável).
- [ ] Front: após lead, mostra tipo + 4 eixos + perfil do tipo (não só mapa de “áreas”).
- [ ] E-mail HTML inclui tipo + resumo; From = admin do site.
- [ ] Disclaimer: autoconhecimento / não diagnóstico clínico; método próprio (não MBTI oficial).
- [ ] Deploy staging + prod em saulocoelho.com; flush permalinks; skin OK.

### Como testar

1. Completar quiz MAPA com respostas conhecidas da planilha.
2. Comparar resultado com coluna «Resultado» / «Resultado Comp.» da Revisão 2022.
3. Reenviar e-mail pelo admin Leads.
4. Validar mobile + desktop na skin Saulo.

### Fontes

- `g:\Meu Drive\EDUARDOVIANNA.COM\SCCR\HERO\MAPA\` (Teste/Revisão, PDF, PERSONALIDADES, CAPA)
- Análise: canvas `mapa-vtis-quiz-analise`
