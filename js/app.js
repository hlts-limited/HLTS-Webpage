/* ==========================================================================
   HLTS site behaviour: header, menus, forms, search and small interactions.
   Animation lives in motion.js.
   ========================================================================== */

(function () {
  'use strict';

  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));
  const desktop = window.matchMedia('(min-width: 1200px)');

  /* ---- Toast ---------------------------------------------------------- */

  function toast(message, iconName = 'info-circle') {
    const el = document.createElement('div');
    el.className = 'toast-hl';
    el.setAttribute('role', 'status');
    const icon = document.createElement('i');
    icon.className = 'bi bi-' + iconName;
    const text = document.createElement('span');
    text.textContent = message;
    el.append(icon, text);
    document.body.appendChild(el);
    requestAnimationFrame(() => el.classList.add('is-visible'));
    setTimeout(() => {
      el.classList.remove('is-visible');
      setTimeout(() => el.remove(), 400);
    }, 4200);
  }
  window.HLTSToast = toast;

  /* ---- Header: solid after scrolling, hides while scrolling down ------ */

  function initHeader() {
    const header = $('[data-header]');
    const bar = $('[data-mobile-bar]');
    if (!header) return;

    let lastY = window.scrollY;
    let ticking = false;

    const update = () => {
      const y = window.scrollY;
      header.classList.toggle('is-scrolled', y > 24);
      const goingDown = y > lastY && y > 420;
      const menuOpen = !!$('.mega.show') || document.body.classList.contains('offcanvas-open');
      header.classList.toggle('is-hidden', goingDown && !menuOpen);
      if (bar) bar.classList.toggle('is-hidden', goingDown && y > 600);
      lastY = y;
      ticking = false;
    };

    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(update);
        ticking = true;
      }
    }, { passive: true });
    update();
  }

  /* ---- Desktop menus open on hover as well as click ------------------- */

  function initHoverMenus() {
    if (typeof bootstrap === 'undefined') return;

    $$('[data-hover-dropdown]').forEach((item) => {
      const toggle = $('[data-bs-toggle="dropdown"]', item);
      const menu = bootstrap.Dropdown.getOrCreateInstance(toggle);
      let timer;

      item.addEventListener('mouseenter', () => {
        if (!desktop.matches) return;
        clearTimeout(timer);
        $$('[data-hover-dropdown] [aria-expanded="true"]').forEach((open) => {
          if (open !== toggle) bootstrap.Dropdown.getOrCreateInstance(open).hide();
        });
        menu.show();
      });

      item.addEventListener('mouseleave', () => {
        if (!desktop.matches) return;
        clearTimeout(timer);
        timer = setTimeout(() => menu.hide(), 160);
      });
    });
  }

  /* ---- Forms ---------------------------------------------------------- */

  const messages = {
    required: 'This field is required.',
    choose: 'Choose an option.',
    chooseOne: 'Choose at least one option.',
    email: 'Enter a valid email address, like name@example.com.',
    tel: 'Enter a valid phone number, like 0810 000 0000.',
    url: 'Enter a full link starting with https://',
    consent: 'Please agree to continue.',
    fileSize: 'This file is too large.',
    fileType: 'Upload a PDF or Word document (.pdf, .doc or .docx).',
  };

  function fieldValue(field) {
    const inputs = $$('input, select, textarea', field).filter((el) => el.name && el.type !== 'hidden');
    if (!inputs.length) return '';
    const first = inputs[0];
    if (first.type === 'checkbox' && first.name.endsWith('[]')) {
      return inputs.filter((el) => el.checked).map((el) => el.value);
    }
    if (first.type === 'checkbox') return first.checked ? first.value : '';
    if (first.type === 'radio') {
      const checked = inputs.find((el) => el.checked);
      return checked ? checked.value : '';
    }
    return first.value.trim();
  }

  function isRequired(field) {
    // Fields switched off (e.g. guardian for adults) are skipped. Fields on
    // other steps of a multi-step form still count.
    if (field.hidden) return false;
    const hiddenParent = field.parentElement.closest('[hidden]');
    if (hiddenParent && !hiddenParent.classList.contains('form-step')) return false;
    const fieldset = $('fieldset', field);
    if (fieldset && fieldset.dataset.required) return fieldset.dataset.required === 'true';
    return !!$('[required]', field);
  }

  function validateField(field) {
    const value = fieldValue(field);
    const input = $('input:not([type="hidden"]), select, textarea', field);
    const type = input ? input.type : '';
    let error = '';

    const empty = Array.isArray(value) ? value.length === 0 : value === '';

    if (empty) {
      if (isRequired(field)) {
        if (field.classList.contains('field--consent')) error = messages.consent;
        else if (field.classList.contains('field--checkbox-cards')) error = messages.chooseOne;
        else if ($('fieldset', field) || (input && input.tagName === 'SELECT')) error = messages.choose;
        else error = messages.required;
      }
    } else if (type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
      error = messages.email;
    } else if (type === 'tel' && !/^\+?\d{10,15}$/.test(value.replace(/[\s\-().]/g, ''))) {
      error = messages.tel;
    } else if (type === 'url' && !/^https?:\/\/\S+\.\S+/i.test(value)) {
      error = messages.url;
    } else if (type === 'file' && input.files && input.files[0]) {
      const file = input.files[0];
      const max = Number(input.dataset.maxBytes || 0);
      if (max && file.size > max) error = `${messages.fileSize} The limit is ${Math.round(max / 1048576)} MB.`;
      else if (!/\.(pdf|docx?)$/i.test(file.name)) error = messages.fileType;
    }

    showFieldError(field, error);
    field.classList.toggle('is-valid', !error && !empty && !!input && !['radio', 'checkbox'].includes(type));
    return !error;
  }

  function showFieldError(field, error) {
    const box = $('.field-error', field);
    const controls = $$('input:not([type="hidden"]), select, textarea', field);
    if (box) {
      box.hidden = !error;
      const span = $('span', box);
      if (span) span.textContent = error;
    }
    controls.forEach((el) => {
      if (error) el.setAttribute('aria-invalid', 'true');
      else el.removeAttribute('aria-invalid');
    });
  }

  function validateGroup(root) {
    const fields = $$('.field', root).filter((f) => !f.hidden);
    let firstInvalid = null;
    fields.forEach((field) => {
      if (!validateField(field) && !firstInvalid) firstInvalid = field;
    });
    if (firstInvalid) {
      const focusable = $('input:not([type="hidden"]), select, textarea', firstInvalid);
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      if (focusable) setTimeout(() => focusable.focus({ preventScroll: true }), 300);
    }
    return !firstInvalid;
  }

  function setAlert(form, message) {
    const alert = $('.form-alert', form);
    if (!alert) return;
    alert.textContent = message || '';
    alert.hidden = !message;
  }

  function initStepped(form) {
    const steps = $$('.form-step', form);
    const indicators = $$('[data-step-indicator]', form);
    const bar = $('.stepper__bar span', form);
    let current = 0;

    const show = (index, back = false) => {
      steps.forEach((step, i) => {
        step.hidden = i !== index;
        step.classList.toggle('is-back', back);
      });
      indicators.forEach((el, i) => {
        el.classList.toggle('is-current', i === index);
        el.classList.toggle('is-done', i < index);
      });
      if (bar) bar.style.setProperty('--progress', ((index) / Math.max(1, steps.length - 1)) * 100 + '%');
      current = index;
      form.dispatchEvent(new CustomEvent('step:change', { detail: { index } }));
      const top = form.getBoundingClientRect().top + window.scrollY - 120;
      if (window.scrollY > top) window.scrollTo({ top, behavior: 'smooth' });
      const heading = $('.form-step__title', steps[index]);
      if (heading) {
        heading.setAttribute('tabindex', '-1');
        heading.focus({ preventScroll: true });
      }
    };

    form.addEventListener('click', (event) => {
      if (event.target.closest('[data-step-next]')) {
        if (validateGroup(steps[current])) {
          setAlert(form, '');
          show(Math.min(current + 1, steps.length - 1));
        }
      }
      if (event.target.closest('[data-step-back]')) {
        show(Math.max(current - 1, 0), true);
      }
    });

    form.goToFieldStep = (name) => {
      const field = $(`.field[data-field="${name}"]`, form);
      const step = field ? field.closest('.form-step') : null;
      if (step) show(steps.indexOf(step));
    };

    show(0);
  }

  /* "Same as my phone number" copies the phone into the WhatsApp field and keeps it in step. */
  function initSameAs(form) {
    $$('[data-same-as]', form).forEach((box) => {
      const field = box.closest('.field');
      const target = field && $('input[data-same-target]', field);
      const source = $(`[name="${box.dataset.sameAs}"]`, form);
      if (!target || !source) return;
      const sync = () => {
        if (!box.checked) return;
        target.value = source.value;
        validateField(field);
      };
      box.addEventListener('change', () => {
        target.readOnly = box.checked;
        sync();
      });
      source.addEventListener('input', sync);
      if (box.checked) target.readOnly = true;
    });
    // Show the chosen file's name.
    $$('input[type="file"]', form).forEach((input) => {
      const label = $('[data-file-name]', input.closest('.file-pick') || form);
      input.addEventListener('change', () => {
        if (label) label.textContent = input.files && input.files[0] ? input.files[0].name : 'Choose a file';
      });
    });
    form.addEventListener('reset', () => {
      $$('[data-file-name]', form).forEach((l) => (l.textContent = 'Choose a file'));
      $$('input[data-same-target]', form).forEach((i) => (i.readOnly = false));
    });
  }

  /* Fields that only apply to some answers (data-show-when="basis=pupils|fulltime") appear and disappear with them. */
  function initShowWhen(form) {
    const fields = $$('[data-show-when]', form);
    if (!fields.length) return;
    const update = () => {
      fields.forEach((field) => {
        const show = field.dataset.showWhen.split('&').every((rule) => {
          const [name, values] = rule.split('=');
          const checked = $(`[name="${name}"]:checked`, form) || $(`select[name="${name}"]`, form);
          return values.split('|').includes(checked ? checked.value : '');
        });
        field.hidden = !show;
        if (!show) showFieldError(field, '');
      });
    };
    form.addEventListener('change', update);
    form.addEventListener('reset', () => setTimeout(update));
    update();
  }

  function initForm(form) {
    if (form.dataset.stepped) initStepped(form);
    initSameAs(form);
    initShowWhen(form);

    // Live validation: check a field once it has been touched.
    form.addEventListener('focusout', (event) => {
      const field = event.target.closest('.field');
      if (field && (event.target.value || field.dataset.touched)) {
        field.dataset.touched = '1';
        validateField(field);
      }
    });
    form.addEventListener('input', (event) => {
      const field = event.target.closest('.field');
      if (field && (field.dataset.touched || $('[aria-invalid="true"]', field))) validateField(field);
    });
    form.addEventListener('change', (event) => {
      const field = event.target.closest('.field');
      if (field && ['radio', 'checkbox'].includes(event.target.type)) validateField(field);
    });

    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      setAlert(form, '');

      if (!validateGroup(form)) {
        if (form.goToFieldStep) {
          const firstBad = $('.field [aria-invalid="true"]', form);
          if (firstBad) form.goToFieldStep(firstBad.closest('.field').dataset.field);
        }
        setAlert(form, 'Please check the highlighted fields.');
        return;
      }

      const button = $('[data-submit]', form);
      if (button) {
        button.classList.add('is-loading');
        button.disabled = true;
      }

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: { Accept: 'application/json' },
          credentials: 'same-origin',
        });
        const data = await response.json().catch(() => ({ ok: false, message: 'Something went wrong. Please try again.' }));

        if (data.ok) {
          if (data.redirect) {
            toast('Taking you to secure payment…', 'lock');
            window.location.href = data.redirect;
            return;
          }
          const success = $('.form-success', form);
          if (success) {
            $('.form-success__text', success).textContent = data.message;
            form.classList.add('is-done');
            success.hidden = false;
            success.focus();
          } else {
            toast(data.message, 'check-circle');
          }
          form.reset();
          return;
        }

        if (data.errors) {
          Object.entries(data.errors).forEach(([name, message]) => {
            const field = $(`.field[data-field="${name}"]`, form);
            if (field) showFieldError(field, message);
          });
          const first = Object.keys(data.errors)[0];
          if (first && form.goToFieldStep) form.goToFieldStep(first);
        }
        setAlert(form, data.message || 'Please check the highlighted fields.');
      } catch (error) {
        setAlert(form, 'We could not reach the server. Check your connection and try again.');
      } finally {
        if (button) {
          button.classList.remove('is-loading');
          button.disabled = false;
        }
      }
    });
  }

  /* ---- Course registration extras ------------------------------------- */

  function initRegistration() {
    const form = $('form[data-form="student"]');
    if (!form) return;

    const fees = JSON.parse(form.dataset.fees || '{}');
    const planField = $('.field[data-field="plan"]', form);
    const payBox = $('[data-pay-option]', form);
    const guardian = $('.field[data-field="guardian"]', form);
    const naira = (kobo) => '₦' + Math.round(kobo / 100).toLocaleString('en-NG');

    const update = () => {
      const course = (fieldValue($('.field[data-field="course"]', form)) || '');
      const courseFees = fees[course] || {};
      const hasFees = Object.keys(courseFees).length > 0;

      if (planField) {
        planField.hidden = !hasFees;
        // A payment plan is required only for courses with published fees.
        $('fieldset', planField).dataset.required = String(hasFees);
        $$('.option', planField).forEach((option) => {
          const input = $('input', option);
          const meta = $('.option__meta', option) || document.createElement('span');
          meta.className = 'option__meta';
          const fee = courseFees[input.value];
          const unit = { session: ' once', semester: ' × 2 semesters', monthly: ' × 8 months' }[input.value] || '';
          meta.textContent = fee ? naira(fee.each) + unit : '';
          if (!meta.parentNode) $('.option__label', option).after(meta);
        });
      }

      const plan = planField ? fieldValue(planField) : '';
      if (payBox) {
        const fee = courseFees[plan];
        payBox.hidden = !fee;
        const label = $('[data-pay-amount]', payBox);
        const note = $('[data-pay-note]', payBox);
        if (fee) {
          if (label) label.textContent = naira(fee.each);
          if (note) {
            note.textContent = fee.count > 1
              ? `Your first of ${fee.count} payments (${naira(fee.total)} in total). Card, bank transfer or USSD; you can also pay later.`
              : 'The full session fee. Card, bank transfer or USSD; you can also pay later.';
          }
        }
      }

      if (guardian) {
        const age = fieldValue($('.field[data-field="age_group"]', form));
        guardian.hidden = !(age === 'under-13' || age === '13-17');
      }
    };

    form.addEventListener('change', update);
    form.addEventListener('step:change', () => {
      update();
      buildReview(form);
    });
    update();
  }

  // Fill a <dl data-review> on the last step with the visitor's answers.
  function buildReview(form) {
    const list = $('[data-review]', form);
    if (!list) return;
    list.innerHTML = '';
    $$('.field', form).forEach((field) => {
      if (field.hidden || field.classList.contains('field--consent')) return;
      if (field.closest('.form-step') === list.closest('.form-step')) return;
      const labelEl = $('.field-label', field);
      if (!labelEl) return;
      let value = fieldValue(field);
      if (Array.isArray(value)) {
        value = $$('input:checked', field).map((el) => $('.option__label', el.parentNode).textContent).join(', ');
      } else {
        const checked = $('input:checked', field);
        const select = $('select', field);
        if (checked && $('.option__label', checked.parentNode)) value = $('.option__label', checked.parentNode).textContent;
        if (select && select.selectedIndex > 0) value = select.options[select.selectedIndex].text;
      }
      if (!value) return;
      const row = document.createElement('div');
      const dt = document.createElement('dt');
      const dd = document.createElement('dd');
      dt.textContent = labelEl.childNodes[0].textContent.trim();
      dd.textContent = value;
      row.append(dt, dd);
      list.appendChild(row);
    });
  }

  /* ---- FAQ search ----------------------------------------------------- */

  function initFaqSearch() {
    const input = $('[data-faq-search]');
    if (!input) return;
    const items = $$('.faq-item');
    const groups = $$('.faq-group');
    const empty = $('[data-faq-empty]');

    input.addEventListener('input', () => {
      const term = input.value.trim().toLowerCase();
      let shown = 0;
      items.forEach((item) => {
        const match = !term || item.textContent.toLowerCase().includes(term);
        item.hidden = !match;
        if (match) shown++;
        if (term && match) item.open = true;
      });
      groups.forEach((group) => {
        group.hidden = !$$('.faq-item', group).some((item) => !item.hidden);
      });
      if (empty) empty.hidden = shown > 0;
    });
  }

  /* ---- Horizontal rails (testimonials) -------------------------------- */

  function initRails() {
    $$('[data-rail]').forEach((wrap) => {
      const rail = $('.quote-rail', wrap);
      const step = () => (rail.firstElementChild ? rail.firstElementChild.getBoundingClientRect().width + 20 : 300);
      $$('[data-rail-prev]', wrap).forEach((btn) => btn.addEventListener('click', () => rail.scrollBy({ left: -step(), behavior: 'smooth' })));
      $$('[data-rail-next]', wrap).forEach((btn) => btn.addEventListener('click', () => rail.scrollBy({ left: step(), behavior: 'smooth' })));
    });
  }

  /* ---- Tabs (ecosystem explorer, portfolio filters) ------------------- */

  function initTabs() {
    $$('[data-tabs]').forEach((tabs) => {
      const buttons = $$('[role="tab"]', tabs);
      const select = (button, focus = false) => {
        buttons.forEach((b) => {
          const active = b === button;
          b.setAttribute('aria-selected', String(active));
          b.tabIndex = active ? 0 : -1;
          const panel = document.getElementById(b.getAttribute('aria-controls'));
          if (panel) {
            panel.hidden = !active;
            if (active) {
              panel.classList.remove('is-entering');
              void panel.offsetWidth;
              panel.classList.add('is-entering');
            }
          }
        });
        if (focus) button.focus();
      };
      buttons.forEach((button, i) => {
        button.addEventListener('click', () => select(button));
        button.addEventListener('keydown', (e) => {
          if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); select(buttons[(i + 1) % buttons.length], true); }
          if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); select(buttons[(i - 1 + buttons.length) % buttons.length], true); }
        });
      });
    });

    // Simple filter chips: buttons with data-filter show cards with matching data-category.
    $$('[data-filter-group]').forEach((group) => {
      const target = document.getElementById(group.dataset.filterGroup);
      if (!target) return;
      $$('[data-filter]', group).forEach((button) => {
        button.addEventListener('click', () => {
          $$('[data-filter]', group).forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
          const value = button.dataset.filter;
          $$('[data-category]', target).forEach((card) => {
            card.hidden = value !== 'all' && card.dataset.category !== value;
          });
        });
      });
    });
  }

  /* ---- Print buttons -------------------------------------------------- */

  function initPrint() {
    $$('[data-print]').forEach((button) => button.addEventListener('click', () => window.print()));
  }

  /* ---- Confirm before destructive admin actions ----------------------- */

  function initConfirm() {
    document.addEventListener('submit', (event) => {
      const message = event.target.dataset.confirm;
      if (message && !window.confirm(message)) event.preventDefault();
    }, true);
  }

  document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initHoverMenus();
    $$('form.smart-form').forEach(initForm);
    initRegistration();
    initFaqSearch();
    initRails();
    initTabs();
    initPrint();
    initConfirm();

    document.addEventListener('shown.bs.offcanvas', () => document.body.classList.add('offcanvas-open'));
    document.addEventListener('hidden.bs.offcanvas', () => document.body.classList.remove('offcanvas-open'));
  });
})();
