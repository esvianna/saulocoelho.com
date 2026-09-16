# Rascunho de issue — Portal do Aluno: sininho + avisos (+ Web Push)

**Issue criada:** [#16](https://github.com/esvianna/saulocoelho.com/issues/16) · Project #6 · **Backlog**  
Decisão: **ADR-013** em `DECISIONS.md`  
Só implementar após mover a **Ready** (Fase A) ou com aprovação explícita.

---

## Título sugerido

```
Portal do Aluno: sininho de avisos + Web Push (PWA)
```

## Corpo (copiar para a issue)

### Contexto

O Portal do Aluno (tema ≥ 1.3.24, ADR-011) já é PWA com service worker em `/portal-aluno/sw.js` (scope `/`). A equipa Saulo precisa de **avisar alunos** (turma Leadership Academy, lembretes, links para aulas) com:

1. **Sininho** na topbar (lista + badge) — funciona sempre logado.
2. **Web Push** (opcional) — notificação do SO quando a app está fechada.

iOS só recebe push de forma fiável com PWA no ecrã inicial (16.4+). Por isso o **inbox é obrigatório** e o push é canal secundário sobre o mesmo aviso.

Separado do PWA OCD (`app.saulocoelho.com`).

### Objectivo

- Admin cria avisos e o aluno vê no Portal (e, se subscrito, recebe push).
- Segmentação v1: todos os logados do Portal / matriculados num curso Ama / um utilizador.
- Entrega em fases (A → B → C) sem bloquear a turma no inbox.

### Dentro do escopo

#### Fase A — Inbox + sininho (MVP)

- Tabelas: avisos, leituras; REST autenticada (listar, marcar lida, contagem).
- Admin WP: criar aviso (título, corpo curto, URL, audience).
- Topbar Portal: ícone sininho + painel (últimos N) + badge.
- Permissão: só `manage_options` (ou role a definir) cria avisos.

#### Fase B — Web Push

- VAPID (public no front; private só servidor).
- Registo de subscription por dispositivo; opt-in na tab Conta (não no 1.º segundo).
- SW: `push` + `notificationclick` → URL do aviso.
- Admin: checkbox «Enviar push agora» ao publicar.

#### Fase C (depois)

- Fila/cron em lotes; relatório enviados/falhas; silenciar curso; e-mail opcional.

### Fora do escopo (MVP)

- Chat, threads, anexos grandes, rich HTML no push.
- Integração com OCD / Firebase obrigatório.
- Notificações automáticas de progresso LMS (pode ser fase futura via hook).

### Critérios de aceite (Fase A)

- [ ] Admin cria aviso para curso Leadership Academy; aluno matriculado vê no sininho.
- [ ] Aluno não matriculado / audience errada não vê o aviso.
- [ ] Abrir aviso marca como lido; badge actualiza.
- [ ] Visitante / páginas de marketing: sem sininho.
- [ ] Sem regressão do shell Portal / PWA install.

### Critérios de aceite (Fase B)

- [ ] Android (Chrome PWA ou browser elegível): opt-in → recebe push → clique abre URL correta.
- [ ] iOS: com PWA instalada + permissão, recebe push; sem PWA, só inbox.
- [ ] Subscription inválida (410) é removida.
- [ ] Quem negou permissão continua a ver o sininho.

### Como testar

1. Fase A: criar aviso de teste → login aluno → badge → abrir → lido.
2. Fase B: Conta → activar notificações → enviar push → fechar app → receber → toque.
3. Confirmar que `app.saulocoelho.com` não é afectado.

### Riscos

- Hosting FTP/Confrarias: timeouts em mass push → lote via cron (Fase C).
- SW scope `/`: cuidado para não cachear HTML de conta; push handlers só no SW do Portal.
- LGPD: opt-in explícito; textos claros na Conta.

### Notas técnicas

- Spec: ADR-013.
- Preferência: backend plugin/módulo WP; UI no tema (`template-parts/portal-aluno/`).
- Audience `course:{id}` usa matrículas AmaEducacional quando o plugin estiver activo.

---

## Checklist ao criar no GitHub

- [x] Colar corpo acima → [#16](https://github.com/esvianna/saulocoelho.com/issues/16)
- [x] Project #6 → **Backlog**
- [x] Label: `enhancement` (portal/pwa não existiam no repo)
- [x] Referenciar ADR-013
- [ ] Só mover a **Ready** a Fase A (ou issue filha) após OK do mantenedor
