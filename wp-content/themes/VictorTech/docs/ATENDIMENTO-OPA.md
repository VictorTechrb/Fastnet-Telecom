# Atendimento online Fastnet Telecom

## Objetivo

O tema utiliza o Webchat oficial do Opa! Suite para oferecer atendimento online aos visitantes do site da Fastnet Telecom. O OPA controla a janela de conversa, a sessão e o encaminhamento das mensagens.

A personalização do tema altera o botão de abertura do atendimento e a apresentação da página `/atendimento/`. O conteúdo interno da conversa continua sob responsabilidade do Webchat oficial.

## Configuração no WordPress

Acesse **Configurações → Atendimento Fastnet** e informe:

- **Endereço do Opa! Suite:** `https://lara.fastnettelecom.com`
- **Identificador do canal de atendimento:** segundo parâmetro da chamada `opa.init()` no código oficial de instalação do Webchat.

Deixe o campo do identificador vazio para manter o valor que já está salvo.

## Como o Webchat é carregado

O tema carrega o arquivo oficial:

```text
https://lara.fastnettelecom.com/js/opa.js
```

Depois, inicializa o Webchat com as opções fornecidas no código de instalação da Fastnet. Os nomes das opções são mantidos exatamente como o OPA exige.

## Arquivos do atendimento

- `inc/atendimento/configuracao.php`: valida e recupera as configurações.
- `inc/atendimento/inicializacao.php`: inicia o Webchat e administra o título e a descrição da página de atendimento.
- `assets/atendimento/identidade-visual.css`: identidade visual do botão e da página de atendimento.
- `assets/atendimento/identidade-visual.js`: aplica os atributos de acessibilidade ao botão criado pelo OPA.
- `page-atendimento.php` (nome convencional dos modelos de página do WordPress): conteúdo visível da página de atendimento.

## Compatibilidade e segurança

As configurações já gravadas no WordPress são preservadas. A leitura também aceita os nomes de campos e variáveis de ambiente utilizados pelas versões anteriores.

O identificador do Webchat incorporado não deve ser confundido com uma senha de usuário ou com o token Bearer do modo API. Esta integração utiliza o script oficial de instalação, não o modo API para aplicativo próprio.

## SEO e conteúdo

A página de atendimento apresenta conteúdo visível em português, com foco natural em:

- Fastnet Telecom;
- atendimento online em Sergipe;
- internet fibra óptica;
- suporte técnico de internet;
- planos de internet residencial e empresarial.

O título e a descrição são definidos pelo tema somente quando uma extensão de SEO não está ativa. A página não lista municípios ou endereços como áreas atendidas sem confirmação da disponibilidade comercial.

## Arquivos antigos removidos

Foram removidos os módulos da antiga interface própria e do modo API que não eram mais carregados: telas, scripts, estilos, rotas REST, serviços e rotinas de banco de dados. Esta limpeza não exclui tabelas nem dados existentes no banco.

## Documentação do fornecedor

- Instalação do Webchat: https://central-opasuite.ixcsoft.com.br/documentacao/opa%21-store/canais-de-comunicacao/webchat
- Integração pelo modo API: https://central-opasuite.ixcsoft.com.br/documentacao/opa%21-store/canais-de-comunicacao/webchat-modo-api
