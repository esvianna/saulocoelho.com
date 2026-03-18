# Instruções de Instalação do Tema "saulocoelho"

O WordPress está relatando que o tema está incompleto porque a estrutura de pastas do projeto contém o tema em uma subpasta.

### Como Instalar Corretamente:

Para que o WordPress reconheça o tema, você deve usar **apenas** o conteúdo da pasta:
`wp-content/themes/saulocoelho`

#### Opção 1: Via Painel do WordPress (Upload de ZIP)
1. Entre na pasta `wp-content/themes/`.
2. Compacte (ZIP) apenas a pasta `saulocoelho`.
3. No WordPress, vá em **Aparência > Temas > Adicionar Novo > Enviar Tema** e selecione o arquivo `saulocoelho.zip`.

#### Opção 2: Via FTP ou Gerenciador de Arquivos
1. Copie a pasta `saulocoelho` (que está em `wp-content/themes/` neste projeto).
2. Cole-a dentro da pasta `wp-content/themes/` da sua instalação do WordPress.

### Por que o erro ocorreu?
O erro "Folha de estilos em falta" acontece porque o WordPress procura o arquivo `style.css` na raiz da pasta que você instalou. Se você instalou a pasta raiz do projeto (`saulocoelho.com`), o WordPress não encontrou o arquivo lá, pois ele está escondido dentro de subpastas.

---
**Nota:** O arquivo `style.css` também foi recriado para garantir que não existam caracteres de codificação (BOM) que possam causar erros de leitura no WordPress.
