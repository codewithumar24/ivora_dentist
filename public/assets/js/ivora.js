/* Ivora Dental Studio — motion + booking behaviour.
   Everything degrades gracefully: no library, no breakage. */
(function () {
  'use strict';

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---- Scroll reveals ---------------------------------------------------- */
  const revealables = document.querySelectorAll('.reveal');
  if (revealables.length) {
    if (reduced || !('IntersectionObserver' in window)) {
      revealables.forEach((el) => el.classList.add('is-in'));
    } else {
      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry, i) => {
            if (!entry.isIntersecting) return;
            const delay = parseInt(entry.target.dataset.delay || i * 70, 10);
            setTimeout(() => entry.target.classList.add('is-in'), delay);
            io.unobserve(entry.target);
          });
        },
        { threshold: 0.12, rootMargin: '0px 0px -60px' }
      );
      revealables.forEach((el) => io.observe(el));
    }
  }

  /* ---- Count-up figures -------------------------------------------------- */
  document.querySelectorAll('[data-count]').forEach((el) => {
    const target = parseFloat(el.dataset.count);
    if (reduced) { el.textContent = target.toLocaleString(); return; }

    const run = () => {
      const duration = 1500;
      const start = performance.now();
      const tick = (now) => {
        const p = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased).toLocaleString();
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    };

    const io = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) { run(); io.disconnect(); }
    }, { threshold: 0.5 });
    io.observe(el);
  });

  /* ---- Header state on scroll ------------------------------------------- */
  const header = document.querySelector('[data-header]');
  if (header) {
    let last = 0;
    const onScroll = () => {
      const y = window.scrollY;
      header.classList.toggle('is-stuck', y > 24);
      if (y > 400 && y > last) header.classList.add('is-hidden');
      else header.classList.remove('is-hidden');
      last = y;
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---- Mobile navigation -------------------------------------------------- */
  const navToggle = document.querySelector('[data-nav-toggle]');
  const navPanel = document.querySelector('[data-nav-panel]');
  if (navToggle && navPanel) {
    navToggle.addEventListener('click', () => {
      const open = navPanel.classList.toggle('hidden') === false;
      navToggle.setAttribute('aria-expanded', String(open));
      document.body.style.overflow = open ? 'hidden' : '';
    });
  }

  /* ---- Accordions (FAQ) --------------------------------------------------- */
  document.querySelectorAll('[data-accordion] > [data-accordion-item]').forEach((item) => {
    const trigger = item.querySelector('[data-accordion-trigger]');
    const panel = item.querySelector('[data-accordion-panel]');
    if (!trigger || !panel) return;

    trigger.addEventListener('click', () => {
      const open = item.classList.toggle('is-open');
      panel.style.maxHeight = open ? panel.scrollHeight + 'px' : '0px';
      trigger.setAttribute('aria-expanded', String(open));
    });
  });

  /* ---- Tabs --------------------------------------------------------------- */
  document.querySelectorAll('[data-tabs]').forEach((group) => {
    const triggers = group.querySelectorAll('[data-tab]');
    triggers.forEach((trigger) => {
      trigger.addEventListener('click', () => {
        const name = trigger.dataset.tab;
        triggers.forEach((t) => t.classList.toggle('is-active', t === trigger));
        group.querySelectorAll('[data-tab-panel]').forEach((panel) => {
          panel.classList.toggle('hidden', panel.dataset.tabPanel !== name);
        });
      });
    });
  });

  /* ---- Booking: day strip + slot picker ---------------------------------- */
  const booking = document.querySelector('[data-booking]');
  if (booking) {
    const doctorField = booking.querySelector('[name="doctor_id"]');
    const dateField = booking.querySelector('[name="scheduled_on"]');
    const timeField = booking.querySelector('[name="starts_at"]');
    const dayStrip = booking.querySelector('[data-days]');
    const slotBox = booking.querySelector('[data-slots]');
    const summary = booking.querySelector('[data-slot-summary]');
    const slotsUrl = booking.dataset.slotsUrl;
    const daysUrl = booking.dataset.daysUrl;

    const loadDays = async () => {
      if (!doctorField || !doctorField.value || !dayStrip) return;
      dayStrip.innerHTML = '<span class="text-sm text-pine/50 py-3">Checking the diary…</span>';

      try {
        const res = await fetch(`${daysUrl}?doctor_id=${doctorField.value}`, { headers: { Accept: 'application/json' } });
        const days = await res.json();

        dayStrip.innerHTML = '';
        days.forEach((day) => {
          const chip = document.createElement('button');
          chip.type = 'button';
          chip.className = 'day-chip' + (day.open === 0 ? ' is-closed' : '');
          chip.disabled = day.open === 0;
          chip.dataset.date = day.date;
          chip.innerHTML = `
            <span class="block text-[10px] uppercase tracking-widest opacity-60">${day.weekday}</span>
            <span class="block text-lg font-semibold leading-tight">${day.day}</span>
            <span class="block text-[10px] opacity-60">${day.open ? day.open + ' open' : 'closed'}</span>`;
          chip.addEventListener('click', () => selectDay(chip, day.date));
          dayStrip.appendChild(chip);
        });

        const firstOpen = dayStrip.querySelector('.day-chip:not(.is-closed)');
        if (firstOpen) firstOpen.click();
      } catch (e) {
        dayStrip.innerHTML = '<span class="text-sm text-rose-600 py-3">Could not load the diary. Refresh and try again.</span>';
      }
    };

    const selectDay = (chip, date) => {
      dayStrip.querySelectorAll('.day-chip').forEach((c) => c.classList.remove('is-active'));
      chip.classList.add('is-active');
      if (dateField) dateField.value = date;
      loadSlots(date);
    };

    const loadSlots = async (date) => {
      if (!slotBox) return;
      slotBox.innerHTML = '<p class="text-sm text-pine/50">Loading times…</p>';
      if (timeField) timeField.value = '';
      if (summary) summary.textContent = '';

      try {
        const res = await fetch(`${slotsUrl}?doctor_id=${doctorField.value}&date=${date}`, { headers: { Accept: 'application/json' } });
        const data = await res.json();

        if (!data.count) {
          slotBox.innerHTML = '<p class="text-sm text-pine/60">Nothing free that day. Try another date, or call the front desk and we will find something.</p>';
          return;
        }

        slotBox.innerHTML = '';
        Object.entries(data.slots).forEach(([period, slots]) => {
          const group = document.createElement('div');
          group.className = 'mb-4';
          group.innerHTML = `<p class="eyebrow text-pine/50 mb-2">${period}</p>`;

          const row = document.createElement('div');
          row.className = 'slot-arch';

          slots.forEach((slot) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'slot';
            btn.textContent = slot.label;
            btn.addEventListener('click', () => {
              slotBox.querySelectorAll('.slot').forEach((s) => s.classList.remove('is-active'));
              btn.classList.add('is-active');
              if (timeField) timeField.value = slot.value;
              if (summary) summary.textContent = `${data.date} at ${slot.label}`;
            });
            row.appendChild(btn);
          });

          group.appendChild(row);
          slotBox.appendChild(group);
        });
      } catch (e) {
        slotBox.innerHTML = '<p class="text-sm text-rose-600">Could not load times. Refresh and try again.</p>';
      }
    };

    if (doctorField) {
      doctorField.addEventListener('change', loadDays);
      if (doctorField.value) loadDays();
    }
  }

  /* ---- Console sidebar (mobile) ------------------------------------------ */
  const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
  const sidebar = document.querySelector('[data-sidebar]');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('-translate-x-full'));
  }

  /* ---- Dropdowns ---------------------------------------------------------- */
  document.querySelectorAll('[data-dropdown]').forEach((wrapper) => {
    const trigger = wrapper.querySelector('[data-dropdown-trigger]');
    const menu = wrapper.querySelector('[data-dropdown-menu]');
    if (!trigger || !menu) return;

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      document.querySelectorAll('[data-dropdown-menu]').forEach((m) => { if (m !== menu) m.classList.add('hidden'); });
      menu.classList.toggle('hidden');
    });
  });
  document.addEventListener('click', () => {
    document.querySelectorAll('[data-dropdown-menu]').forEach((m) => m.classList.add('hidden'));
  });

  /* ---- Modals ------------------------------------------------------------- */
  document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const modal = document.getElementById(trigger.dataset.modalOpen);
      if (modal) { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    });
  });
  document.querySelectorAll('[data-modal-close]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      trigger.closest('[data-modal]')?.classList.add('hidden');
      document.body.style.overflow = '';
    });
  });

  /* ---- Auto-dismiss flash messages --------------------------------------- */
  document.querySelectorAll('[data-flash]').forEach((flash) => {
    setTimeout(() => {
      flash.style.transition = 'opacity .5s, transform .5s';
      flash.style.opacity = '0';
      flash.style.transform = 'translateY(-8px)';
      setTimeout(() => flash.remove(), 500);
    }, 6000);
  });

  /* ---- Confirm destructive actions --------------------------------------- */
  document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      if (!window.confirm(form.dataset.confirm)) e.preventDefault();
    });
  });

  /* ---- Repeatable rows (invoice lines, prescriptions) -------------------- */
  document.querySelectorAll('[data-repeater]').forEach((repeater) => {
    const list = repeater.querySelector('[data-repeater-list]');
    const template = repeater.querySelector('template');
    const addBtn = repeater.querySelector('[data-repeater-add]');
    if (!list || !template || !addBtn) return;

    let index = list.children.length;

    addBtn.addEventListener('click', () => {
      const html = template.innerHTML.replace(/__INDEX__/g, index++);
      const wrapper = document.createElement('div');
      wrapper.innerHTML = html;
      const row = wrapper.firstElementChild;
      list.appendChild(row);
      row.querySelector('[data-repeater-remove]')?.addEventListener('click', () => row.remove());
    });

    list.querySelectorAll('[data-repeater-remove]').forEach((btn) => {
      btn.addEventListener('click', () => btn.closest('[data-repeater-row]')?.remove());
    });
  });
})();
