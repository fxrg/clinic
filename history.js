/**
 * history.js — Appointment History Page (localStorage version)
 * Clinic Scholar | DS362 Web Programming Project
 *
 * Works without a server — reads/writes data from localStorage.
 * PHP files (history.php, update_appointment.php, etc.) handle the
 * server-side MySQL CRUD when accessed via XAMPP.
 */

'use strict';

// ── LocalStorage Keys ──────────────────────────────────────────
const KEY_USER  = 'clinic_user';         // {id, name, email}
const KEY_APTS  = 'clinic_appointments'; // array of appointment objects

// ── Active state for delete/reschedule ────────────────────────
let activeRescheduleId = null;
let activeDeleteId     = null;

// ── Helpers ────────────────────────────────────────────────────
function getUser()         { try { return JSON.parse(localStorage.getItem(KEY_USER))  || null; } catch(e){ return null; } }
function getAppointments() { try { return JSON.parse(localStorage.getItem(KEY_APTS)) || []; }  catch(e){ return []; } }
function saveAppointments(apts) { localStorage.setItem(KEY_APTS, JSON.stringify(apts)); }

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const p = dateStr.split('-');
    const d = new Date(+p[0], +p[1]-1, +p[2]);
    return d.toLocaleDateString('en-US', { weekday:'short', year:'numeric', month:'short', day:'numeric' });
}

function formatTime(timeStr) {
    if (!timeStr) return '—';
    const parts = timeStr.split(':');
    const h = parseInt(parts[0]), m = parts[1];
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return `${h12}:${m} ${ampm}`;
}

function getTodayISO() { return new Date().toISOString().split('T')[0]; }

function statusBarClass(status) {
    return { Confirmed:'confirmed', Pending:'pending', Completed:'completed', Cancelled:'cancelled' }[status] || 'pending';
}

// ── Toast notification ─────────────────────────────────────────
function showToast(msg, type) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = msg;
    toast.className = 'toast ' + (type === 'error' ? 'toast-error' : 'toast-success');
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '0.5rem';
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(1.5rem)';
        setTimeout(function() { toast.style.display = 'none'; }, 350);
    }, 4000);
}

// ── Modal helpers ──────────────────────────────────────────────
window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
};

// ── Logout ─────────────────────────────────────────────────────
window.handleLogout = function() {
    localStorage.removeItem(KEY_USER);
    window.location.href = 'index.html';
};

// ── Render Sidebar ─────────────────────────────────────────────
function renderSidebar(apts, total, upcomingCount) {
    const sidebar = document.getElementById('sidebar-content');
    if (!sidebar) return;

    const today   = getTodayISO();
    const upcoming = apts.filter(a =>
        ['Confirmed','Pending'].includes(a.status) && a.appointment_date >= today
    ).sort((a,b) => a.appointment_date.localeCompare(b.appointment_date));
    const nextApt = upcoming[0] || null;

    let html = '';

    // Next Appointment Card
    if (nextApt) {
        html += `
        <div style="background:linear-gradient(135deg,var(--primary),var(--primary-dim)); color:var(--on-primary); padding:2rem; border-radius:1rem; box-shadow:0 8px 24px rgba(17,92,185,0.3); position:relative; overflow:hidden;">
          <div style="position:absolute; right:-3rem; top:-3rem; width:12rem; height:12rem; background:rgba(255,255,255,0.08); border-radius:50%;"></div>
          <div style="position:relative; z-index:1;">
            <span style="font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.15em; opacity:0.7; display:block; margin-bottom:0.75rem;">Upcoming Priority</span>
            <h2 style="font-size:1.25rem; font-weight:800; margin-bottom:1.25rem;">${nextApt.specialty}</h2>
            <div style="display:flex; flex-direction:column; gap:0.75rem; font-size:0.875rem;">
              <div style="display:flex; align-items:center; gap:0.75rem;">
                <span class="material-symbols-outlined" style="opacity:0.7;">person</span>
                <span style="font-weight:600;">${nextApt.doctor_name}</span>
              </div>
              <div style="display:flex; align-items:center; gap:0.75rem;">
                <span class="material-symbols-outlined" style="opacity:0.7;">schedule</span>
                <span>${formatDate(nextApt.appointment_date)} &bull; ${formatTime(nextApt.appointment_time)}</span>
              </div>
            </div>
            <button onclick="openReschedule('${nextApt.id}')"
              style="margin-top:1.5rem; width:100%; background:#fff; color:var(--primary); border:none; padding:0.875rem; border-radius:0.75rem; font-weight:800; font-size:0.875rem; cursor:pointer;">
              Reschedule
            </button>
          </div>
        </div>`;
    } else {
        html += `
        <div style="background:var(--surface-container-low); padding:2rem; border-radius:1rem; text-align:center;">
          <span class="material-symbols-outlined" style="font-size:3rem; color:var(--outline-variant); display:block; margin-bottom:0.75rem;">event_busy</span>
          <h2 style="font-size:1rem; font-weight:700; margin-bottom:0.5rem;">No Upcoming Appointments</h2>
          <p style="font-size:0.8rem; color:var(--on-surface-variant); margin-bottom:1.25rem;">You have no scheduled visits.</p>
          <a href="book.html" class="btn btn-primary btn-sm" style="display:inline-flex;">Book Now</a>
        </div>`;
    }

    // Patient Summary
    const latestDoc = apts.length ? apts[0].doctor_name : 'No records yet';
    html += `
    <div style="background:var(--surface-container-low); padding:2rem; border-radius:1rem;">
      <span style="font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.15em; color:var(--on-surface-variant); display:block; margin-bottom:1.5rem;">Patient Summary</span>
      <div style="display:flex; flex-direction:column; gap:1rem;">
        <div style="display:flex; justify-content:space-between;">
          <span style="color:var(--on-surface-variant); font-size:0.875rem;">Total Visits</span>
          <span style="color:var(--primary); font-weight:900; font-size:1.25rem;">${total}</span>
        </div>
        <div class="progress-track">
          <div class="progress-fill" style="width:${Math.min(100, total * 12)}%;"></div>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:var(--on-surface-variant); font-size:0.875rem;">Upcoming</span>
          <span style="color:var(--tertiary); font-weight:900; font-size:1.25rem;">${upcomingCount}</span>
        </div>
        <div style="padding-top:1rem; border-top:1px solid var(--surface-container); display:flex; flex-direction:column; gap:1rem;">
          <div style="display:flex; align-items:center; gap:1rem;">
            <div style="padding:0.5rem; background:var(--tertiary-container); border-radius:0.5rem;">
              <span class="material-symbols-outlined" style="color:var(--on-tertiary-container); font-size:1.25rem;">receipt_long</span>
            </div>
            <div>
              <p style="font-size:0.875rem; font-weight:700;">Latest Record</p>
              <p style="font-size:0.75rem; color:var(--on-surface-variant);">${latestDoc}</p>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:1rem;">
            <div style="padding:0.5rem; background:var(--secondary-container); border-radius:0.5rem;">
              <span class="material-symbols-outlined" style="color:var(--on-secondary-container); font-size:1.25rem;">shield_person</span>
            </div>
            <div>
              <p style="font-size:0.875rem; font-weight:700;">Account Status</p>
              <p style="font-size:0.75rem; color:var(--on-surface-variant);">Active scholar member</p>
            </div>
          </div>
        </div>
      </div>
    </div>`;

    sidebar.innerHTML = html;
}

// ── Render Table Rows ──────────────────────────────────────────
function renderTable(apts) {
    const tbody = document.getElementById('appointmentsTableBody');
    if (!tbody) return;

    const today = getTodayISO();

    if (apts.length === 0) {
        tbody.innerHTML = `
        <tr>
          <td colspan="5" style="padding:4rem; text-align:center; color:var(--on-surface-variant);">
            <span class="material-symbols-outlined" style="font-size:3rem; display:block; margin-bottom:0.75rem; color:var(--outline-variant);">event_note</span>
            No appointments yet. <a href="book.html" style="color:var(--primary); font-weight:700;">Book one now &rarr;</a>
          </td>
        </tr>`;
        return;
    }

    tbody.innerHTML = apts.map(apt => {
        const barClass = statusBarClass(apt.status);
        const isPast   = apt.appointment_date < today || ['Completed','Cancelled'].includes(apt.status);
        const rescheduleBtn = !isPast ? `
            <button onclick="openReschedule('${apt.id}')"
              style="padding:0.4rem 0.6rem; background:var(--primary-container); color:var(--on-primary-container); border:none; border-radius:0.375rem; cursor:pointer; display:flex; align-items:center;" title="Reschedule">
              <span class="material-symbols-outlined" style="font-size:1rem;">edit_calendar</span>
            </button>` : '';
        const notes = apt.notes
            ? `<span style="font-size:0.75rem; color:var(--on-surface-variant); font-style:italic; display:block; max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${apt.notes}">${apt.notes}</span>`
            : `<span style="font-size:0.75rem; color:var(--outline);">—</span>`;

        return `
        <tr data-status="${apt.status}" style="opacity:${isPast ? '0.72' : '1'};">
          <td>
            <div style="font-weight:700; font-size:0.875rem;">${apt.doctor_name}</div>
            <div style="font-size:0.75rem; color:var(--on-surface-variant);">${apt.specialty}</div>
          </td>
          <td>
            <div style="font-weight:700; font-size:0.875rem;">${formatDate(apt.appointment_date)}</div>
            <div style="font-size:0.75rem; color:var(--on-surface-variant);">${formatTime(apt.appointment_time)}</div>
          </td>
          <td>${notes}</td>
          <td>
            <div class="status-pill">
              <div class="status-bar ${barClass}"></div>
              <span class="status-text ${barClass}">${apt.status}</span>
            </div>
          </td>
          <td style="text-align:right;">
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
              ${rescheduleBtn}
              <button onclick="openDeleteModal('${apt.id}', '${apt.doctor_name.replace(/'/g,"&#39;")}')"
                style="padding:0.4rem 0.6rem; background:#fef2f2; color:#dc2626; border:none; border-radius:0.375rem; cursor:pointer; display:flex; align-items:center;" title="Cancel/Delete">
                <span class="material-symbols-outlined" style="font-size:1rem;">delete</span>
              </button>
            </div>
          </td>
        </tr>`;
    }).join('');
}

// ── Render Everything ──────────────────────────────────────────
function renderPage() {
    const user = getUser();
    const apts = getAppointments();
    const today = getTodayISO();

    // Update nav user name
    const navName = document.getElementById('nav-user-name');
    if (navName) navName.textContent = user ? ('👋 ' + user.name) : '';

    // Stats
    const upcoming = apts.filter(a =>
        ['Confirmed','Pending'].includes(a.status) && a.appointment_date >= today
    );
    document.getElementById('stat-total').textContent    = apts.length;
    document.getElementById('stat-upcoming').textContent = upcoming.length;
    document.getElementById('footer-count').textContent  =
        `Showing ${apts.length} record${apts.length !== 1 ? 's' : ''}`;

    renderSidebar(apts, apts.length, upcoming.length);
    renderTable(apts);
    attachTabListeners();
}

// ── Status Filter Tabs ─────────────────────────────────────────
function attachTabListeners() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const filter = this.dataset.status;
            tabBtns.forEach(b => {
                b.classList.remove('chip-active');
                b.classList.add('chip-default');
            });
            this.classList.remove('chip-default');
            this.classList.add('chip-active');

            document.querySelectorAll('#appointmentsTableBody tr[data-status]').forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.classList.remove('row-hidden');
                } else {
                    row.classList.add('row-hidden');
                }
            });
        });
    });
}

// ── Reschedule ─────────────────────────────────────────────────
window.openReschedule = function(id) {
    const apts = getAppointments();
    const apt  = apts.find(a => a.id === id);
    if (!apt) return;
    activeRescheduleId = id;
    document.getElementById('new-date').value = apt.appointment_date;
    document.getElementById('new-date').setAttribute('min', getTodayISO());
    // Set time select
    const timeSelect = document.getElementById('new-time');
    for (let i = 0; i < timeSelect.options.length; i++) {
        if (timeSelect.options[i].value === apt.appointment_time) {
            timeSelect.selectedIndex = i; break;
        }
    }
    document.getElementById('rescheduleModal').classList.add('active');
};

window.saveReschedule = function() {
    const newDate = document.getElementById('new-date').value;
    const newTime = document.getElementById('new-time').value;
    const today   = getTodayISO();

    if (!newDate) { alert('Please select a new date.'); return; }
    if (newDate < today) { alert('Date cannot be in the past.'); return; }
    if (!newTime) { alert('Please select a new time.'); return; }

    const apts = getAppointments();
    const idx  = apts.findIndex(a => a.id === activeRescheduleId);
    if (idx === -1) { alert('Appointment not found.'); return; }

    apts[idx].appointment_date = newDate;
    apts[idx].appointment_time = newTime;
    apts[idx].status = 'Confirmed';
    saveAppointments(apts);

    closeModal('rescheduleModal');
    showToast('Appointment rescheduled successfully!', 'success');
    renderPage();
};

// ── Delete ─────────────────────────────────────────────────────
window.openDeleteModal = function(id, doctorName) {
    activeDeleteId = id;
    const nameEl = document.getElementById('delete-doctor-name');
    if (nameEl) nameEl.textContent = doctorName;
    document.getElementById('deleteModal').classList.add('active');
};

window.confirmDelete = function() {
    const apts     = getAppointments();
    const filtered = apts.filter(a => a.id !== activeDeleteId);
    saveAppointments(filtered);
    closeModal('deleteModal');
    showToast('Appointment cancelled and removed.', 'success');
    renderPage();
};

// ── Close modals on backdrop click / Escape ────────────────────
document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
    }
});

// ── Flash message from book page ───────────────────────────────
function checkFlash() {
    const msg = localStorage.getItem('clinic_flash');
    if (msg) {
        localStorage.removeItem('clinic_flash');
        showToast(msg, 'success');
    }
}

// ── Auth guard ─────────────────────────────────────────────────
function authGuard() {
    const user = getUser();
    if (!user) {
        window.location.href = 'auth.html#login';
        return false;
    }
    return true;
}

// ── Init ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    if (!authGuard()) return;
    renderPage();
    checkFlash();
});
