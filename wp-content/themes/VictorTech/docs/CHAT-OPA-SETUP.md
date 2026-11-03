# Atendimento Fastnet — Webchat oficial do Opa! Suite

## Integração escolhida

Este tema utiliza o **script oficial de instalação do Webchat Opa! Suite**. O próprio OPA renderiza a janela de conversa, gerencia a sessão e encaminha as mensagens para a equipe.

Esta opção foi escolhida para configurar o chat com somente dois dados no WordPress:

1. URL da instalação OPA.
2. Token/identificador do canal usado pelo script de instalação do Webchat.

**Não é o modo API para aplicativo próprio.** O modo API exige ID do canal, Token de autenticação do Opa! e Token de segurança do webhook como dados distintos, além da URL base. Não reutilize um único valor para essas credenciais.

## Configuração no WordPress

Acesse **Configurações → Atendimento Fastnet** e preencha:

- URL do Opa! Suite: `https://lara.fastnettelecom.com`
- Token / ID do canal: o segundo argumento de `opa.init()` no script oficial gerado pelo OPA. No script fornecido pela Fastnet, é o identificador iniciado por `6ad1`.

O campo aceita um valor novo; se for deixado vazio, o valor salvo anteriormente é mantido.

O tema carrega o script:

```text
https://lara.fastnettelecom.com/js/opa.js
```

E inicializa o Webchat com as opções de login anônimo que vieram no script original:

```javascript
opa.init(URL, TOKEN_DO_CANAL, JSON.stringify({
  permitir_login_anonimo: "on",
  facebook_appid: "",
  google_credential: "",
  google_oauth: ""
}));
```

## Importante sobre o identificador

O valor colocado no campo Token / ID do canal é o identificador usado pelo Webchat incorporado. Ele é enviado ao navegador pelo próprio script do OPA, como acontece no código de instalação original. Portanto, não coloque nesse campo um token privado de usuário ou o Token de autenticação da API OPA.

## O que não é necessário nesta modalidade

- Não é necessário ativar **Integrar aplicativo próprio / modo API**.
- Não é necessário preencher Token de autenticação da API.
- Não é necessário cadastrar webhook do WordPress.
- Não são necessários os endpoints REST, banco de sessões ou polling do chat personalizado anterior.

O widget oficial aparece nas páginas públicas do site e também na página /atendimento/.

## Se quiser manter uma interface personalizada

Para manter uma interface própria da Fastnet e fazer o WordPress conversar por trás com a API, é necessário usar o modo API do OPA. Nessa modalidade, a documentação exige credenciais separadas (ID do canal, token Bearer de autenticação e token de segurança do cabeçalho X-Signature). Não é tecnicamente correto prometer uma integração personalizada bidirecional usando apenas a URL e o identificador público do script de instalação.

## Referências

- Documentação de instalação do Webchat: https://central-opasuite.ixcsoft.com.br/documentacao/opa%21-store/canais-de-comunicacao/webchat
- Integração de aplicativo próprio / modo API: https://central-opasuite.ixcsoft.com.br/documentacao/opa%21-store/canais-de-comunicacao/webchat-modo-api
