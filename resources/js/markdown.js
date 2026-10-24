import { marked } from 'marked';
import hljs from 'highlight.js';

// ─── Shared, XSS-safe markdown rendering ─────────────────────────────────────
// Chat content is written by students and by the AI (which students can steer),
// so the rendered HTML must never contain raw HTML or script-capable URLs.

const SAFE_URL_PROTOCOLS = ['http', 'https', 'mailto'];

export function cloneTemplate(id) {
    const tpl = document.getElementById(id);
    if (!tpl || !tpl.content || !tpl.content.firstElementChild) {
        throw new Error(`Missing template: ${id}`);
    }
    return tpl.content.firstElementChild.cloneNode(true);
}

export function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/**
 * Only allow relative URLs and an allowlist of protocols (blocks javascript:, data:, vbscript:, ...).
 */
export function isSafeUrl(href) {
    if (typeof href !== 'string') {
        return false;
    }

    // Browsers ignore whitespace and control characters when parsing the scheme (e.g. "java\tscript:").
    const normalized = href.replace(/[\u0000- \u007f-\u009f]/g, '');
    const scheme = normalized.match(/^([a-z][a-z0-9+.-]*):/i);

    if (!scheme) {
        return true;
    }

    return SAFE_URL_PROTOCOLS.includes(scheme[1].toLowerCase());
}

function titleAttribute(title) {
    return title ? ` title="${escapeHtml(title)}"` : '';
}

const renderer = {
    html({ text }) {
        return escapeHtml(text);
    },
    link({ href, title, tokens }) {
        const text = this.parser.parseInline(tokens);

        if (!isSafeUrl(href)) {
            return text;
        }

        return `<a href="${escapeHtml(href)}"${titleAttribute(title)}>${text}</a>`;
    },
    image({ href, title, text }) {
        if (!isSafeUrl(href)) {
            return escapeHtml(text);
        }

        return `<img src="${escapeHtml(href)}" alt="${escapeHtml(text)}"${titleAttribute(title)}>`;
    },
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

export function renderMarkdown(text) {
    return marked.parse(text ?? '');
}
