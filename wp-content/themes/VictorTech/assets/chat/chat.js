(function () {
'use strict';
var config=window.FastnetChatConfig||{},launcher=document.querySelector('.widget-chat'),panel=document.querySelector('.fastnet-chat-panel');
if(!launcher||!panel||!config.restUrl){return;}
var storageKey='fastnet_chat_session_v2',session=null,messages=[],lastMessageId=0,unread=0,pollTimer=null,pollDelay=4000,isOpen=false,isSending=false,initializing=false,ready=false,pendingOpen=false,polling=false,recorder=null,stream=null,chunks=[],recordTimer=null,recordStart=0,discard=false;
var decision=panel.querySelector('[data-chat-decision]'),first=panel.querySelector('[data-chat-first]'),conversation=panel.querySelector('[data-chat-conversation]'),fallback=panel.querySelector('[data-chat-fallback]'),fallbackLink=panel.querySelector('[data-chat-fallback-link]'),messagesNode=panel.querySelector('[data-chat-messages]'),form=panel.querySelector('[data-chat-form]'),input=panel.querySelector('[data-chat-input]'),recordButton=panel.querySelector('[data-chat-record]'),sendButton=panel.querySelector('[data-chat-send]'),audioSend=panel.querySelector('[data-chat-audio-send]'),recordingPanel=panel.querySelector('[data-chat-recording]'),recordingTime=panel.querySelector('[data-chat-recording-time]'),pauseButton=panel.querySelector('[data-chat-audio-pause]'),discardButton=panel.querySelector('[data-chat-audio-discard]'),statusNode=panel.querySelector('[data-chat-record-status]'),fileInput=panel.querySelector('[data-chat-file]'),fileButton=panel.querySelector('[data-chat-file-button]'),photoInput=panel.querySelector('[data-chat-photo]'),photoButton=panel.querySelector('[data-chat-photo-button]'),plusButton=panel.querySelector('[data-chat-plus]'),plusMenu=panel.querySelector('[data-chat-plus-menu]'),locationButton=panel.querySelector('[data-chat-location]'),badge=launcher.querySelector('[data-chat-unread]'),maximizeButton=panel.querySelector('[data-chat-maximize]');
function status(value){statusNode.textContent=value||'';}
function setUnread(value){unread=Math.max(0,Number(value)||0);badge.textContent=String(unread);badge.hidden=unread<1;}
function saveSession(){try{if(session){localStorage.setItem(storageKey,JSON.stringify(session));}else{localStorage.removeItem(storageKey);}}catch(e){status('O armazenamento do navegador está indisponível. Mantenha esta aba aberta durante o atendimento.');}}
function loadSession(){try{var saved=JSON.parse(localStorage.getItem(storageKey)||'null');if(saved&&typeof saved.session_id==='string'&&typeof saved.token==='string'){session=saved;}}catch(e){session=null;}}
function api(url,options){var opts=options||{};opts.credentials='same-origin';opts.headers=opts.headers||{};opts.headers.Accept='application/json';return fetch(url,opts).then(function(response){return response.text().then(function(raw){var body={};try{body=raw?JSON.parse(raw):{};}catch(e){}if(!response.ok){var error=new Error(body.message||body.error||'Não foi possível concluir a operação.');error.status=response.status;throw error;}return body;});});}
function sessionUrl(){return config.restUrl.replace(/\/$/,'')+'/session/'+encodeURIComponent(session.session_id);}
function headers(){return {'Content-Type':'application/json','X-Fastnet-Chat-Token':session.token};}
function createSession(){status('Preparando seu atendimento...');return api(config.restUrl.replace(/\/$/,'')+'/session',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'}).then(function(result){if(!result.session_id||!result.token){throw new Error('O servidor não retornou uma sessão válida.');}session={session_id:result.session_id,token:result.token};messages=[];lastMessageId=0;setUnread(0);saveSession();status('');return session;});}
function formatTime(value){if(!value){return '';}var date=new Date(String(value).replace(' ','T')+'Z');return Number.isNaN(date.getTime())?'':date.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}
function loadMediaSource(message,node,attribute){
function applySource(source){if(attribute==='href'){node.href=source;}else{node.src=source;}}
if(!message.media_private){applySource(message.media_url);return;}
if(message._mediaDataUrl){applySource(message._mediaDataUrl);return;}
if(!message.media_url||!session){return;}
api(message.media_url,{method:'GET',headers:{'X-Fastnet-Chat-Token':session.token}}).then(function(result){
if(typeof result.data_url!=='string'||result.data_url.indexOf('data:')!==0){throw new Error('Resposta de anexo inválida.');}
message._mediaDataUrl=result.data_url;
applySource(message._mediaDataUrl);
}).catch(function(){
node.classList.add('is-unavailable');
if(attribute==='href'){node.textContent='Não foi possível carregar o anexo';}
});
}
function renderMessage(message){var item=document.createElement('div');item.className='fastnet-chat-message fastnet-chat-message--'+(message.direction==='visitor'?'user':'agent');if(message.text){var p=document.createElement('p');p.textContent=message.text;item.appendChild(p);}if(message.media_url){var box=document.createElement('div');box.className='fastnet-chat-media';if(String(message.media_type||'').indexOf('image/')===0){var image=document.createElement('img');loadMediaSource(message,image,'src');image.alt=message.media_name||'Imagem recebida';image.loading='lazy';box.appendChild(image);}else if(String(message.media_type||'').indexOf('audio/')===0){var audio=document.createElement('audio');loadMediaSource(message,audio,'src');audio.controls=true;audio.preload='metadata';box.appendChild(audio);}else{var link=document.createElement('a');loadMediaSource(message,link,'href');link.download=message.media_name||'arquivo';link.target='_blank';link.rel='noopener noreferrer';link.textContent='Abrir '+(message.media_name||'arquivo');box.appendChild(link);}item.appendChild(box);}else if(message.type==='media'&&message.media_name){var label=document.createElement('p');label.textContent='Arquivo enviado: '+message.media_name;item.appendChild(label);}var time=document.createElement('time');time.textContent=formatTime(message.created_at);item.appendChild(time);return item;}
function renderMessages(){messagesNode.replaceChildren();messages.forEach(function(message){messagesNode.appendChild(renderMessage(message));});messagesNode.scrollTop=messagesNode.scrollHeight;}
function appendMessages(incoming){if(!Array.isArray(incoming)){return;}incoming.forEach(function(message){var id=Number(message&&message.id);if(!message||!Number.isFinite(id)||id<=lastMessageId){return;}messages.push(message);lastMessageId=id;if(message.direction==='agent'&&!isOpen){setUnread(unread+1);}});renderMessages();}
function fetchMessages(initial){if(!session||polling){return Promise.resolve();}polling=true;var after=initial?0:lastMessageId;return api(sessionUrl()+'/messages?after_id='+encodeURIComponent(after),{method:'GET',headers:{'X-Fastnet-Chat-Token':session.token}}).then(function(result){appendMessages(result.messages||[]);if(isOpen&&!conversation.hidden){setUnread(0);}return result;}).finally(function(){polling=false;});}
function stopPolling(){if(pollTimer){clearTimeout(pollTimer);}pollTimer=null;}
function schedulePolling(delay){stopPolling();pollTimer=setTimeout(function(){fetchMessages(false).then(function(){pollDelay=document.hidden?8000:4000;}).catch(function(){pollDelay=Math.min(Math.max(pollDelay*2,8000),30000);}).finally(function(){schedulePolling(pollDelay);});},delay||pollDelay);}
function showPanel(show){isOpen=Boolean(show);panel.classList.toggle('is-hidden',!isOpen);panel.classList.toggle('is-minimized',!isOpen);panel.setAttribute('aria-hidden',isOpen?'false':'true');launcher.setAttribute('aria-expanded',isOpen?'true':'false');}
function showFallback(){first.hidden=true;decision.hidden=true;conversation.hidden=true;fallback.hidden=false;fallbackLink.hidden=!config.fallbackUrl;if(config.fallbackUrl){fallbackLink.href=config.fallbackUrl;}status('');}
function showConversation(){first.hidden=true;decision.hidden=true;fallback.hidden=true;conversation.hidden=false;renderMessages();setUnread(0);status('');if(document.activeElement!==input){input.focus();}}
function showDecision(){first.hidden=true;decision.hidden=false;conversation.hidden=true;fallback.hidden=true;}
function showFirst(){first.hidden=false;decision.hidden=true;conversation.hidden=true;fallback.hidden=true;}
function openChat(force){showPanel(true);if(!ready){pendingOpen=true;showFirst();status('Preparando seu atendimento...');initialize();return;}if(!session){showFallback();return;}if(!force&&messages.length){showDecision();}else if(!messages.length){showFirst();}else{showConversation();}fetchMessages(false).catch(function(){status('Não foi possível atualizar as mensagens. Verifique sua conexão.');});}
function minimize(){showPanel(false);}
function standalone(){var url=new URL(location.href);url.searchParams.set('fastnet_chat','standalone');window.open(url.toString(),'_blank','noopener,noreferrer');}
function newConversation(){stopPolling();createSession().then(function(){showConversation();schedulePolling(500);}).catch(function(e){status(e.message||'Não foi possível iniciar uma nova conversa.');});}
function updateComposer(){var hasText=input.value.trim().length>0,recording=Boolean(recorder&&recorder.state!=='inactive');recordButton.hidden=hasText||recording;sendButton.hidden=!hasText||recording;input.disabled=recording||isSending;plusButton.disabled=recording||isSending;photoButton.disabled=recording||isSending;if(recording||isSending){plusMenu.hidden=true;}}
function sendPayload(message){if(!session||isSending){return Promise.reject(new Error('A sessão de atendimento não está disponível.'));}isSending=true;updateComposer();status('Enviando mensagem...');return api(sessionUrl()+'/messages',{method:'POST',headers:headers(),body:JSON.stringify({message:message})}).then(function(result){status(result.history_warning?'Mensagem recebida pelo atendimento. O histórico será atualizado em seguida.':'');return fetchMessages(false);}).catch(function(e){status(e.message||'Não foi possível enviar a mensagem. Tente novamente.');throw e;}).finally(function(){isSending=false;updateComposer();});}
function sendText(value){var text=String(value||'').trim();return text?sendPayload({type:'text',text:text}):Promise.resolve();}
function fileMime(file){if(file.type){return file.type;}var ext=String(file.name||'').split('.').pop().toLowerCase();var map={pdf:'application/pdf',txt:'text/plain',doc:'application/msword',docx:'application/vnd.openxmlformats-officedocument.wordprocessingml.document',xls:'application/vnd.ms-excel',xlsx:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',jpg:'image/jpeg',jpeg:'image/jpeg',png:'image/png',gif:'image/gif',webp:'image/webp',webm:'audio/webm',ogg:'audio/ogg',mp3:'audio/mpeg',mp4:'audio/mp4',wav:'audio/wav'};return map[ext]||'';}
function sendFile(file){if(!file){return Promise.resolve();}var max=Number(config.maxUploadBytes)||(2*1024*1024);if(file.size<1||file.size>max){status('O arquivo deve ter conteúdo e respeitar o limite de '+(max/(1024*1024)).toFixed(1)+' MB.');return Promise.resolve();}var mime=fileMime(file);if(!mime){status('Este formato de arquivo não é permitido.');return Promise.resolve();}status('Preparando arquivo...');return new Promise(function(resolve,reject){var reader=new FileReader();reader.onload=function(){var result=String(reader.result||''),comma=result.indexOf(',');if(comma<0){reject(new Error('Não foi possível ler o arquivo.'));return;}sendPayload({type:'media',mediaType:mime,filename:file.name,data:result.slice(comma+1)}).then(resolve).catch(reject);};reader.onerror=function(){reject(new Error('Não foi possível ler o arquivo.'));};reader.readAsDataURL(file);}).catch(function(e){status(e.message||'Não foi possível enviar o arquivo.');});}
function sendLocation(){if(!navigator.geolocation){status('Seu navegador não permite enviar localização.');return;}status('Obtendo sua localização...');navigator.geolocation.getCurrentPosition(function(position){var coords=position.coords.latitude+','+position.coords.longitude,url='https://www.google.com/maps?q='+encodeURIComponent(coords);sendText('Minha localização: '+url).catch(function(){});},function(){status('Não foi possível obter sua localização. Verifique a permissão do navegador.');},{enableHighAccuracy:true,timeout:10000});}
function updateTimer(){if(!recordStart){return;}var elapsed=Math.floor((Date.now()-recordStart)/1000);recordingTime.textContent=String(Math.floor(elapsed/60)).padStart(2,'0')+':'+String(elapsed%60).padStart(2,'0');}
function cleanupRecorder(){if(recordTimer){clearInterval(recordTimer);}recordTimer=null;if(stream){stream.getTracks().forEach(function(track){track.stop();});}stream=null;recorder=null;chunks=[];recordStart=0;recordingPanel.hidden=true;recordButton.setAttribute('aria-pressed','false');pauseButton.disabled=false;discardButton.disabled=false;audioSend.disabled=false;pauseButton.textContent='Ⅱ';recordingTime.textContent='00:00';updateComposer();}
function stopRecording(shouldDiscard){if(!recorder||recorder.state==='inactive'){return;}discard=Boolean(shouldDiscard);if(recordTimer){clearInterval(recordTimer);}recordTimer=null;pauseButton.disabled=true;discardButton.disabled=true;audioSend.disabled=true;status(discard?'Descartando áudio...':'Processando áudio...');recorder.stop();}
function startRecording(){if(!navigator.mediaDevices||!window.MediaRecorder){status('Seu navegador não permite gravação de áudio.');return;}navigator.mediaDevices.getUserMedia({audio:true}).then(function(mediaStream){stream=mediaStream;chunks=[];discard=false;var options={};if(MediaRecorder.isTypeSupported&&MediaRecorder.isTypeSupported('audio/webm')){options.mimeType='audio/webm';}recorder=new MediaRecorder(mediaStream,options);recorder.ondataavailable=function(event){if(event.data&&event.data.size){chunks.push(event.data);}};recorder.onstop=function(){var blob=new Blob(chunks,{type:recorder.mimeType||'audio/webm'}),send=!discard&&blob.size>0;cleanupRecorder();if(send){sendFile(new File([blob],'mensagem-'+Date.now()+'.webm',{type:blob.type||'audio/webm'}));}else{status('');}};recorder.start();recordStart=Date.now();recordingPanel.hidden=false;recordButton.setAttribute('aria-pressed','true');status('Gravando áudio...');recordTimer=setInterval(updateTimer,1000);updateComposer();}).catch(function(){status('Permita o acesso ao microfone para enviar áudio.');});}
launcher.addEventListener('click',function(){openChat(false);});
maximizeButton.addEventListener('click',standalone);
panel.querySelector('[data-chat-minimize]').addEventListener('click',minimize);
panel.querySelector('[data-chat-continue]').addEventListener('click',function(){showConversation();fetchMessages(false).catch(function(){});schedulePolling(500);});
panel.querySelector('[data-chat-new]').addEventListener('click',newConversation);
panel.querySelector('[data-chat-start]').addEventListener('click',function(){showConversation();schedulePolling(500);});
form.addEventListener('submit',function(event){event.preventDefault();var text=input.value.trim();if(!text||isSending){return;}sendText(text).then(function(){input.value='';}).catch(function(){}).finally(updateComposer);});
input.addEventListener('input',updateComposer);
recordButton.addEventListener('click',startRecording);
audioSend.addEventListener('click',function(){stopRecording(false);});
pauseButton.addEventListener('click',function(){if(!recorder){return;}if(recorder.state==='recording'){recorder.pause();pauseButton.textContent='▶';pauseButton.setAttribute('aria-label','Retomar gravação');status('Gravação pausada.');}else if(recorder.state==='paused'){recorder.resume();pauseButton.textContent='Ⅱ';pauseButton.setAttribute('aria-label','Pausar gravação');status('Gravando áudio...');}});
discardButton.addEventListener('click',function(){stopRecording(true);});
plusButton.addEventListener('click',function(){var expanded=plusButton.getAttribute('aria-expanded')==='true';plusButton.setAttribute('aria-expanded',expanded?'false':'true');plusMenu.hidden=expanded;});
fileButton.addEventListener('click',function(){plusMenu.hidden=true;plusButton.setAttribute('aria-expanded','false');fileInput.click();});
fileInput.addEventListener('change',function(){if(fileInput.files&&fileInput.files[0]){sendFile(fileInput.files[0]);}fileInput.value='';});
photoButton.addEventListener('click',function(){photoInput.click();});
photoInput.addEventListener('change',function(){if(photoInput.files&&photoInput.files[0]){sendFile(photoInput.files[0]);}photoInput.value='';});
locationButton.addEventListener('click',function(){plusMenu.hidden=true;plusButton.setAttribute('aria-expanded','false');sendLocation();});
function initialize(){
if(ready||initializing){return;}
initializing=true;
loadSession();
fallbackLink.hidden=!config.fallbackUrl;
if(config.fallbackUrl){fallbackLink.href=config.fallbackUrl;}
var result;
if(session){
result=fetchMessages(true).catch(function(error){
if(error.status===401||error.status===403||error.status===404){session=null;saveSession();return createSession();}
throw error;
});
}else{
result=createSession();
}
result.then(function(){
initializing=false;
ready=true;
var isStandalone=new URLSearchParams(location.search).get('fastnet_chat')==='standalone';
if(document.querySelector('.pagina-atendimento')||isStandalone||pendingOpen){
pendingOpen=false;
openChat(true);
}
if(isStandalone){
document.documentElement.classList.add('fastnet-chat-standalone');
panel.classList.add('is-maximized');
maximizeButton.setAttribute('aria-pressed','true');
maximizeButton.textContent='❐';
}
if(session){schedulePolling(4000);}
}).catch(function(error){
initializing=false;
showFallback();
status(error.message||'O atendimento está temporariamente indisponível.');
});
}
document.addEventListener('visibilitychange',function(){pollDelay=document.hidden?8000:4000;if(!document.hidden){schedulePolling(500);}});
updateComposer();setUnread(0);if(document.querySelector('.pagina-atendimento')||new URLSearchParams(location.search).get('fastnet_chat')==='standalone'){openChat(true);}
}());
