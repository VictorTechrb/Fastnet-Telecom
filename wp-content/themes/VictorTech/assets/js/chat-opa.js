(function () {
    'use strict';

    var STORAGE_KEY = 'fastnet_chat_state';
    var config = window.FastnetChatConfig || {};
    var launcher = document.querySelector('.widget-chat');
    var panel = document.querySelector('.fastnet-chat-panel');
    if (!launcher || !panel) return;

    var decision = panel.querySelector('[data-chat-decision]');
    var firstVisit = panel.querySelector('[data-chat-first]');
    var conversation = panel.querySelector('[data-chat-conversation]');
    var messagesNode = panel.querySelector('[data-chat-messages]');
    var form = panel.querySelector('[data-chat-form]');
    var input = panel.querySelector('[data-chat-input]');
    var recordButton = panel.querySelector('[data-chat-record]');
    var sendButton = panel.querySelector('[data-chat-send]');
    var audioSendButton = panel.querySelector('[data-chat-audio-send]');
    var recordingPanel = panel.querySelector('[data-chat-recording]');
    var recordingTime = panel.querySelector('[data-chat-recording-time]');
    var audioPauseButton = panel.querySelector('[data-chat-audio-pause]');
    var audioDiscardButton = panel.querySelector('[data-chat-audio-discard]');
    var recordStatus = panel.querySelector('[data-chat-record-status]');
    var fileInput = panel.querySelector('[data-chat-file]');
    var fileButton = panel.querySelector('[data-chat-file-button]');
    var photoInput = panel.querySelector('[data-chat-photo]');
    var photoButton = panel.querySelector('[data-chat-photo-button]');
    var plusButton = panel.querySelector('[data-chat-plus]');
    var plusMenu = panel.querySelector('[data-chat-plus-menu]');
    var locationButton = panel.querySelector('[data-chat-location]');
    var badge = launcher.querySelector('[data-chat-unread]');
    var maximizeButton = panel.querySelector('[data-chat-maximize]');
    var state = loadState();
    var directOpen = false;
    var opaSocket = null;
    var opaConnection = null;
    var mediaRecorder = null;
    var audioChunks = [];
    var audioProcessing = false;
    var recordingDiscarded = false;
    var recordingStartedAt = 0;
    var recordingTimer = null;
    var standaloneChat = new URLSearchParams(window.location.search).get('fastnet_chat') === 'standalone';

    function defaultState() {
        return {
            conversationId: 'fastnet-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8),
            status: 'idle',
            unreadCount: 0,
            messages: []
        };
    }

    function loadState() {
        try {
            var saved = JSON.parse(window.localStorage.getItem(STORAGE_KEY) || 'null');
            if (!saved || !Array.isArray(saved.messages)) return defaultState();
            return {
                conversationId: typeof saved.conversationId === 'string' && saved.conversationId ? saved.conversationId : defaultState().conversationId,
                status: saved.status === 'active' ? 'active' : 'idle',
                unreadCount: Number.isFinite(Number(saved.unreadCount)) ? Math.max(0, Number(saved.unreadCount)) : 0,
                messages: saved.messages.filter(function (message) {
                    return message && typeof message.text === 'string' && typeof message.sender === 'string' && typeof message.time === 'string';
                })
            };
        } catch (error) {
            return defaultState();
        }
    }

    function saveState() {
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (error) {
            // A blocked storage area must not prevent the atendimento from opening.
        }
        updateBadge();
    }

    function updateBadge() {
        badge.textContent = String(state.unreadCount);
        badge.hidden = state.unreadCount < 1;
    }

    function renderMessages() {
        messagesNode.innerHTML = '';
        state.messages.forEach(function (message) {
            var item = document.createElement('div');
            item.className = 'fastnet-chat-message fastnet-chat-message--' + (message.sender === 'user' ? 'user' : 'agent');
            if (message.text) item.appendChild(createMessageText(message.text));
            if (message.media && message.media.url) {
                var mediaBox = document.createElement('div');
                mediaBox.className = 'fastnet-chat-media';
                if (message.media.kind === 'image') {
                    var image = document.createElement('img');
                    image.src = message.media.url;
                    image.alt = message.media.name || 'Imagem recebida';
                    image.loading = 'lazy';
                    image.addEventListener('click', function () {
                        window.open(message.media.url, '_blank', 'noopener,noreferrer');
                    });
                    image.style.cursor = 'zoom-in';
                    mediaBox.appendChild(image);
                } else if (message.media.kind === 'audio') {
                    var audio = document.createElement('audio');
                    audio.src = message.media.url;
                    audio.controls = true;
                    audio.preload = 'metadata';
                    mediaBox.appendChild(audio);
                } else {
                    var link = document.createElement('a');
                    link.href = message.media.url;
                    link.target = '_blank';
                    link.rel = 'noopener noreferrer';
                    link.textContent = 'Abrir ' + (message.media.name || 'arquivo');
                    mediaBox.appendChild(link);
                }
                item.appendChild(mediaBox);
            }
            var time = document.createElement('time');
            time.textContent = message.time;
            item.appendChild(time);
            messagesNode.appendChild(item);
        });
        messagesNode.scrollTop = messagesNode.scrollHeight;
    }

    function createMessageText(value) {
        var paragraph = document.createElement('p');
        var source = String(value);
        if (source.indexOf('<') === -1) {
            paragraph.textContent = source;
            return paragraph;
        }

        var parsed = new DOMParser().parseFromString(source, 'text/html');
        function appendSafe(node, parent) {
            if (node.nodeType === Node.TEXT_NODE) {
                parent.appendChild(document.createTextNode(node.nodeValue));
                return;
            }
            if (node.nodeType !== Node.ELEMENT_NODE) return;
            var tag = node.tagName.toLowerCase();
            if (tag === 'img' || tag === 'svg' || tag === 'video' || tag === 'audio' || tag === 'iframe' || tag === 'object') {
                return;
            }
            if (tag === 'br') {
                parent.appendChild(document.createElement('br'));
                return;
            }
            if (tag === 'b' || tag === 'strong' || tag === 'i' || tag === 'em') {
                var formatted = document.createElement(tag);
                Array.prototype.forEach.call(node.childNodes, function (child) {
                    appendSafe(child, formatted);
                });
                parent.appendChild(formatted);
                return;
            }
            Array.prototype.forEach.call(node.childNodes, function (child) {
                appendSafe(child, parent);
            });
        }
        Array.prototype.forEach.call(parsed.body.childNodes, function (node) {
            appendSafe(node, paragraph);
        });
        return paragraph;
    }

    function showPanel(show) {
        panel.classList.toggle('is-hidden', !show);
        panel.classList.toggle('is-minimized', !show);
        panel.setAttribute('aria-hidden', show ? 'false' : 'true');
        launcher.setAttribute('aria-expanded', show ? 'true' : 'false');
    }

    function openStandaloneChat() {
        var url = new URL(window.location.href);
        url.searchParams.set('fastnet_chat', 'standalone');
        window.open(url.toString(), '_blank', 'noopener,noreferrer');
    }

    function showConversation() {
        firstVisit.hidden = true;
        decision.hidden = true;
        conversation.hidden = false;
        renderMessages();
        if (document.activeElement !== input) input.focus();
    }

    function showDecision() {
        firstVisit.hidden = true;
        decision.hidden = false;
        conversation.hidden = true;
    }

    function showFirstVisit() {
        firstVisit.hidden = false;
        decision.hidden = true;
        conversation.hidden = true;
    }

    function openChat(forceConversation) {
        directOpen = Boolean(forceConversation);
        showPanel(true);
        if (document.querySelector('.pagina-atendimento') && state.messages.length === 0) {
            showFirstVisit();
        } else if (!directOpen && state.messages.length > 0) {
            showDecision();
        } else {
            showConversation();
        }
        state.unreadCount = 0;
        saveState();
    }

    function minimizeChat() {
        showPanel(false);
        state.status = state.messages.length ? 'active' : 'idle';
        saveState();
    }

    function clearCookie(name) {
        document.cookie = name + '=; Max-Age=0; path=/';
    }

    function startNewConversation() {
        try { window.localStorage.removeItem(STORAGE_KEY); } catch (error) {}
        clearCookie('opa_user');
        if (opaSocket) opaSocket.disconnect();
        opaSocket = null;
        opaConnection = null;
        state = defaultState();
        saveState();
        showConversation();
    }

    function addMessage(text, sender, media) {
        var message = {
            text: text,
            sender: sender,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        };
        if (media) message.media = media;
        state.messages.push(message);
        state.status = 'active';
        saveState();
        renderMessages();
    }

    function addRemoteMessage(data, sender) {
        var text = data && typeof data.mensagem === 'string' ? data.mensagem : '';
        var media = getRemoteMedia(data);
        if (!text && !media) return;
        text = text || media.name || 'Arquivo recebido';
        var duplicate = state.messages.some(function (message) {
            return message.text === text && message.sender === sender && (!media || (message.media && message.media.url === media.url));
        });
        if (duplicate) return;
        addMessage(text, sender, media);
        if (sender === 'agent') notifyResponse(text);
    }

    function getRemoteMedia(data) {
        var object = data && data.objeto;
        if (!object) return null;
        var url = object.local && object.local.indexOf('https') === 0
            ? object.local
            : config.opaUrl.replace(/\/$/, '') + '/atendente/services/download/file/' + encodeURIComponent(object._id) + '?signature=' + encodeURIComponent(object.signature || '');
        var type = String(object.tipo || '');
        return {
            kind: type.indexOf('image/') === 0 ? 'image' : (type.indexOf('audio/') === 0 ? 'audio' : 'file'),
            name: object.nome || 'arquivo',
            url: url
        };
    }

    function connectOpa() {
        if (opaConnection) return opaConnection;
        opaConnection = new Promise(function (resolve, reject) {
            if (!config.opaUrl || !config.opaToken) {
                reject(new Error('Integração OPA não configurada.'));
                return;
            }
            function connect() {
                var query = 'opa_tipo=page&opa_token=' + encodeURIComponent(config.opaToken);
                var cookie = document.cookie.match(/(?:^|; )opa_user=([^;]*)/);
                if (cookie && cookie[1]) query += '&opa_user=' + encodeURIComponent(cookie[1]);
                opaSocket = window.io(config.opaUrl, { query: query, transports: ['websocket'] });
                opaSocket.on('opa_user', function (token) {
                    document.cookie = 'opa_user=' + encodeURIComponent(token) + '; Max-Age=31536000; path=/; SameSite=Lax';
                });
                opaSocket.on('Message', function (id, mode, data) {
                    if (mode === 'recebida' || (data && !data.id_user)) addRemoteMessage(data, 'agent');
                });
                opaSocket.on('Messages', function (messages) {
                    if (!Array.isArray(messages)) return;
                    messages.forEach(function (data) {
                        addRemoteMessage(data, data && data.id_user ? 'user' : 'agent');
                    });
                });
                opaSocket.on('connect', function () {
                    opaSocket.emit('get_mensagens_atendimento_web_chat');
                    resolve(opaSocket);
                });
                opaSocket.on('connect_error', reject);
            }
            if (window.io) {
                connect();
                return;
            }
            var script = document.createElement('script');
            script.src = config.opaUrl.replace(/\/$/, '') + '/socket.io/socket.io.js';
            script.async = true;
            script.onload = connect;
            script.onerror = function () { reject(new Error('Não foi possível conectar ao atendimento.')); };
            document.head.appendChild(script);
        });
        return opaConnection;
    }

    function notifyResponse(text) {
        var background = document.hidden || panel.classList.contains('is-hidden') || panel.classList.contains('is-minimized');
        if (!background) return;
        state.unreadCount += 1;
        saveState();
        try {
            var audio = new (window.AudioContext || window.webkitAudioContext)();
            var oscillator = audio.createOscillator();
            var gain = audio.createGain();
            oscillator.frequency.value = 660;
            gain.gain.setValueAtTime(0.04, audio.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audio.currentTime + 0.18);
            oscillator.connect(gain);
            gain.connect(audio.destination);
            oscillator.start();
            oscillator.stop(audio.currentTime + 0.18);
        } catch (error) {}
        if (window.Notification && Notification.permission === 'granted') {
            var notification = new Notification('Fastnet Telecom - Nova mensagem', { body: text });
            notification.onclick = function () {
                window.focus();
                openChat(true);
                notification.close();
            };
        }
    }

    function sendMessage(text) {
        addMessage(text, 'user');
        if (window.Notification && Notification.permission === 'default') Notification.requestPermission();
        connectOpa().then(function (socket) {
            socket.emit('mensagem', '', '', { tipo: 'texto', conteudo: text, dadosLogin: {} });
        }).catch(function () {
            opaConnection = null;
            addMessage('Não foi possível conectar ao atendimento agora. Tente novamente em instantes.', 'agent');
        });
    }

    function sendAudio(blob) {
        var reader = new FileReader();
        reader.onloadend = function () {
            var media = {
                kind: 'audio',
                name: 'mensagem-' + Date.now() + '.webm',
                url: reader.result
            };
            addMessage('', 'user', media);
            connectOpa().then(function (socket) {
                socket.emit('mensagem', '', '', {
                    tipo: 'midia',
                    conteudo: {
                        nomeArquivo: media.name,
                        data: reader.result
                    },
                    dadosLogin: {}
                });
            }).catch(function () {
                opaConnection = null;
                addMessage('Não foi possível enviar o áudio agora. Tente novamente.', 'agent');
            });
        };
        reader.readAsDataURL(blob);
    }

    function sendMedia(file) {
        if (file.size > 10 * 1024 * 1024) {
            setRecordingStatus('O arquivo deve ter no máximo 10 MB.');
            return;
        }
        setRecordingStatus('Enviando ' + file.name + '...');
        var reader = new FileReader();
        reader.onloadend = function () {
            var media = {
                kind: file.type.indexOf('image/') === 0 ? 'image' : (file.type.indexOf('audio/') === 0 ? 'audio' : 'file'),
                name: file.name,
                url: reader.result
            };
            addMessage('', 'user', media);
            connectOpa().then(function (socket) {
                socket.emit('mensagem', '', '', {
                    tipo: 'midia',
                    conteudo: { nomeArquivo: file.name, data: reader.result },
                    dadosLogin: {}
                });
                setRecordingStatus('');
            }).catch(function () {
                opaConnection = null;
                setRecordingStatus('Não foi possível enviar o arquivo agora.');
            });
        };
        reader.onerror = function () {
            setRecordingStatus('Não foi possível ler o arquivo.');
        };
        reader.readAsDataURL(file);
    }

    function sendLocation() {
        if (!navigator.geolocation) {
            setRecordingStatus('Seu navegador não permite enviar localização.');
            return;
        }
        setRecordingStatus('Obtendo sua localização...');
        navigator.geolocation.getCurrentPosition(function (position) {
            var location = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            };
            connectOpa().then(function (socket) {
                socket.emit('mensagem', '', '', {
                    tipo: 'localizacao',
                    conteudo: location,
                    dadosLogin: {}
                });
                addMessage('Localização enviada', 'user');
                setRecordingStatus('');
            }).catch(function () {
                opaConnection = null;
                setRecordingStatus('Não foi possível enviar sua localização.');
            });
        }, function () {
            setRecordingStatus('Permita o acesso à localização para enviar.');
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    function setRecordingStatus(text) {
        recordStatus.textContent = text;
    }

    function updateComposer() {
        var hasText = input.value.trim().length > 0;
        var recording = audioProcessing || (mediaRecorder && mediaRecorder.state === 'recording');
        recordButton.hidden = hasText || recording;
        sendButton.hidden = !hasText || recording;
        audioSendButton.hidden = true;
        input.disabled = Boolean(recording);
        plusButton.disabled = Boolean(recording);
        photoButton.disabled = Boolean(recording);
        if (recording) plusMenu.hidden = true;
    }

    function updateRecordingTimer() {
        if (!recordingStartedAt) return;
        var elapsed = Math.floor((Date.now() - recordingStartedAt) / 1000);
        var minutes = String(Math.floor(elapsed / 60)).padStart(2, '0');
        var seconds = String(elapsed % 60).padStart(2, '0');
        recordingTime.textContent = minutes + ':' + seconds;
    }

    function finishRecording(discard) {
        if (!mediaRecorder) return;
        recordingDiscarded = Boolean(discard);
        if (mediaRecorder.state !== 'inactive') mediaRecorder.stop();
        audioProcessing = true;
        if (recordingTimer) window.clearInterval(recordingTimer);
        recordingTimer = null;
        audioPauseButton.disabled = true;
        audioDiscardButton.disabled = true;
        audioSendButton.disabled = true;
        setRecordingStatus(discard ? 'Excluindo áudio...' : 'Processando áudio...');
    }

    function toggleRecording() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.pause();
            audioPauseButton.textContent = '▶';
            audioPauseButton.setAttribute('aria-label', 'Retomar gravação');
            setRecordingStatus('Gravação pausada');
            return;
        }
        if (mediaRecorder && mediaRecorder.state === 'paused') {
            mediaRecorder.resume();
            audioPauseButton.textContent = 'Ⅱ';
            audioPauseButton.setAttribute('aria-label', 'Pausar gravação');
            setRecordingStatus('Gravando...');
            return;
        }
        if (!navigator.mediaDevices || !window.MediaRecorder) {
            setRecordingStatus('Seu navegador não permite gravação de áudio.');
            return;
        }
        navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
            audioChunks = [];
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.ondataavailable = function (event) {
                if (event.data.size > 0) audioChunks.push(event.data);
            };
            mediaRecorder.onstop = function () {
                stream.getTracks().forEach(function (track) { track.stop(); });
                var audio = new Blob(audioChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
                if (!recordingDiscarded) sendAudio(audio);
                setRecordingStatus('');
                mediaRecorder = null;
                audioProcessing = false;
                recordingDiscarded = false;
                recordingStartedAt = 0;
                recordingTime.textContent = '00:00';
                recordingPanel.hidden = true;
                audioPauseButton.disabled = false;
                audioDiscardButton.disabled = false;
                audioSendButton.disabled = false;
                updateComposer();
            };
            mediaRecorder.start();
            recordingStartedAt = Date.now();
            recordingTimer = window.setInterval(updateRecordingTimer, 1000);
            recordingPanel.hidden = false;
            recordButton.setAttribute('aria-pressed', 'true');
            recordButton.setAttribute('aria-label', 'Gravar áudio');
            setRecordingStatus('Gravando... clique novamente para enviar');
            updateComposer();
        }).catch(function () {
            audioProcessing = false;
            updateComposer();
            setRecordingStatus('Permita o acesso ao microfone para enviar áudio.');
        });
    }

    launcher.addEventListener('click', function () { openChat(false); });
    maximizeButton.addEventListener('click', openStandaloneChat);
    recordButton.addEventListener('click', toggleRecording);
    audioSendButton.addEventListener('click', function () {
        finishRecording(false);
    });
    audioPauseButton.addEventListener('click', toggleRecording);
    audioDiscardButton.addEventListener('click', function () { finishRecording(true); });
    plusButton.addEventListener('click', function () {
        var expanded = plusButton.getAttribute('aria-expanded') === 'true';
        plusButton.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        plusMenu.hidden = expanded;
    });
    fileButton.addEventListener('click', function () {
        plusMenu.hidden = true;
        plusButton.setAttribute('aria-expanded', 'false');
        fileInput.click();
    });
    fileInput.addEventListener('change', function () {
        if (fileInput.files[0]) sendMedia(fileInput.files[0]);
        fileInput.value = '';
    });
    photoButton.addEventListener('click', function () { photoInput.click(); });
    photoInput.addEventListener('change', function () {
        if (photoInput.files[0]) sendMedia(photoInput.files[0]);
        photoInput.value = '';
    });
    locationButton.addEventListener('click', sendLocation);
    input.addEventListener('input', updateComposer);
    panel.querySelector('[data-chat-minimize]').addEventListener('click', minimizeChat);
    panel.querySelector('[data-chat-continue]').addEventListener('click', function () { showConversation(); });
    panel.querySelector('[data-chat-new]').addEventListener('click', startNewConversation);
    panel.querySelector('[data-chat-start]').addEventListener('click', showConversation);
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var text = input.value.trim();
        if (!text) return;
        input.value = '';
        sendMessage(text);
        updateComposer();
    });
    updateBadge();
    updateComposer();

    if (document.querySelector('.pagina-atendimento')) {
        openChat(false);
    }

    if (standaloneChat) {
        document.documentElement.classList.add('fastnet-chat-standalone');
        openChat(true);
        panel.classList.add('is-maximized');
        maximizeButton.setAttribute('aria-pressed', 'true');
        maximizeButton.setAttribute('aria-label', 'Atendimento aberto em página exclusiva');
        maximizeButton.textContent = '❐';
    }

    var opaButton = document.querySelector('.opa_ico');
    if (opaButton) {
        opaButton.setAttribute('aria-hidden', 'true');
        opaButton.setAttribute('tabindex', '-1');
    }
}());
