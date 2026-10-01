/* ==========================================================================
   HLTS motion: scroll reveals and the animated product scenes.
   Every effect checks prefers-reduced-motion and shows its final state
   straight away for visitors who have asked for less movement.
   ========================================================================== */

(function () {
  'use strict';

  window.HLTSMotionReady = true;

  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  /** Run fn once when el scrolls into view. */
  function whenVisible(el, fn, options = {}) {
    if (!('IntersectionObserver' in window)) return fn(el);
    // Already on screen: start now rather than waiting for the first callback.
    const rect = el.getBoundingClientRect();
    if (rect.top < window.innerHeight * 0.92 && rect.bottom > 0) {
      setTimeout(() => fn(el), 120);
      return;
    }
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          io.unobserve(entry.target);
          fn(entry.target);
        }
      });
    }, { threshold: options.threshold ?? 0.2, rootMargin: options.rootMargin ?? '0px 0px -8% 0px' });
    io.observe(el);
  }

  /* ---- Reveal on scroll ---------------------------------------------- */

  function initReveal() {
    // Split marked headlines into words that rise one after another.
    $$('[data-split]').forEach((el) => {
      const words = el.textContent.trim().split(/\s+/);
      const html = el.innerHTML;
      // Only split plain-text headings; keep any that contain markup intact.
      if (/<(?!br)/i.test(html)) return;
      el.textContent = '';
      el.classList.add('split-words');
      el.setAttribute('aria-label', words.join(' '));
      words.forEach((word, i) => {
        const span = document.createElement('span');
        span.className = 'word';
        span.setAttribute('aria-hidden', 'true');
        span.style.setProperty('--i', i);
        span.textContent = word;
        el.append(span, i < words.length - 1 ? ' ' : '');
      });
    });

    const targets = $$('[data-reveal], .split-words');
    if (reduced) {
      targets.forEach((el) => el.classList.add('is-revealed'));
      return;
    }

    // Stagger siblings inside a [data-reveal-group] automatically.
    $$('[data-reveal-group]').forEach((group) => {
      $$(':scope > [data-reveal]', group).forEach((child, i) => {
        if (!child.hasAttribute('data-reveal-delay')) child.style.setProperty('--reveal-delay', String(i % 8));
      });
    });

    // Anything already on screen animates in straight away, without waiting
    // for the first intersection callback; the rest reveal as they scroll in.
    targets.forEach((el) => whenVisible(el, (t) => t.classList.add('is-revealed'), { threshold: 0.12 }));
  }

  /* ---- Count-up numbers ---------------------------------------------- */

  function initCounters() {
    $$('[data-count]').forEach((el) => {
      const target = Number(el.dataset.count);
      const suffix = el.dataset.suffix || '';
      const format = (n) => Math.round(n).toLocaleString('en-NG') + suffix;
      if (reduced) { el.textContent = format(target); return; }
      el.textContent = format(0);
      whenVisible(el, () => {
        const start = performance.now();
        const duration = 1600;
        const tick = (now) => {
          const p = Math.min(1, (now - start) / duration);
          const eased = 1 - Math.pow(1 - p, 4);
          el.textContent = format(target * eased);
          if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
      }, { threshold: 0.6 });
    });
  }

  /* ---- Scroll path: the line fills and steps light up ----------------- */

  function initPaths() {
    $$('[data-path]').forEach((path) => {
      const steps = $$('.path__step', path);
      let ticking = false;
      const update = () => {
        const rect = path.getBoundingClientRect();
        const middle = window.innerHeight * 0.62;
        const progress = Math.min(1, Math.max(0, (middle - rect.top) / rect.height));
        path.style.setProperty('--path-progress', reduced ? 1 : progress.toFixed(3));
        steps.forEach((step) => {
          const r = step.getBoundingClientRect();
          step.classList.toggle('is-reached', reduced || r.top + 24 < middle);
        });
        ticking = false;
      };
      window.addEventListener('scroll', () => {
        if (!ticking) { requestAnimationFrame(update); ticking = true; }
      }, { passive: true });
      update();
    });
  }

  /* ---- CBT demo: answers questions, counts down, shows the score ------- */

  function initCbtDemo() {
    $$('[data-cbt-demo]').forEach((demo) => {
      const questions = JSON.parse(demo.dataset.questions || '[]');
      if (!questions.length) return;

      const qNum = $('[data-cbt-num]', demo);
      const qText = $('[data-cbt-question]', demo);
      const options = $('[data-cbt-options]', demo);
      const timer = $('[data-cbt-timer]', demo);
      const progress = $('[data-cbt-progress]', demo);
      const result = $('[data-cbt-result]', demo);
      const score = $('[data-cbt-score]', demo);
      const dots = $('[data-cbt-dots]', demo);
      let seconds = 30 * 60;
      let index = 0;
      let running = false;
      let clock;

      const renderDots = () => {
        dots.innerHTML = '';
        questions.forEach((_, i) => {
          const d = document.createElement('span');
          d.className = 'cbt-demo__dot' + (i < index ? ' is-done' : '') + (i === index ? ' is-current' : '');
          dots.appendChild(d);
        });
      };

      const render = () => {
        const q = questions[index];
        qNum.textContent = `Question ${index + 1} of ${questions.length}`;
        qText.textContent = q.q;
        options.innerHTML = '';
        q.options.forEach((text, i) => {
          const li = document.createElement('li');
          li.className = 'cbt-demo__option';
          const letter = document.createElement('span');
          letter.textContent = String.fromCharCode(65 + i);
          const label = document.createElement('span');
          label.textContent = text;
          li.append(letter, label);
          options.appendChild(li);
        });
        progress.style.width = ((index) / questions.length) * 100 + '%';
        renderDots();
      };

      const tickClock = () => {
        seconds = Math.max(0, seconds - 7);
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        timer.textContent = `${m}:${s}`;
      };

      const step = () => {
        if (!running) return;
        const q = questions[index];
        const items = $$('.cbt-demo__option', options);
        setTimeout(() => items[q.answer] && items[q.answer].classList.add('is-picked'), 900);
        setTimeout(() => {
          index++;
          if (index < questions.length) {
            demo.classList.add('is-switching');
            setTimeout(() => { render(); demo.classList.remove('is-switching'); step(); }, 280);
          } else {
            progress.style.width = '100%';
            renderDots();
            result.hidden = false;
            demo.classList.add('is-finished');
            animateNumber(score, 92, '%');
            setTimeout(restart, 5200);
          }
        }, 2100);
      };

      const restart = () => {
        index = 0;
        seconds = 30 * 60;
        result.hidden = true;
        demo.classList.remove('is-finished');
        render();
        step();
      };

      render();
      if (reduced) {
        result.hidden = false;
        score.textContent = '92%';
        return;
      }
      whenVisible(demo, () => {
        running = true;
        clock = setInterval(tickClock, 1000);
        step();
      });
      document.addEventListener('visibilitychange', () => {
        running = !document.hidden;
        if (running && !clock) clock = setInterval(tickClock, 1000);
      });
    });
  }

  function animateNumber(el, target, suffix = '') {
    if (reduced) { el.textContent = target + suffix; return; }
    const start = performance.now();
    const tick = (now) => {
      const p = Math.min(1, (now - start) / 1100);
      el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))) + suffix;
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }

  /* ---- Result sheet: scores fill in row by row, then bars grow -------- */

  function initResultSheets() {
    $$('[data-result-sheet]').forEach((sheet) => {
      const cells = $$('[data-score]', sheet);
      const grades = $$('[data-grade]', sheet);
      const bars = $$('[data-bar]', sheet);
      const fill = () => {
        cells.forEach((cell, i) => {
          setTimeout(() => {
            animateNumber(cell, Number(cell.dataset.score));
            cell.closest('tr').classList.add('is-filled');
          }, reduced ? 0 : i * 140);
        });
        grades.forEach((g, i) => setTimeout(() => g.classList.add('is-visible'), reduced ? 0 : 600 + i * 140));
        bars.forEach((bar, i) => setTimeout(() => bar.style.setProperty('--h', bar.dataset.bar + '%'), reduced ? 0 : 900 + i * 90));
        sheet.classList.add('is-filled');
      };
      whenVisible(sheet, fill, { threshold: 0.35 });
    });
  }

  /* ---- Typing code ---------------------------------------------------- */

  function initTyping() {
    $$('[data-typing]').forEach((el) => {
      const text = el.textContent;
      if (reduced) return;
      el.textContent = '';
      el.classList.add('is-typing');
      whenVisible(el, () => {
        let i = 0;
        const type = () => {
          el.textContent = text.slice(0, i++);
          if (i <= text.length) setTimeout(type, text[i - 1] === '\n' ? 140 : 22);
          else el.classList.remove('is-typing');
        };
        type();
      });
    });
  }

  /* ---- Gentle 3D tilt on device frames -------------------------------- */

  function initTilt() {
    if (reduced || !finePointer) return;
    $$('[data-tilt]').forEach((el) => {
      let frame;
      el.addEventListener('pointermove', (e) => {
        const r = el.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5;
        const y = (e.clientY - r.top) / r.height - 0.5;
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(() => {
          el.style.transform = `perspective(1000px) rotateY(${x * 8}deg) rotateX(${-y * 6}deg)`;
        });
      });
      el.addEventListener('pointerleave', () => {
        cancelAnimationFrame(frame);
        el.style.transform = '';
      });
    });
  }

  /* ---- Magnetic buttons ----------------------------------------------- */

  function initMagnetic() {
    if (reduced || !finePointer) return;
    $$('[data-magnetic]').forEach((el) => {
      el.addEventListener('pointermove', (e) => {
        const r = el.getBoundingClientRect();
        const x = e.clientX - r.left - r.width / 2;
        const y = e.clientY - r.top - r.height / 2;
        el.style.transform = `translate(${x * 0.15}px, ${y * 0.25}px)`;
      });
      el.addEventListener('pointerleave', () => { el.style.transform = ''; });
    });
  }

  /* ---- Hero network: links draw in, nodes pop, then pulses travel ----- */

  function initHeroNet() {
    const net = $('[data-hero-net]');
    if (!net) return;
    if (reduced) { net.classList.add('is-live'); return; }
    requestAnimationFrame(() => setTimeout(() => net.classList.add('is-live'), 200));

    // Highlight the node that matches the hovered audience card.
    $$('[data-audience]').forEach((card) => {
      const node = $(`[data-node="${card.dataset.audience}"]`, net);
      if (!node) return;
      card.addEventListener('mouseenter', () => node.classList.add('is-hot'));
      card.addEventListener('mouseleave', () => node.classList.remove('is-hot'));
    });

    if (!finePointer) return;
    const hero = net.closest('section');
    hero.addEventListener('pointermove', (e) => {
      const r = hero.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      net.style.setProperty('--px', (x * 18).toFixed(1) + 'px');
      net.style.setProperty('--py', (y * 14).toFixed(1) + 'px');
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initReveal();
    initCounters();
    initPaths();
    initCbtDemo();
    initResultSheets();
    initTyping();
    initTilt();
    initMagnetic();
    initHeroNet();
  });
})();
