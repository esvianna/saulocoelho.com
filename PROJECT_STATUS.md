# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-08-02

## Onde paramos

Issue **[#9](https://github.com/esvianna/saulocoelho.com/issues/9)** MAPA tipológico — plugin **v1.3.0** commit `62d69a7` **deployado** em staging + produção. Aguarda validação humana → **In Review**.

Issues quiz **#6–#8** continuam em **In Review**.

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

## Pós-deploy (fazer no WP)

1. Reactivar / visitar plugins (upgrade DB→4 + seed `mapa` se ainda não existir).
2. **Flush permalinks**.
3. Criar página com `[vtis_quiz slug="mapa"]` ou abrir `/quiz/mapa/`.
4. Completar um caso da planilha 2022 e comparar tipo/%; testar e-mail.

## Próximos passos

1. Validar MAPA em staging/prod → mover #9 para Done quando OK.
2. (Fase 2) PDF/capa CAPA-MAPA-MODELO.

## Riscos

- Polaridade dos 60 itens inferida semanticamente — validar vs planilha.
- Perfis 16 tipos: revisão editorial/direitos.
- Não marcar como MBTI® oficial.

## Como retomar

- "Validei o MAPA — mover #9 para Done" ou "Ajustar polaridade da pergunta X"
