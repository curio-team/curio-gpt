import { marked } from 'marked';
import hljs from 'highlight.js';
import 'highlight.js/styles/atom-one-dark.min.css';

// ─── Markdown Renderer ────────────────────────────────────────────────────────

const renderer = {
    code({ text, lang }) {
        const validLang = lang && hljs.getLanguage(lang) ? lang : 'plaintext';
        const highlighted = hljs.highlight(text, { language: validLang }).value;

        return `<div class="chat-code-block">
            <div class="chat-code-header">
                <span class="chat-code-lang">${lang || 'text'}</span>
                <button class="copy-code-btn" data-code="${encodeURIComponent(text)}">${COPY_ICON} Copy</button>
            </div>
            <pre class="chat-code-pre"><code class="hljs language-${validLang}">${highlighted}</code></pre>
        </div>`;
    },
};

marked.use({ renderer, breaks: true, gfm: true });

// ─── Icons ────────────────────────────────────────────────────────────────────

const COPY_ICON = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>`;
const CHECK_ICON = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>`;
const SEND_ICON = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>`;
const EDIT_ICON = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`;

// ─── Utilities ────────────────────────────────────────────────────────────────

function escapeHtml(text) {
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);

    if (parts.length === 2) {
        return decodeURIComponent(parts.pop().split(';').shift());
    }

    return null;
}

async function ensureCsrfCookie() {
    if (!getCookie('XSRF-TOKEN')) {
        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
    }
}

function flashCopied(btn) {
    const orig = btn.innerHTML;
    btn.innerHTML = CHECK_ICON + ' Copied!';
    btn.disabled = true;
    setTimeout(() => {
        btn.innerHTML = orig;
        btn.disabled = false;
    }, 2000);
}

// ─── Chat App ─────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('chat-form');

    if (!form) {
        return;
    }

    const promptEl = document.getElementById('prompt');
    const messagesEl = document.getElementById('messages');
    const statusEl = document.getElementById('status');
    const sendBtn = document.getElementById('send-btn');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const chatContainer = document.querySelector('[data-agent-config-id]');
    const selectedAgentConfigId = chatContainer?.dataset.agentConfigId ?? null;
    let conversationId = null;
    let isStreaming = false;

    /**
     * Tracks all rendered messages in order: [{type, text, el}]
     * Indices here correspond directly to DB row positions, enabling
     * position-based branching when a message is edited.
     */
    let chatMessages = [];

    /**
     * When non-null, the user is editing a previous message.
     * @type {{ branchFromConversationId: string, keepMessageCount: number, removed: Array<{type: string, text: string, el: Element}> } | null}
     */
    let pendingEdit = null;

    // ─ Send icon in submit button ─────────────────────────────────────────────
    if (sendBtn) {
        sendBtn.innerHTML = SEND_ICON + ' Send';
    }

    // ─ Auto-resize textarea ───────────────────────────────────────────────────
    function autoResize() {
        promptEl.style.height = 'auto';
        promptEl.style.height = Math.min(promptEl.scrollHeight, 192) + 'px';
    }

    promptEl.addEventListener('input', autoResize);

    // ─ Enter to send, Shift+Enter for newline ─────────────────────────────────
    promptEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();

            if (!isStreaming) {
                form.requestSubmit();
            }
        }

        if (e.key === 'Escape' && pendingEdit) {
            cancelEdit();
        }
    });

    // ─ Copy / edit buttons (event delegation) ─────────────────────────────────
    messagesEl.addEventListener('click', (e) => {
        const codeBtn = e.target.closest('.copy-code-btn');

        if (codeBtn) {
            const code = decodeURIComponent(codeBtn.dataset.code);
            navigator.clipboard?.writeText(code).then(() => flashCopied(codeBtn));
            return;
        }

        const msgBtn = e.target.closest('.copy-msg-btn');

        if (msgBtn) {
            const text = decodeURIComponent(msgBtn.dataset.text);
            navigator.clipboard?.writeText(text).then(() => flashCopied(msgBtn));
            return;
        }

        const editBtn = e.target.closest('.edit-msg-btn');

        if (editBtn && !isStreaming) {
            const index = parseInt(editBtn.dataset.index, 10);
            startEdit(index);
        }
    });

    // ─ Cancel edit button ─────────────────────────────────────────────────────
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', cancelEdit);
    }

    // ─ Edit state helpers ─────────────────────────────────────────────────────
    function startEdit(index) {
        const originalText = chatMessages[index]?.text ?? '';
        const removed = chatMessages.splice(index);

        pendingEdit = {
            branchFromConversationId: conversationId,
            keepMessageCount: index,
            removed,
        };

        removed.forEach((m) => m.el.remove());

        promptEl.value = originalText;
        autoResize();
        promptEl.focus();
        setEditMode(true);
    }

    function cancelEdit() {
        if (!pendingEdit) {
            return;
        }

        pendingEdit.removed.forEach((m) => {
            messagesEl.appendChild(m.el);
            chatMessages.push(m);
        });

        pendingEdit = null;
        promptEl.value = '';
        autoResize();
        setEditMode(false);
    }

    function setEditMode(active) {
        if (active) {
            sendBtn.innerHTML = SEND_ICON + ' Send edit';
            promptEl.placeholder = 'Edit message…';
            cancelEditBtn?.style.removeProperty('display');
        } else {
            sendBtn.innerHTML = SEND_ICON + ' Send';
            promptEl.placeholder = 'Message CurioGPT…';
            cancelEditBtn?.style.setProperty('display', 'none');
        }
    }

    // ─ DOM helpers ────────────────────────────────────────────────────────────
    function scrollBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function addUserMessage(text) {
        const index = chatMessages.length;
        const el = document.createElement('div');
        el.className = 'flex justify-end mb-6 group';
        el.innerHTML = `
            <div class="flex flex-col items-end gap-1.5 max-w-[80%]">
                <div class="rounded-2xl rounded-tr-sm bg-black dark:bg-white text-white dark:text-black px-4 py-2.5 text-sm whitespace-pre-wrap break-words leading-relaxed">${escapeHtml(text)}</div>
                <div class="flex items-center gap-2">
                    <button class="edit-msg-btn flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-0 group-hover:opacity-100" data-index="${index}" title="Edit message">${EDIT_ICON} Edit</button>
                    <button class="copy-msg-btn flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-0 group-hover:opacity-100" data-text="${encodeURIComponent(text)}" title="Copy message">${COPY_ICON} Copy</button>
                </div>
            </div>`;

        messagesEl.appendChild(el);
        scrollBottom();

        chatMessages.push({ type: 'user', text, el });
    }

    function createAssistantBubble() {
        const el = document.createElement('div');
        el.className = 'flex gap-3 items-start mb-6 group';
        el.innerHTML = `
            <div class="shrink-0 mt-0.5 w-7 h-7 rounded-full bg-black dark:bg-white flex items-center justify-center" aria-hidden="true">
                <span class="text-white dark:text-black font-semibold text-xs select-none">C</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="prose-chat text-sm text-black dark:text-white leading-relaxed"></div>
                <div class="mt-2 h-5">
                    <button class="copy-msg-btn flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-0 group-hover:opacity-100" data-text="" title="Copy message">${COPY_ICON} Copy</button>
                </div>
            </div>`;

        messagesEl.appendChild(el);

        return {
            el,
            contentEl: el.querySelector('.prose-chat'),
            copyBtn: el.querySelector('.copy-msg-btn'),
        };
    }

    function addErrorMessage(text) {
        const el = document.createElement('div');
        el.className = 'flex justify-center mb-4';
        el.innerHTML = `<div class="text-xs text-red-500 dark:text-red-400 px-3 py-1.5 rounded-full bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800">${escapeHtml(text)}</div>`;
        messagesEl.appendChild(el);
        scrollBottom();
    }

    // ─ Loading state ───────────────────────────────────────────────────────────
    function setLoading(loading) {
        isStreaming = loading;
        sendBtn.disabled = loading;
        promptEl.focus();
    }

    // ─ Submit ──────────────────────────────────────────────────────────────────
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const prompt = promptEl.value.trim();

        if (!prompt || isStreaming) {
            return;
        }

        promptEl.value = '';
        promptEl.style.height = 'auto';

        let branchFrom = null;

        if (pendingEdit) {
            branchFrom = {
                conversationId: pendingEdit.branchFromConversationId,
                keepMessageCount: pendingEdit.keepMessageCount,
            };
            pendingEdit = null;
            setEditMode(false);
        }

        addUserMessage(prompt);
        setLoading(true);
        statusEl.textContent = '';

        try {
            await ensureCsrfCookie();

            const body = { prompt, agentConfigId: selectedAgentConfigId };

            if (branchFrom) {
                body.branchFrom = branchFrom;
            } else if (conversationId) {
                body.conversationId = conversationId;
            }

            const res = await fetch('/api/agent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') ?? '',
                },
                credentials: 'same-origin',
                body: JSON.stringify(body),
            });

            if (!res.ok) {
                if (res.status === 401) {
                    addErrorMessage('Please log in to chat with the agent.');
                    return;
                }

                addErrorMessage((await res.text()) || `Error ${res.status}`);
                return;
            }

            const { el: assistantEl, contentEl, copyBtn } = createAssistantBubble();
            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';
            let rawText = '';
            let rafPending = false;

            function renderChunk() {
                rafPending = false;
                contentEl.innerHTML = marked.parse(rawText);
                scrollBottom();
            }

            while (true) {
                const { done, value } = await reader.read();

                if (done) {
                    break;
                }

                buffer += decoder.decode(value, { stream: true });

                const lines = buffer.split('\n');
                buffer = lines.pop() ?? '';

                for (const line of lines) {
                    if (!line.startsWith('data: ')) {
                        continue;
                    }

                    const raw = line.slice(6).trim();

                    if (raw === '[DONE]') {
                        break;
                    }

                    try {
                        const event = JSON.parse(raw);

                        if (event.type === 'text_delta') {
                            rawText += event.delta;

                            if (!rafPending) {
                                rafPending = true;
                                requestAnimationFrame(renderChunk);
                            }
                        } else if (event.type === 'conversation_id') {
                            conversationId = event.conversation_id;
                        }
                    } catch {
                        // ignore malformed events
                    }
                }
            }

            // Final render, attach copy text, and track the message
            contentEl.innerHTML = marked.parse(rawText);
            copyBtn.dataset.text = encodeURIComponent(rawText);
            chatMessages.push({ type: 'assistant', text: rawText, el: assistantEl });
            scrollBottom();
        } catch (err) {
            addErrorMessage(err?.message ?? 'Network error');
        } finally {
            setLoading(false);
        }
    });
});
