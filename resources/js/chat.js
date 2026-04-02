import { marked } from 'marked';
import hljs from 'highlight.js';
import 'highlight.js/styles/atom-one-dark.min.css';

// ─── Markdown Renderer ────────────────────────────────────────────────────────

function cloneTemplate(id) {
    const tpl = document.getElementById(id);
    if (!tpl || !tpl.content || !tpl.content.firstElementChild) {
        throw new Error(`Missing template: ${id}`);
    }
    return tpl.content.firstElementChild.cloneNode(true);
}

const renderer = {
    code({ text, lang }) {
        const validLang = lang && hljs.getLanguage(lang) ? lang : 'plaintext';
        const highlighted = hljs.highlight(text, { language: validLang }).value;

        try {
            const el = cloneTemplate('tpl-code-block');
            const langEl = el.querySelector('[data-slot="lang"]');
            const codeEl = el.querySelector('[data-slot="code"]');
            const copyBtn = el.querySelector('.copy-code-btn');

            if (langEl) { langEl.textContent = lang || 'text'; }
            if (copyBtn) { copyBtn.dataset.code = encodeURIComponent(text); }
            if (codeEl) {
                codeEl.classList.add(`language-${validLang}`);
                codeEl.innerHTML = highlighted;
            }

            return el.outerHTML;
        } catch (e) {
            // Fallback minimal HTML if template missing
            return `<pre class="chat-code-pre"><code class="hljs language-${validLang}">${highlighted}</code></pre>`;
        }
    },
};

marked.use({ renderer, breaks: true, gfm: true });

// ─── Icons & labels from templates ───────────────────────────────────────────
function getCheckIconHtml() {
    return document.getElementById('tpl-icon-check')?.innerHTML?.trim() ?? '✓';
}
function getCopiedText() {
    const t = document.getElementById('tpl-copied-text');
    return (t?.content?.textContent ?? 'Copied!').trim();
}

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
    btn.innerHTML = getCheckIconHtml() + ' ' + getCopiedText();
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
    const sendBtn = document.getElementById('send-btn');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const chatContainer = document.querySelector('[data-agent-config-id]');
    const modelSelect = document.getElementById('model-select');
    const turnCounterEl = document.getElementById('turn-counter');
    const turnLimitBanner = document.getElementById('turn-limit-banner');
    const turnLimitNewChatBtn = document.getElementById('turn-limit-new-chat-btn');
    const selectedAgentConfigId = chatContainer?.dataset.agentConfigId ?? null;
    const turnLimit = parseInt(chatContainer?.dataset.turnLimit ?? '0', 10);
    let conversationId = null;
    let isStreaming = false;
    let userTurnCount = 0;

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

        // Recalculate turn count from remaining messages
        userTurnCount = chatMessages.filter((m) => m.type === 'user').length;
        unlockChat();
        updateTurnCounter();

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

        // Restore turn count from full message list
        userTurnCount = chatMessages.filter((m) => m.type === 'user').length;
        updateTurnCounter();

        pendingEdit = null;
        promptEl.value = '';
        autoResize();
        setEditMode(false);
    }

    function setEditMode(active) {
        if (active) {
            cancelEditBtn?.style.removeProperty('display');
        } else {
            cancelEditBtn?.style.setProperty('display', 'none');
        }
    }

    // ─ DOM helpers ────────────────────────────────────────────────────────────
    function scrollBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function addUserMessage(text, skipCountIncrement = false) {
        const index = chatMessages.length;
        const el = cloneTemplate('tpl-user-message');

        const textEl = el.querySelector('[data-slot="text"]');
        const editBtn = el.querySelector('.edit-msg-btn');
        const copyBtn = el.querySelector('.copy-msg-btn');

        if (textEl) { textEl.textContent = text; }
        if (editBtn) { editBtn.dataset.index = String(index); }
        if (copyBtn) { copyBtn.dataset.text = encodeURIComponent(text); }

        messagesEl.appendChild(el);
        scrollBottom();

        chatMessages.push({ type: 'user', text, el });

        if (!skipCountIncrement) {
            userTurnCount++;
            updateTurnCounter();
        }
    }

    function createAssistantBubble() {
        const el = cloneTemplate('tpl-assistant-message');
        messagesEl.appendChild(el);
        return {
            el,
            contentEl: el.querySelector('[data-slot="content"]') || el.querySelector('.prose-chat'),
            copyBtn: el.querySelector('.copy-msg-btn'),
        };
    }

    function addErrorMessage(text) {
        const el = cloneTemplate('tpl-error-message');
        const textEl = el.querySelector('[data-slot="text"]');
        if (textEl) { textEl.textContent = text; }
        messagesEl.appendChild(el);
        scrollBottom();
    }

    // ─ Loading state ───────────────────────────────────────────────────────────
    function setLoading(loading) {
        isStreaming = loading;
        sendBtn.disabled = loading || (turnLimit > 0 && userTurnCount >= turnLimit);
        promptEl.focus();
    }

    // ─ Turn limit helpers ──────────────────────────────────────────────────────
    function updateTurnCounter() {
        if (!turnLimit || turnLimit <= 0) { return; }

        const tpl = document.getElementById('tpl-turn-counter-text');
        const pattern = tpl?.content?.textContent?.trim() ?? ':used / :limit messages';
        const text = pattern.replace(':used', String(userTurnCount)).replace(':limit', String(turnLimit));

        if (turnCounterEl) {
            turnCounterEl.textContent = text;
            turnCounterEl.style.removeProperty('display');

            const remaining = turnLimit - userTurnCount;
            if (remaining <= 5 && remaining > 0) {
                turnCounterEl.classList.add('text-amber-500', 'dark:text-amber-400');
                turnCounterEl.classList.remove('text-gray-400', 'dark:text-gray-500');
            } else {
                turnCounterEl.classList.remove('text-amber-500', 'dark:text-amber-400');
                turnCounterEl.classList.add('text-gray-400', 'dark:text-gray-500');
            }
        }

        if (userTurnCount >= turnLimit) {
            lockChat();
        }
    }

    function lockChat() {
        if (turnLimitBanner) { turnLimitBanner.style.removeProperty('display'); }
        if (form) { form.style.setProperty('display', 'none'); }
    }

    function unlockChat() {
        if (turnLimitBanner) { turnLimitBanner.style.setProperty('display', 'none'); }
        if (form) { form.style.removeProperty('display'); }
        if (turnCounterEl) { turnCounterEl.style.setProperty('display', 'none'); }
    }

    if (turnLimitNewChatBtn) {
        turnLimitNewChatBtn.addEventListener('click', startNewChat);
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

        try {
            await ensureCsrfCookie();

            const body = { prompt, agentConfigId: selectedAgentConfigId };

            if (modelSelect && modelSelect.value) {
                body.model = modelSelect.value;
            }

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

                if (res.status === 429) {
                    lockChat();
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
                            loadConversations();
                            setActiveConversation(conversationId);
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

    // ─ Conversations Sidebar ──────────────────────────────────────────────────

    const newChatBtn = document.getElementById('new-chat-btn');
    const conversationsList = document.getElementById('conversations-list');

    function relativeTime(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const diffMins = Math.floor((now - date) / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffMins < 1) { return 'just now'; }
        if (diffMins < 60) { return `${diffMins}m ago`; }
        if (diffHours < 24) { return `${diffHours}h ago`; }
        if (diffDays < 7) { return `${diffDays}d ago`; }

        return date.toLocaleDateString();
    }

    function setActiveConversation(id) {
        if (!conversationsList) { return; }

        conversationsList.querySelectorAll('[data-conversation-id]').forEach((btn) => {
            if (btn.dataset.conversationId === id) {
                btn.classList.add('bg-black/5', 'dark:bg-white/5');
            } else {
                btn.classList.remove('bg-black/5', 'dark:bg-white/5');
            }
        });
    }

    async function loadConversations() {
        if (!conversationsList || !selectedAgentConfigId) {
            return;
        }

        try {
            const res = await fetch(`/api/conversations?agentConfigId=${encodeURIComponent(selectedAgentConfigId)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!res.ok) {
                return;
            }

            const conversations = await res.json();

            renderConversationList(conversations);

            if (conversationId) {
                setActiveConversation(conversationId);
            }
        } catch {
            // silently ignore
        }
    }

    function renderConversationList(conversations) {
        if (!conversationsList) { return; }

        conversationsList.innerHTML = '';

        if (conversations.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'px-4 py-4 text-xs text-gray-400 dark:text-gray-500 text-center';
            empty.textContent = 'No previous chats.';
            conversationsList.appendChild(empty);

            return;
        }

        conversations.forEach((conv) => {
            const btn = cloneTemplate('tpl-conversation-item');
            btn.dataset.conversationId = conv.id;
            const titleEl = btn.querySelector('[data-slot="title"]');
            const timeEl = btn.querySelector('[data-slot="time"]');
            if (titleEl) { titleEl.textContent = conv.title; }
            if (timeEl) { timeEl.textContent = relativeTime(conv.updated_at); }
            btn.addEventListener('click', () => selectConversation(conv.id));
            conversationsList.appendChild(btn);
        });
    }

    async function selectConversation(id) {
        if (isStreaming) { return; }

        try {
            const res = await fetch(`/api/conversations/${encodeURIComponent(id)}/messages`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!res.ok) { return; }

            const messages = await res.json();

            messagesEl.innerHTML = '';
            chatMessages = [];
            pendingEdit = null;
            setEditMode(false);
            conversationId = id;
            userTurnCount = 0;

            messages.forEach((msg) => {
                if (msg.role === 'user') {
                    addUserMessage(msg.content, true);
                    userTurnCount++;
                } else if (msg.role === 'assistant') {
                    const { el: assistantEl, contentEl, copyBtn } = createAssistantBubble();
                    contentEl.innerHTML = marked.parse(msg.content);
                    copyBtn.dataset.text = encodeURIComponent(msg.content);
                    chatMessages.push({ type: 'assistant', text: msg.content, el: assistantEl });
                }
            });

            unlockChat();
            updateTurnCounter();

            setActiveConversation(id);
            scrollBottom();
        } catch {
            // silently ignore
        }
    }

    function startNewChat() {
        if (isStreaming) { return; }

        messagesEl.innerHTML = '';
        chatMessages = [];
        pendingEdit = null;
        setEditMode(false);
        conversationId = null;
        userTurnCount = 0;
        unlockChat();
        setActiveConversation(null);
        promptEl.focus();
    }

    if (newChatBtn) {
        newChatBtn.addEventListener('click', startNewChat);
    }

    loadConversations();
});
