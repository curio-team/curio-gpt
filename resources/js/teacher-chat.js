import { marked } from 'marked';
import hljs from 'highlight.js';
import 'highlight.js/styles/atom-one-dark.min.css';

// ─── Utilities & Templates ───────────────────────────────────────────────────
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

function getCheckIconHtml() {
    return document.getElementById('tpl-icon-check')?.innerHTML?.trim() ?? '✓';
}
function getCopiedText() {
    const t = document.getElementById('tpl-copied-text');
    return (t?.content?.textContent ?? 'Copied!').trim();
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

// ─── Bootstrap ───────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Render any markdown blocks
    document.querySelectorAll('[data-md-source]').forEach((el) => {
        const rawText = el.textContent ?? '';
        el.innerHTML = marked.parse(rawText);
    });

    // Copy buttons for code blocks
    document.body.addEventListener('click', (e) => {
        const btn = e.target.closest('.copy-code-btn');
        if (!btn) { return; }
        const code = decodeURIComponent(btn.dataset.code || '');
        if (!navigator.clipboard) { return; }
        navigator.clipboard.writeText(code).then(() => flashCopied(btn));
    });
});
