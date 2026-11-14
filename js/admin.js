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

    // The post editor: toolbar, Write / Preview, pictures, and writing kept safe.
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

  const HINT = 'Select words, then press a button. Drag or paste a photo into the box to add it.';

  /** A small element with a class and optional text. */
  function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text != null) node.textContent = text;
    return node;
  }

  function initFormatting(box) {
    const form = box.form;

    /* ---- Layout: toolbar, Write / Preview, picture strip, status line ------------------- */

    const editor = el('div', 'editor');
    editor.dataset.view = 'write';
    const bar = el('div', 'editor__bar');
    bar.setAttribute('role', 'toolbar');
    bar.setAttribute('aria-label', 'Text formatting');
    const tabs = el('div', 'editor__tabs');
    tabs.setAttribute('role', 'tablist');
    const panes = el('div', 'editor__panes');
    const writePane = el('div', 'editor__write');
    const previewPane = el('div', 'editor__preview');
    const previewBody = el('div', 'prose');
    previewPane.append(el('p', 'editor__label', 'Preview'), previewBody);
    const strip = el('div', 'editor__pictures');
    strip.hidden = true;
    const foot = el('div', 'editor__foot');
    const status = el('span', 'editor__status', HINT);
    status.setAttribute('aria-live', 'polite');
    const stats = el('span', 'editor__stats');
    foot.append(status, stats);

    box.before(editor);
    writePane.appendChild(box);
    panes.append(writePane, previewPane);
    editor.append(bar, tabs, panes, strip, foot);

    let statusTimer = 0;
    const say = (message, seconds = 8) => {
      status.textContent = message;
      window.clearTimeout(statusTimer);
      statusTimer = window.setTimeout(() => { status.textContent = HINT; }, seconds * 1000);
    };

    /* ---- Toolbar ---------------------------------------------------------------------- */

    let group = null;
    const startGroup = () => {
      group = el('div', 'editor__group');
      bar.appendChild(group);
    };
    /** A toolbar button: an icon (or a short text) with its name as a tooltip. */
    const button = (iconName, label, onClick, text) => {
      const b = el('button', 'editor__btn');
      b.type = 'button';
      b.title = label;
      b.setAttribute('aria-label', label);
      if (iconName) {
        const i = el('i', 'bi bi-' + iconName);
        i.setAttribute('aria-hidden', 'true');
        b.appendChild(i);
      }
      if (text) b.appendChild(text);
      b.addEventListener('click', onClick);
      group.appendChild(b);
      return b;
    };

    /**
     * Put a style on the selected words, or take it off again if they already have it. Styles
     * sit inside each other, so words can be bold, italic and smaller at the same time.
     */
    const style = (mark, sample) => {
      const start = box.selectionStart;
      const end = box.selectionEnd;
      const words = box.value.slice(start, end);
      if (!words.trim()) {
        replaceSelection(box, mark + sample + mark, mark.length, mark.length + sample.length);
        return;
      }
      const inner = words.trim();
      // The marks are inside what was selected: take them off.
      if (inner.length > mark.length * 2 && inner.startsWith(mark) && inner.endsWith(mark) && isOwnMark(inner, mark)) {
        replaceSelection(box, inner.slice(mark.length, -mark.length));
        return;
      }
      // The marks sit just outside what was selected: take those off instead.
      const before = box.value.slice(0, start);
      const after = box.value.slice(end);
      if (before.endsWith(mark) && after.startsWith(mark) && isOwnMark(before.slice(-mark.length - 1) + after.slice(0, mark.length + 1), mark)) {
        box.setSelectionRange(start - mark.length, end + mark.length);
        replaceSelection(box, inner);
        return;
      }
      replaceSelection(box, mark + inner + mark, mark.length, mark.length + inner.length);
    };

    /** True unless this is really the other asterisk mark (one * inside a **bold** pair). */
    const isOwnMark = (around, mark) => mark !== '*' || !around.includes('**');

    /** Put a mark at the start of every selected line, or take it off if every line has it. */
    const lineStyle = (mark, sample) => {
      const value = box.value;
      const start = value.lastIndexOf('\n', box.selectionStart - 1) + 1;
      let end = value.indexOf('\n', box.selectionEnd);
      if (end === -1) end = value.length;
      const lines = value.slice(start, end).split('\n');
      if (lines.length === 1 && lines[0].trim() === '') {
        replaceSelection(box, mark + sample, mark.length, mark.length + sample.length);
        return;
      }
      const on = lines.every((l) => l.trim() === '' || l.startsWith(mark));
      // A line only carries one of these at a time, so any other one is taken off first.
      const bare = (l) => l.replace(/^(#{2,3} |- |> )/, '');
      const next = lines.map((l) => (l.trim() === '' ? l : on ? l.slice(mark.length) : mark + bare(l))).join('\n');
      box.setSelectionRange(start, end);
      replaceSelection(box, next, 0, next.length);
    };

    const addLink = () => {
      const words = box.value.slice(box.selectionStart, box.selectionEnd).trim();
      if (words.includes('\n')) {
        say('Select words within one paragraph, then press Link.');
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
    };

    const smallerMark = el('span', 'editor__glyph');
    smallerMark.append('A', el('small', null, 'a'));

    startGroup();
    button('type-bold', 'Bold (Ctrl+B)', () => style('**', 'bold words'));
    button('type-italic', 'Italic (Ctrl+I)', () => style('*', 'italic words'));
    button(null, 'Smaller text', () => style('~', 'smaller words'), smallerMark);
    startGroup();
    button('type-h2', 'Heading', () => lineStyle('## ', 'Heading'));
    button('list-ul', 'Bullet list', () => lineStyle('- ', 'First point'));
    button('quote', 'Quotation', () => lineStyle('> ', 'A quotation'));
    startGroup();
    button('link-45deg', 'Link (Ctrl+K)', addLink);
    let beside = false;
    const picker = el('input');
    picker.type = 'file';
    picker.accept = 'image/jpeg,image/png,image/webp';
    picker.hidden = true;
    const addPicture = button('image', 'Picture across the width', () => { beside = false; picker.click(); });
    const addBeside = button('layout-text-sidebar-reverse', 'Picture beside text', () => { beside = true; picker.click(); });
    group.appendChild(picker);
    startGroup();
    group.classList.add('editor__group--end');
    const fullButton = button('arrows-fullscreen', 'Full screen (Esc to leave)', () => setFull(!editor.classList.contains('is-full')));

    box.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && editor.classList.contains('is-full')) {
        setFull(false);
        return;
      }
      if (!event.ctrlKey && !event.metaKey) return;
      const key = event.key.toLowerCase();
      if (key === 'b' || key === 'i') {
        event.preventDefault();
        style(key === 'b' ? '**' : '*', key === 'b' ? 'bold words' : 'italic words');
      } else if (key === 'k') {
        event.preventDefault();
        addLink();
      }
    });

    /* ---- Write / Preview -------------------------------------------------------------- */

    const tabButtons = ['write', 'preview'].map((view) => {
      const t = el('button', 'editor__tab', view === 'write' ? 'Write' : 'Preview');
      t.type = 'button';
      t.setAttribute('role', 'tab');
      t.addEventListener('click', () => setView(view));
      tabs.appendChild(t);
      return t;
    });
    const setView = (view) => {
      editor.dataset.view = view;
      tabButtons.forEach((t, n) => t.setAttribute('aria-selected', String((n === 0) === (view === 'write'))));
      if (view === 'write') box.focus();
    };
    setView('write');

    // Full screen: writing and preview side by side, with nothing else on the page.
    const setFull = (on) => {
      editor.classList.toggle('is-full', on);
      document.body.classList.toggle('editor-is-full', on);
      fullButton.querySelector('i').className = 'bi bi-' + (on ? 'fullscreen-exit' : 'arrows-fullscreen');
      fullButton.title = on ? 'Leave full screen (Esc)' : 'Full screen (Esc to leave)';
      fullButton.setAttribute('aria-label', fullButton.title);
      box.focus();
    };

    // The toolbar stays in view under the admin header while a long post is written.
    const top = document.querySelector('.admin-top');
    const setTop = () => editor.style.setProperty('--editor-top', (top ? top.getBoundingClientRect().height : 0) + 'px');
    setTop();
    window.addEventListener('resize', setTop);

    /* ---- Preview, word count and the pictures in the post ----------------------------- */

    let drawTimer = 0;
    const draw = () => {
      renderPreview(box.value, previewBody);
      const words = (previewBody.textContent.trim().match(/\S+/g) || []).length;
      stats.textContent = words.toLocaleString() + ' words · ' + Math.max(1, Math.round(words / 200)) + ' min read';
      drawStrip();
    };
    box.addEventListener('input', () => {
      window.clearTimeout(drawTimer);
      drawTimer = window.setTimeout(draw, 120);
    });

    /** Thumbnails of every picture in the text; pressing one finds its line in the box. */
    const drawStrip = () => {
      strip.replaceChildren();
      const found = [...box.value.matchAll(/^!\[([^\]\n]*)\]\(([^()\s]+)\)\s*$/gm)];
      strip.hidden = !found.length;
      if (!found.length) return;
      strip.appendChild(el('span', 'editor__label', 'Pictures in this post'));
      found.forEach((m) => {
        const path = m[2].replace(/^\/+/, '');
        const thumb = el('button', 'editor__thumb');
        thumb.type = 'button';
        thumb.title = (m[1] || 'Picture') + ' — show it in the text';
        const img = el('img');
        img.alt = '';
        img.loading = 'lazy';
        img.src = '/' + path.split('/').map(encodeURIComponent).join('/');
        thumb.appendChild(img);
        thumb.addEventListener('click', () => {
          setView('write');
          box.focus();
          box.setSelectionRange(m.index, m.index + m[0].trimEnd().length);
        });
        strip.appendChild(thumb);
      });
    };

    /* ---- Pictures: from the buttons, dragged in, or pasted ---------------------------- */

    const maxBytes = Number((document.querySelector('[data-max-bytes]') || {}).dataset?.maxBytes) || 10 * 1024 * 1024;

    const upload = async (file, nextToText) => {
      if (!/^image\/(jpeg|png|webp)$/.test(file.type)) {
        say('Only JPG, PNG or WebP photos can be added.');
        return;
      }
      if (file.size > maxBytes) {
        window.alert('That picture is too large. The limit is ' + Math.round(maxBytes / 1048576) + ' MB.');
        return;
      }
      const token = form.querySelector('input[name=csrf_token]');
      const body = new FormData();
      body.append('csrf_token', token ? token.value : '');
      body.append('picture', file);
      const pressed = nextToText ? addBeside : addPicture;
      pressed.disabled = true;
      editor.classList.add('is-uploading');
      say('Uploading the picture…', 60);
      try {
        const response = await fetch('/admin/upload.php', { method: 'POST', body, headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        const result = await response.json();
        if (!result.ok || !result.path) {
          say(result.message || 'That picture could not be uploaded.');
          return;
        }
        // Beside text: the words go on the very next line, ready to be typed over. On its own:
        // the picture line alone, with the cursor between the brackets for a caption.
        const words = 'Write the text that goes beside the picture here.';
        const line = '\n\n![](' + result.path + ')\n';
        if (nextToText) replaceSelection(box, line + words + '\n\n', line.length, line.length + words.length);
        else replaceSelection(box, line + '\n', 4, 4);
        draw();
        say(nextToText ? 'Picture added. Type the words that go beside it, over the highlighted text.' : 'Picture added (see the strip below). Type a caption between the brackets, or leave it empty.');
      } catch (e) {
        say('That picture could not be uploaded. Check your connection and try again.');
      } finally {
        pressed.disabled = false;
        editor.classList.remove('is-uploading');
      }
    };

    picker.addEventListener('change', () => {
      const file = picker.files && picker.files[0];
      picker.value = '';
      if (file) upload(file, beside);
    });

    const hasFiles = (event) => event.dataTransfer && [...event.dataTransfer.types].includes('Files');
    box.addEventListener('dragover', (event) => {
      if (!hasFiles(event)) return;
      event.preventDefault();
      editor.classList.add('is-dropping');
    });
    box.addEventListener('dragleave', () => editor.classList.remove('is-dropping'));
    box.addEventListener('drop', (event) => {
      editor.classList.remove('is-dropping');
      if (!hasFiles(event)) return;
      event.preventDefault();
      const file = [...event.dataTransfer.files].find((f) => f.type.startsWith('image/'));
      if (file) upload(file, false);
    });
    box.addEventListener('paste', (event) => {
      const file = [...((event.clipboardData && event.clipboardData.files) || [])].find((f) => f.type.startsWith('image/'));
      if (!file) return;
      event.preventDefault();
      upload(file, false);
    });

    /* ---- Never lose writing: kept in this browser, and a warning before leaving -------- */

    const key = 'hlts-editor:' + location.pathname + location.search;
    const fields = () => [...form.elements].filter((f) => f.name && !['csrf_token', 'action', 'then'].includes(f.name) && !['file', 'submit', 'button'].includes(f.type));
    const snapshot = () => JSON.stringify(Object.fromEntries(fields().map((f) => [f.name, f.value])));
    let original = snapshot();
    let submitting = false;
    let saveTimer = 0;

    const keep = () => {
      const now = snapshot();
      try {
        if (now === original) localStorage.removeItem(key);
        else localStorage.setItem(key, JSON.stringify({ at: Date.now(), values: JSON.parse(now) }));
        if (now !== original) stats.dataset.kept = 'Kept in this browser ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      } catch (e) {
        /* storage unavailable: the leave warning still protects the writing */
      }
    };
    form.addEventListener('input', () => {
      window.clearTimeout(saveTimer);
      saveTimer = window.setTimeout(keep, 1500);
    });
    form.addEventListener('submit', (event) => {
      // "Save and view as published" opens in another tab and this one stays: what is in the
      // form now is saved, so only later changes need protecting.
      if (event.submitter && event.submitter.formTarget === '_blank') original = snapshot();
      else submitting = true;
      try {
        localStorage.removeItem(key);
      } catch (e) {
        /* nothing kept */
      }
    });
    window.addEventListener('beforeunload', (event) => {
      if (submitting || snapshot() === original) return;
      event.preventDefault();
      event.returnValue = '';
    });

    // Writing left behind last time (tab closed, connection lost): offer it back.
    let stored = null;
    try {
      stored = JSON.parse(localStorage.getItem(key) || 'null');
    } catch (e) {
      stored = null;
    }
    if (stored && stored.values && JSON.stringify(stored.values) !== original) {
      const notice = el('div', 'editor__restore');
      notice.setAttribute('role', 'status');
      const when = new Date(stored.at).toLocaleString([], { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
      notice.appendChild(el('span', null, 'There is unsaved writing from ' + when + ' on this computer.'));
      const restore = el('button', 'btn-hl btn-hl--primary btn-hl--sm', 'Bring it back');
      restore.type = 'button';
      const discard = el('button', 'btn-hl btn-hl--ghost btn-hl--sm', 'Discard it');
      discard.type = 'button';
      restore.addEventListener('click', () => {
        fields().forEach((f) => {
          if (f.name in stored.values) f.value = stored.values[f.name];
        });
        notice.remove();
        draw();
        say('Your unsaved writing is back. Press Save to keep it.');
      });
      discard.addEventListener('click', () => {
        try {
          localStorage.removeItem(key);
        } catch (e) {
          /* nothing kept */
        }
        notice.remove();
      });
      notice.append(restore, discard);
      editor.before(notice);
    }

    // The short summary: how long it is against what fits in search results.
    const summary = form.querySelector('textarea[name=excerpt]');
    if (summary) {
      const counter = el('p', 'editor__counter');
      summary.after(counter);
      const count = () => {
        const n = summary.value.length;
        counter.textContent = n + ' characters' + (n > 160 ? ' — search results show about the first 160' : '');
        counter.classList.toggle('is-long', n > 160);
      };
      summary.addEventListener('input', count);
      count();
    }

    draw();
  }

  /** A picture in the preview. Same rule as post_image_path() in lib/helpers.php: our images only. */
  function pictureFigure(caption, src, beside) {
    const path = src.trim().replace(/^\/+/, '');
    if (path.includes('..') || path.includes('\\') || !/^images\/[\w.\- ]+(\/[\w.\- ]+)*\.(jpe?g|png|webp|gif)$/i.test(path)) {
      const bad = document.createElement('p');
      const mark = document.createElement('span');
      mark.className = 'format-preview__bad';
      mark.title = 'Only pictures stored on this website can be used, so this will show as typed.';
      mark.textContent = '![' + caption + '](' + src + ')';
      bad.appendChild(mark);
      return bad;
    }
    const figure = document.createElement('figure');
    figure.className = beside ? 'prose-beside__picture' : 'prose-figure';
    const image = document.createElement('img');
    image.src = '/' + path.split('/').map(encodeURIComponent).join('/');
    image.alt = caption.trim();
    image.loading = 'lazy';
    // If the picture can't be found, say so rather than leaving a blank space.
    image.addEventListener('error', () => {
      const missing = document.createElement('p');
      missing.className = 'format-preview__bad';
      missing.textContent = 'This picture could not be found: ' + path;
      figure.replaceWith(missing);
    });
    figure.appendChild(image);
    if (caption.trim()) {
      const text = document.createElement('figcaption');
      text.textContent = caption.trim();
      figure.appendChild(text);
    }
    return figure;
  }

  function renderPreview(text, into) {
    into.replaceChildren();
    let paragraph = [];
    let list = [];
    let quote = [];
    const flush = () => {
      if (quote.length) {
        const block = document.createElement('blockquote');
        block.className = 'prose-quote';
        const q = document.createElement('p');
        quote.forEach((line, i) => {
          if (i) q.appendChild(document.createElement('br'));
          inline(line, q);
        });
        block.appendChild(q);
        into.appendChild(block);
        quote = [];
      }
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
    const lines = text.trim().split(/\r?\n/);
    // A line that starts something of its own, so it never runs beside a picture (is_block_line).
    const isBlock = (l) => !l || /^(#{2,3} |- |> |!\[)/.test(l);
    for (let i = 0; i < lines.length; i++) {
      const line = lines[i].trim();
      if (!line) {
        flush();
        continue;
      }
      const picture = /^!\[([^\]\n]{0,300})\]\(([^()\s]{1,300})\)$/.exec(line);
      const heading = /^(#{2,3}) (.*)$/.exec(line);
      if (picture) {
        flush();
        // Text on the very next line runs beside the picture; a blank line means full width.
        const words = [];
        while (i + 1 < lines.length && !isBlock(lines[i + 1].trim())) words.push(lines[++i].trim());
        if (!words.length) {
          into.appendChild(pictureFigure(picture[1], picture[2]));
          continue;
        }
        const block = document.createElement('div');
        block.className = 'prose-beside';
        block.appendChild(pictureFigure(picture[1], picture[2], true));
        const side = document.createElement('div');
        side.className = 'prose-beside__text';
        const p = document.createElement('p');
        words.forEach((w, n) => {
          if (n) p.appendChild(document.createElement('br'));
          inline(w, p);
        });
        side.appendChild(p);
        block.appendChild(side);
        into.appendChild(block);
      } else if (heading) {
        flush();
        const h = document.createElement(heading[1] === '##' ? 'h2' : 'h3');
        inline(heading[2], h);
        into.appendChild(h);
      } else if (line.startsWith('- ')) {
        if (paragraph.length || quote.length) flush();
        list.push(line.slice(2));
      } else if (line.startsWith('> ')) {
        if (paragraph.length || list.length) flush();
        quote.push(line.slice(2));
      } else {
        if (list.length || quote.length) flush();
        paragraph.push(line);
      }
    }
    flush();
    if (!into.childNodes.length) {
      const empty = document.createElement('p');
      empty.className = 'muted';
      empty.textContent = 'Nothing written yet.';
      into.appendChild(empty);
    }
  }

  const MARKS = [
    [/\*\*([^*\n]{1,500}?)\*\*/g, 'strong'],
    [/\*([^*\n]{1,500}?)\*/g, 'em'],
    [/~([^~\n]{1,500}?)~/g, 'small'],
  ];

  /**
   * **bold**, *italic* and ~smaller~, following render_emphasis() in lib/helpers.php: the
   * outermost mark is taken first and the same is then done inside it, so styles combine.
   */
  function emphasis(text, into, links) {
    let best = null;
    MARKS.forEach(([pattern, tag]) => {
      pattern.lastIndex = 0;
      let m;
      while ((m = pattern.exec(text))) {
        // A mark with a space just inside it (as in "3 * 4 * 5") is ordinary writing.
        if (m[1] && m[1].trim() === m[1]) {
          if (!best || m.index < best.at) best = { at: m.index, length: m[0].length, inner: m[1], tag };
          break;
        }
        pattern.lastIndex = m.index + 1;
      }
    });
    if (!best) {
      plain(text, into, links);
      return;
    }
    plain(text.slice(0, best.at), into, links);
    const el = document.createElement(best.tag);
    emphasis(best.inner, el, links);
    into.appendChild(el);
    emphasis(text.slice(best.at + best.length), into, links);
  }

  /** Text with no styles left in it: the links set aside earlier become real links. */
  function plain(text, into, links) {
    const pattern = /\u0002(\d+)\u0003/g;
    let pos = 0;
    let m;
    while ((m = pattern.exec(text))) {
      into.appendChild(document.createTextNode(text.slice(pos, m.index)));
      pos = m.index + m[0].length;
      const link = links[Number(m[1])];
      if (!link.href) {
        const bad = document.createElement('span');
        bad.className = 'format-preview__bad';
        bad.title = 'This address can’t be used, so it will show as typed.';
        bad.textContent = link.asTyped;
        into.appendChild(bad);
        continue;
      }
      const a = document.createElement('a');
      a.href = link.href;
      a.target = '_blank';
      a.rel = 'noopener';
      emphasis(link.label, a, links);
      into.appendChild(a);
    }
    into.appendChild(document.createTextNode(text.slice(pos)));
  }

  function inline(text, into) {
    // Links are set aside first so that styles can wrap them: *~[words](/page)~* works.
    const links = [];
    const tokenised = text.replace(/\[([^\]\n]{1,300})\]\(([^()\s]{1,2000})\)/g, (whole, label, src) => {
      links.push({ href: linkHref(src), label, asTyped: whole });
      return '\u0002' + (links.length - 1) + '\u0003';
    });
    emphasis(tokenised, into, links);
  }
})();
