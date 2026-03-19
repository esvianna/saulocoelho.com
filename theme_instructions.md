# Instruções de Desenvolvimento - Projeto Saulo Coelho

Este arquivo contém informações críticas que devem ser lidas no início de cada sessão de desenvolvimento.

## 1. Ambiente de Homologação
- **URL**: `https://saulo.vtis.com.br`
- **Nota**: Sempre que realizar alterações nos arquivos locais (Meu Drive), lembre ao usuário de sincronizar com o servidor e limpar o cache do WordPress/CDN para ver as mudanças.

## 2. Uso Obrigatório de Skills
- **Mandato**: No início de cada tarefa complexa, o assistente DEVE listar as skills disponíveis e verificar se alguma delas (`wordpress_theme_developer`, `premium_ux_ui_patterns`, `skill_validator`, etc.) é aplicável.
- **Prioridade**: As diretrizes das skills instaladas têm prioridade sobre padrões genéricos de codificação.

## 3. Padrões do Projeto
- **Core**: WordPress (PHP).
- **Estética**: Premium, Dark Mode, High Contrast (Skill: `premium_ux_ui_patterns`).
- **Styling**: Tailwind CSS (Vanilla CSS para ajustes finos).
- **Metaboxes**: Todo conteúdo editável deve passar por Meta Boxes customizadas (Arquivo: `inc/metaboxes.php`).

---
*Assinado: Antigravity Assistant*
