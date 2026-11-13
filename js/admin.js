/* Small helpers for the admin area. */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu
    const menu = document.querySelector('[data-admin-menu]');
    const side = document.getElementById('adminSide');
    if (menu && side) {
      menu.addEventListener('click', () => {
        const open = side.classList.toggle('is-open');
        menu.setAttribute('aria-expanded', String(open));
      });
    }

    // Click anywhere on a table row to open it (links inside still work).
    document.querySelectorAll('tr[data-href]').forEach((row) => {
      row.addEventListener('click', (event) => {
        if (event.target.closest('a, button, form, select, input')) return;
        window.location.href = row.dataset.href;
      });
    });

    // Confirm destructive actions.
    document.addEventListener('submit', (event) => {
      const message = event.target.dataset.confirm;
      if (message && !window.confirm(message)) event.preventDefault();
    }, true);

    // Save a select straight away (e.g. a student's course).
    document.querySelectorAll('select[data-autosubmit]').forEach((select) => {
      select.addEventListener('change', () => select.form.submit());
    });

    // Live preview of the chosen cover image.
    document.querySelectorAll('select[data-image-preview]').forEach((select) => {
      const preview = document.querySelector(select.dataset.imagePreview);
      select.addEventListener('change', () => {
        if (!preview) return;
        preview.hidden = !select.value;
        if (select.value) preview.src = '/' + select.value.split('/').map(encodeURIComponent).join('/');
      });
    });

    // Preview a cover photo picked from the device before it's uploaded.
    document.querySelectorAll('input[type=file][data-upload-preview]').forEach((input) => {
      const preview = document.querySelector(input.dataset.uploadPreview);
      input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!preview || !file) return;
        const max = Number(input.dataset.maxBytes) || 10 * 1024 * 1024;
        if (file.size > max) {
          window.alert('That image is too large. The limit is ' + Math.round(max / 1048576) + ' MB.');
          input.value = '';
          return;
        }
        // Read as a data: URL, which the site's security policy allows for images (blob: isn't).
        const reader = new FileReader();
        reader.onload = () => {
          preview.src = reader.result;
          preview.hidden = false;
        };
        reader.readAsDataURL(file);
      });
    });

    // Fill the certificate form from a chosen student.
    const studentSelect = document.querySelector('[data-fill-certificate]');
    if (studentSelect) {
      studentSelect.addEventListener('change', () => {
        const option = studentSelect.selectedOptions[0];
        if (!option || !option.value) return;
        const name = document.getElementById('c-name');
        const course = document.getElementById('c-course');
        if (name && !name.value) name.value = option.dataset.name || '';
        if (course && !course.value) course.value = option.dataset.course || '';
      });
    }

    // Bold and Add link buttons for the post text, plus a live preview of how it will look.
    document.querySelectorAll('textarea[data-format]').forEach(initFormatting);

    // Print buttons
    document.querySelectorAll('[data-print]').forEach((button) => button.addEventListener('click', () => window.print()));
  });

  /* ---- Post text: [words](address) links and **bold** ------------------------------------- */

  /** Same rules as link_href() in lib/helpers.php: web, email and phone links, and this site's pages. */
  function linkHref(url) {
    url = url.trim();
    if (/^https?:\/\//i.test(url)) {
      try {
        const u = new URL(url);
        return u.hostname.includes('.') || u.hostname === 'localhost' ? url : null;
      } catch (e) {
        return null;
      }
    }
    if (/^mailto:[^\s@<>"]+@[^\s@<>"]+\.[^\s@<>"]+$/i.test(url) || /^tel:\+?[0-9][0-9 ()-]{2,20}$/i.test(url)) return url;
    if (/^\/(?![/\\])[^\s\\]*$/.test(url) || /^#[\w-]+$/.test(url)) return url;
    return null;
  }

  /** Put text in place of the selection in a way the browser's undo (Ctrl+Z) understands. */
  function replaceSelection(box, text, selectFrom, selectTo) {
    const start = box.selectionStart;
    box.focus();
    if (!document.execCommand || !document.execCommand('insertText', false, text)) {
      box.setRangeText(text, start, box.selectionEnd, 'end');
      box.dispatchEvent(new Event('input', { bubbles: true }));
    }
    if (selectFrom != null) box.setSelectionRange(start + selectFrom, start + selectTo);
  }

  function initFormatting(box) {
    const bar = document.createElement('div');
    bar.className = 'format-bar';
    bar.setAttribute('role', 'toolbar');
    bar.setAttribute('aria-label', 'Text formatting');
    const button = (label, title, onClick) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'btn-hl btn-hl--ghost btn-hl--sm';
      b.textContent = label;
      b.title = title;
      b.addEventListener('click', onClick);
      bar.appendChild(b);
    };

    button('Bold', 'Make the selected words bold', () => {
      const words = box.value.slice(box.selectionStart, box.selectionEnd);
      if (!words) {
        replaceSelection(box, '**bold words**', 2, 12);
        return;
      }
      replaceSelection(box, '**' + words.trim() + '**');
    });

    button('Add link', 'Turn the selected words into a link', () => {
      const words = box.value.slice(box.selectionStart, box.selectionEnd).trim();
      if (words.includes('\n')) {
        window.alert('Select words within one paragraph, then press Add link.');
        return;
      }
      const address = window.prompt(words ? 'Web address for “' + words + '”:' : 'Web address for the link:', 'https://');
      if (address == null) return;
      if (!linkHref(address)) {
        window.alert('That address can’t be used. Paste a full address starting with https://, an email as mailto:name@example.com, or one of our pages such as /pricing.html.');
        return;
      }
      const label = (words || 'link words').replace(/[\[\]]/g, '');
      replaceSelection(box, '[' + label + '](' + address.trim() + ')', words ? null : 1, words ? null : 1 + label.length);
    });

    const help = document.createElement('span');
    help.className = 'format-bar__hint';
    help.textContent = 'Select words, then press a button.';
    bar.appendChild(help);
    box.before(bar);

    const details = document.createElement('details');
    details.className = 'format-preview';
    const summary = document.createElement('summary');
    summary.textContent = 'Preview';
    const body = document.createElement('div');
    body.className = 'prose';
    details.append(summary, body);
    box.after(details);

    const draw = () => {
      if (details.open) renderPreview(box.value, body);
    };
    details.addEventListener('toggle', draw);
    box.addEventListener('input', draw);
  }

  /** The preview, built from DOM nodes (never HTML strings), following render_text() in helpers.php. */
  function renderPreview(text, into) {
    into.replaceChildren();
    let paragraph = [];
    let list = [];
    const flush = () => {
      if (paragraph.length) {
        const p = document.createElement('p');
        paragraph.forEach((line, i) => {
          if (i) p.appendChild(document.createElement('br'));
          inline(line, p);
        });
        into.appendChild(p);
        paragraph = [];
      }
      if (list.length) {
        const ul = document.createElement('ul');
        list.forEach((item) => {
          const li = document.createElement('li');
          inline(item, li);
          ul.appendChild(li);
        });
        into.appendChild(ul);
        list = [];
      }
    };
    text.trim().split(/\r?\n/).forEach((raw) => {
      const line = raw.trim();
      if (!line) return flush();
      const heading = /^(#{2,3}) (.*)$/.exec(line);
      if (heading) {
        flush();
        const h = document.createElement(heading[1] === '##' ? 'h2' : 'h3');
        inline(heading[2], h);
        into.appendChild(h);
      } else if (line.startsWith('- ')) {
        if (paragraph.length) flush();
        list.push(line.slice(2));
      } else {
        if (list.length) flush();
        paragraph.push(line);
      }
    });
    flush();
    if (!into.childNodes.length) {
      const empty = document.createElement('p');
      empty.className = 'muted';
      empty.textContent = 'Nothing written yet.';
      into.appendChild(empty);
    }
  }

  function inline(text, into) {
    const pattern = /\[([^\]\n]{1,300})\]\(([^()\s]{1,2000})\)|\*\*([^*\n]{1,500}?)\*\*/g;
    let pos = 0;
    let m;
    const bolded = (t, el) => {
      t.split(/\*\*([^*\n]{1,500}?)\*\*/).forEach((part, i) => {
        if (!part) return;
        if (i % 2) {
          const b = document.createElement('strong');
          b.textContent = part;
          el.appendChild(b);
        } else el.appendChild(document.createTextNode(part));
      });
    };
    while ((m = pattern.exec(text))) {
      into.appendChild(document.createTextNode(text.slice(pos, m.index)));
      pos = m.index + m[0].length;
      if (m[3] !== undefined) {
        const b = document.createElement('strong');
        b.textContent = m[3];
        into.appendChild(b);
        continue;
      }
      const href = linkHref(m[2]);
      if (!href) {
        const bad = document.createElement('span');
        bad.className = 'format-preview__bad';
        bad.title = 'This address can’t be used, so it will show as typed.';
        bad.textContent = m[0];
        into.appendChild(bad);
        continue;
      }
      const a = document.createElement('a');
      a.href = href;
      a.target = '_blank';
      a.rel = 'noopener';
      bolded(m[1], a);
      into.appendChild(a);
    }
    into.appendChild(document.createTextNode(text.slice(pos)));
  }
})();
