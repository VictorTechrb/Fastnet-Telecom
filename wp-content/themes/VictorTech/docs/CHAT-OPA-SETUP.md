# Atendimento Fastnet — integração Opa! Suite (modo API)

## Estrutura

- inc/chat/: configuração privada, banco de dados, serviço OPA e endpoints REST.
- assets/chat/: CSS e JavaScript exclusivos do chat.
- template-parts/chat/: estrutura visual do widget.
- docs/CHAT-OPA-SETUP.md: este guia.

O navegador chama somente a API REST do WordPress. O servidor WordPress chama o Opa! Suite e recebe as respostas por webhook. Nenhum token do Opa! Suite é enviado ao JavaScript.

## Configuração da integração Opa! Suite

Documentação oficial: https://central-opasuite.ixcsoft.com.br/documentacao/opa%21-store/canais-de-comunicacao/webchat-modo-api

1. Acesse Opa! Store → Webchat → Acessar e abra o registro do canal.
2. Em Integrar aplicativo próprio, ative o modo API.
3. Em API de envio de mensagens, informe a URL HTTPS do endpoint WordPress:

   https://SEU-SITE/wp-json/fastnet-chat/v1/webhook

4. Salve e copie a API de recebimento de mensagens, o Token de autenticação do Opa! e o Token de segurança usado no cabeçalho X-Signature.
5. Anote o ID do canal exibido no endereço do registro do Webchat.

O endpoint usado para envio é {API_BASE}/ID-DO-CANAL/message. O servidor envia Authorization: Bearer TOKEN e confere o campo success do JSON, não apenas o status HTTP. A OPA documenta erros de validação que podem retornar HTTP 200.

## Segredos do servidor

Não coloque credenciais no repositório, no tema, no JavaScript ou no wp-config.php enquanto esse arquivo estiver versionado no Git.

Recomendado: configure as variáveis no ambiente do PHP (PHP-FPM/hosting):

- FASTNET_OPA_API_URL: URL base mostrada em API de recebimento, sem ID do canal e sem /message.
- FASTNET_OPA_CHANNEL_ID: ID do canal.
- FASTNET_OPA_TOKEN: Token de autenticação do Opa!.
- FASTNET_OPA_WEBHOOK_SECRET: Token de segurança usado em X-Signature.

Alternativa: defina FASTNET_CHAT_PRIVATE_CONFIG apontando para um arquivo PHP fora do repositório e da pasta pública. Esse arquivo deve retornar um array com as chaves api_url, channel_id, token e webhook_secret:

    <?php
    return array(
        'api_url' => 'https://SEU-DOMINIO/api/v1/webchat',
        'channel_id' => 'ID-DO-CANAL',
        'token' => 'TOKEN-DE-AUTENTICACAO',
        'webhook_secret' => 'TOKEN-DE-SEGURANCA',
    );

Proteja o arquivo com permissões restritas e não o publique.

## Fluxo e recursos

- Sessão aleatória por visitante e token de sessão de alta entropia; o banco guarda apenas o hash do token.
- Mensagens enviadas ao Opa! Suite exclusivamente pelo servidor WordPress.
- Webhook com validação de X-Signature e deduplicação por ID remoto da mensagem.
- Histórico persistido em tabelas próprias do WordPress.
- Retomada da conversa no mesmo navegador e opção de iniciar nova conversa.
- Atualização do painel por consulta à API REST local; o WordPress não consulta o Opa!, que envia respostas por webhook.
- Texto, anexos, fotos, áudio e localização enviada como link do Google Maps.
- Limite de anexo local: até 10 MB, respeitando o limite do PHP/WordPress.
- Limpeza automática de sessões sem atividade por 90 dias.

## Publicação segura

1. Faça backup dos arquivos e do banco de dados.
2. Configure o modo API no canal Opa! Suite e as credenciais no ambiente do servidor.
3. Publique esta branch primeiro em ambiente de homologação.
4. Verifique se a API REST do WordPress está disponível.
5. Teste criação de sessão, envio de texto e resposta do atendente.
6. Teste imagem, PDF, documento, áudio, localização, retomada e nova conversa.
7. Só depois do teste real aprove a integração em produção.

O modo API precisa estar ativo no canal Opa! Suite. Sem essa configuração, o chat exibirá o canal alternativo definido no Personalizador do WordPress.

## Segurança operacional

- O token antigo do chat foi publicado no histórico do Git. Revogue/regere esse token no Opa! Suite; remover o arquivo atual não apaga o segredo dos commits antigos.
- Faça o mesmo com qualquer credencial real que tenha sido publicada no wp-config.php.
- Se desativar e reativar Integrar aplicativo próprio no Opa! Suite, os tokens mudam e as variáveis do servidor precisam ser atualizadas.
- O sucesso HTTP da OPA significa que a mensagem foi aceita para processamento, não que um atendente já a visualizou.
- O histórico antigo mantido apenas no localStorage pelo chat Socket.IO não é migrado automaticamente para a nova sessão.

## Limitações conhecidas

A integração em modo API documentada pela OPA aceita texto e mídia. A localização é compartilhada como link de mapa dentro de uma mensagem de texto. Recursos exclusivos do protocolo Socket.IO antigo, como eventos de digitação, não fazem parte deste fluxo.
