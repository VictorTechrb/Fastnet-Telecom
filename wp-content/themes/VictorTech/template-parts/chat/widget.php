<?php
/**
 * Interface do widget de atendimento Fastnet.
 */
?>
<button class="widget-chat" type="button" aria-controls="fastnet-chat-panel" aria-expanded="false" aria-label="<?php esc_attr_e('Abrir conversa com a Central de Atendimento', 'victortech'); ?>">
    <span class="widget-chat__icon" aria-hidden="true">?</span>
    <span class="widget-chat__label"><?php esc_html_e('Falar com atendimento', 'victortech'); ?></span>
    <span class="widget-chat__badge" data-chat-unread hidden>0</span>
</button>
<section class="fastnet-chat-panel is-hidden" id="fastnet-chat-panel" aria-labelledby="fastnet-chat-title" aria-hidden="true">
    <header class="fastnet-chat-panel__header">
        <div>
            <span class="fastnet-chat-panel__eyebrow">Fastnet Telecom</span>
            <h2 id="fastnet-chat-title">Central de atendimento</h2>
        </div>
        <div class="fastnet-chat-panel__controls">
            <button class="fastnet-chat-panel__maximize" type="button" data-chat-maximize aria-label="Abrir atendimento em página exclusiva" aria-pressed="false">□</button>
            <button class="fastnet-chat-panel__close" type="button" data-chat-minimize aria-label="Minimizar atendimento">×</button>
        </div>
    </header>
    <div class="fastnet-chat-panel__body">
        <div class="fastnet-chat-first" data-chat-first hidden>
            <strong>Fale com a Fastnet</strong>
            <p>Inicie seu atendimento com a nossa equipe.</p>
            <button class="fastnet-chat-action" type="button" data-chat-start>Iniciar atendimento</button>
        </div>
        <div class="fastnet-chat-decision" data-chat-decision hidden>
            <strong>Você tem um atendimento anterior</strong>
            <p>Deseja continuar de onde parou ou iniciar uma nova conversa?</p>
            <button class="fastnet-chat-action" type="button" data-chat-continue>Continuar atendimento</button>
            <button class="fastnet-chat-action fastnet-chat-action--secondary" type="button" data-chat-new>Iniciar nova conversa</button>
        </div>
        <div class="fastnet-chat-conversation" data-chat-conversation hidden>
            <div class="fastnet-chat-messages" data-chat-messages aria-live="polite"></div>
            <form class="fastnet-chat-form" data-chat-form>
                <label class="screen-reader-text" for="fastnet-chat-input">Mensagem</label>
                <input type="file" data-chat-file accept=".pdf,.doc,.docx,.xls,.xlsx,.txt" hidden>
                <input type="file" data-chat-photo accept="image/jpeg,image/png,image/gif,image/webp" hidden>
                <div class="fastnet-chat-plus-wrap">
                    <button class="fastnet-chat-tool fastnet-chat-plus" type="button" data-chat-plus aria-expanded="false" aria-label="Mais opções">+</button>
                    <div class="fastnet-chat-plus-menu" data-chat-plus-menu hidden>
                        <button type="button" data-chat-file-button>📄 <span>Documento</span></button>
                        <button type="button" data-chat-location>📍 <span>Enviar localização</span></button>
                    </div>
                </div>
                <textarea id="fastnet-chat-input" data-chat-input rows="2" maxlength="2000" placeholder="Digite sua mensagem..." required></textarea>
                <button class="fastnet-chat-tool fastnet-chat-photo-button" type="button" data-chat-photo-button aria-label="Enviar foto">📷</button>
                <button class="fastnet-chat-record" type="button" data-chat-record aria-pressed="false" aria-label="Gravar áudio">
                    <svg class="fastnet-chat-mic" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 15.5a3.5 3.5 0 0 0 3.5-3.5V6a3.5 3.5 0 0 0-7 0v6a3.5 3.5 0 0 0 3.5 3.5Zm-2-9.5a2 2 0 0 1 4 0v6a2 2 0 0 1-4 0V6Zm8 6a6 6 0 0 1-12 0H4a8 8 0 0 0 7 7.94V21H8v1h8v-1h-3v-1.06A8 8 0 0 0 20 12h-2Z"/></svg>
                </button>
                <button class="fastnet-chat-send" type="submit" data-chat-send aria-label="Enviar mensagem" hidden>➤</button>
            </form>
            <div class="fastnet-chat-recording" data-chat-recording hidden>
                <span class="fastnet-chat-recording__time" data-chat-recording-time>00:00</span>
                <span class="fastnet-chat-recording__wave" aria-hidden="true">||||||||||||||||||||||||||||||||</span>
                <button type="button" class="fastnet-chat-recording__discard" data-chat-audio-discard aria-label="Excluir gravação">🗑</button>
                <button type="button" class="fastnet-chat-recording__pause" data-chat-audio-pause aria-label="Pausar gravação">Ⅱ</button>
                <button type="button" class="fastnet-chat-audio-send" data-chat-audio-send aria-label="Enviar áudio">➤</button>
            </div>
            <p class="fastnet-chat-record-status" data-chat-record-status aria-live="polite"></p>
        </div>
        <div class="fastnet-chat-fallback" data-chat-fallback hidden>
            <strong>Atendimento temporariamente indisponível.</strong>
            <p>Utilize nosso canal alternativo para falar com a equipe.</p>
            <a class="fastnet-chat-web-link" data-chat-fallback-link href="#" target="_blank" rel="noopener noreferrer">Abrir canal alternativo</a>
        </div>
    </div>
</section>
