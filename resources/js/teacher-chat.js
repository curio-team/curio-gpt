import 'highlight.js/styles/atom-one-dark.min.css';
import { renderMarkdown } from './markdown';

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
        el.innerHTML = renderMarkdown(rawText);
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
