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

    // Print buttons
    document.querySelectorAll('[data-print]').forEach((button) => button.addEventListener('click', () => window.print()));
  });
})();
