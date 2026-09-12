# Acesso ao servidor — Saulocoelho.com (Confrarias)

**Última atualização:** 2026-09-10

## Autorização

> **Qualquer upload / alteração no servidor** exige autorização expressa na conversa.  
> Configuração local de FTP: `.vscode/sftp.json` (gitignored).

## Confrarias (FTP)

Fonte: FileZilla site **Confrarias** (máquina local).

| Campo | Valor |
|--------|--------|
| Host | `ftp.confrarias.com.br` |
| Protocolo | **FTP** (porta 21) — não SFTP/SSH |
| User | `admin@vtis.com.br` |
| Extensão Cursor | [SFTP](https://marketplace.visualstudio.com/items?itemName=Natizyskunk.sftp) (`natizyskunk.sftp`) |

### Paths remotos

| Ambiente | URL | Path FTP |
|----------|-----|----------|
| Staging | https://saulo.vtis.com.br | `/saulo.vtis.com.br` |
| Produção | https://saulocoelho.com | `/saulocoelho.com` |
| PWA OCD | https://app.saulocoelho.com | `/app.saulocoelho.com` |

Tema activo: `…/wp-content/themes/saulocoelho`  
Plugins: `…/wp-content/plugins/` (ex.: `vtis-quiz` ainda por criar no servidor)

## Config local (não commitada)

- Tema/repo: `saulocoelho.com/.vscode/sftp.json` — perfis **STAGING** e **PROD**
- Plugin: `vtis-quiz/.vscode/sftp.json` — upload para `wp-content/plugins/vtis-quiz`

`uploadOnSave` está **false**. Usar comando da extensão *SFTP: Upload* / *Sync* só após ok explícito.

## Como usar no Cursor

1. Extensão SFTP instalada.
2. Abrir o workspace `saulocoelho.code-workspace`.
3. Command Palette → `SFTP: List All` / `SFTP: Upload Folder` no perfil desejado.
4. Preferir **staging** antes de produção.

## Notas

- Conta FTP partilhada (vários sites no mesmo home Confrarias).
- Não há alias SSH `confrarias` nesta máquina (só FTP).
- Não colocar password em docs nem em commits.
