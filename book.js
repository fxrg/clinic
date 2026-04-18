/**
 * book.js — Book Appointment Page JavaScript
 * Clinic Scholar | DS362 Web Programming Project
 *
 * Works without a server — saves appointments to localStorage.
 * PHP file (book_appointment.php) handles MySQL INSERT when via XAMPP.
 *
 * Flow:
 *  1. Pre-fill doctor/specialty from URL params (from doctors.html)
 *  2. Auto-fill specialty dropdown
 *  3. Validate form → show CONFIRMATION MODAL with full summary
 *  4. "Confirm My Booking" → save to localStorage → redirect to history.html
 */

'use strict';

// ── Doctor → Specialty Map ────────────────────────────────────
const DOCTOR_SPECIALTIES = {
    'Dr. Abdullah Al-Mutairi': 'Cardiovascular Science',
    'Dr. Sarah Al-Otaibi':     'Neurological Disorders',
    'Dr. Ahmad Al-Harbi':      'Pediatric Surgery',
    'Dr. Khalid Al-Anazi':     'Endocrinology',
    'Dr. Noura Al-Qahtani':    'Sports Medicine',
    'Dr. Fawzia Al-Zahrani':   'Dermatology'
};

// ── Time labels ───────────────────────────────────────────────
const TIME_LABELS = {
    '09:00:00':'09:00 AM', '09:30:00':'09:30 AM',
    '10:00:00':'10:00 AM', '10:30:00':'10:30 AM',
    '11:00:00':'11:00 AM', '11:30:00':'11:30 AM',
    '14:00:00':'02:00 PM', '14:30:00':'02:30 PM',
    '15:00:00':'03:00 PM', '15:30:00':'03:30 PM',
    '16:00:00':'04:00 PM'
};

// ── LocalStorage Keys ─────────────────────────────────────────
const KEY_APTS  = 'clinic_appointments';
const KEY_USER  = 'clinic_user';

function getParam(name) { return new URLSearchParams(window.location.search).get(name); }
function getTodayISO()   { return new Date().toISOString().split('T')[0]; }

function formatDateFull(dateStr) {
    if (!dateStr) return '—';
    const p = dateStr.split('-');
    const d = new Date(+p[0], +p[1]-1, +p[2]);
    return d.toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
}

function getAppointments() {
    try { return JSON.parse(localStorage.getItem(KEY_APTS)) || []; }
    catch(e) { return []; }
}

// ── DOM Ready ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {

    // ── Auth Guard: require login to book ──────────────────────
    const currentUser = (function() {
        try { return JSON.parse(localStorage.getItem(KEY_USER)); }
        catch(e) { return null; }
    })();
    if (!currentUser) {
        window.location.href = 'auth.html#login';
        return;
    }

    // ── Update navbar to show logged-in state ──────────────────
    const navActions = document.querySelector('.navbar-actions');
    if (navActions && currentUser) {
        navActions.innerHTML =
            '<span style="font-size:0.875rem; color:var(--on-surface-variant);">\uD83D\uDC4B ' + currentUser.name + '</span>' +
            '<button onclick="(function(){ localStorage.removeItem(\'clinic_user\'); window.location.href=\'index.html\'; })()" class="btn btn-outline btn-sm">Logout</button>';
    }

    const doctorSelect     = document.getElementById('doctor-select');
    const specialtyDisplay = document.getElementById('specialty-display');
    const dateInput        = document.getElementById('appt-date');
    const timeSelect       = document.getElementById('appt-time');
    const notesInput       = document.getElementById('appt-notes');
    const bookForm         = document.getElementById('bookForm');
    const confirmModal     = document.getElementById('confirmModal');
    const finalConfirmBtn  = document.getElementById('finalConfirmBtn');

    // Set minimum date to today
    if (dateInput) dateInput.setAttribute('min', getTodayISO());

    // ── Pre-fill from URL params ───────────────────────────────
    const urlDoctor    = getParam('doctor');
    const urlSpecialty = getParam('specialty');

    if (urlDoctor && doctorSelect) {
        for (let i = 0; i < doctorSelect.options.length; i++) {
            if (doctorSelect.options[i].value === urlDoctor) {
                doctorSelect.selectedIndex = i; break;
            }
        }
    }
    if (urlSpecialty && specialtyDisplay) {
        specialtyDisplay.value = urlSpecialty;
    } else if (urlDoctor && specialtyDisplay) {
        specialtyDisplay.value = DOCTOR_SPECIALTIES[urlDoctor] || '';
    }

    // ── Auto-fill specialty when doctor changes ────────────────
    if (doctorSelect && specialtyDisplay) {
        doctorSelect.addEventListener('change', function() {
            specialtyDisplay.value = DOCTOR_SPECIALTIES[this.value] || '';
        });
    }

    // ── Form Submit: Validate → Show Confirmation Modal ────────
    if (bookForm) {
        bookForm.addEventListener('submit', function(e) {
            e.preventDefault();  // Always prevent — show modal first

            const doctor = doctorSelect   ? doctorSelect.value   : '';
            const date   = dateInput      ? dateInput.value      : '';
            const time   = timeSelect     ? timeSelect.value     : '';
            const today  = getTodayISO();
            const errors = [];

            // Client-side validation (required by rubric)
            if (!doctor) errors.push('Please select a doctor from the list.');
            if (!date)   errors.push('Appointment date is required.');
            else if (date < today) errors.push('Appointment date cannot be in the past.');
            if (!time)   errors.push('Please select an appointment time.');

            if (errors.length > 0) {
                alert('⚠️  Booking Validation Failed\n──────────────────────────\n' +
                    errors.map((e,i) => (i+1) + '. ' + e).join('\n'));
                return;
            }

            // ── Populate confirmation modal ────────────────────
            const specialty = specialtyDisplay ? specialtyDisplay.value : (DOCTOR_SPECIALTIES[doctor] || '');
            const notes     = notesInput ? notesInput.value.trim() : '';

            document.getElementById('conf-doctor').textContent    = doctor;
            document.getElementById('conf-specialty').textContent = specialty;
            document.getElementById('conf-date').textContent      = formatDateFull(date);
            document.getElementById('conf-time').textContent      = TIME_LABELS[time] || time;
            const notesEl = document.getElementById('conf-notes');
            if (notesEl) {
                notesEl.textContent  = notes || 'No additional notes.';
                notesEl.style.fontStyle = notes ? 'normal' : 'italic';
            }

            // Show confirmation modal
            if (confirmModal) confirmModal.classList.add('active');
        });
    }

    // ── "Confirm My Booking" → Save to localStorage → Redirect ─
    if (finalConfirmBtn) {
        finalConfirmBtn.addEventListener('click', function() {

            const doctor    = doctorSelect   ? doctorSelect.value   : '';
            const specialty = specialtyDisplay ? specialtyDisplay.value : '';
            const date      = dateInput      ? dateInput.value      : '';
            const time      = timeSelect     ? timeSelect.value     : '';
            const notes     = notesInput     ? notesInput.value.trim() : '';

            // Get current user
            let userId = 'guest', userName = 'Guest';
            try {
                const u = JSON.parse(localStorage.getItem(KEY_USER));
                if (u) { userId = u.id; userName = u.name; }
            } catch(e) {}

            // Build appointment object
            const appointment = {
                id:               String(Date.now()),
                user_id:          userId,
                doctor_name:      doctor,
                specialty:        specialty,
                appointment_date: date,
                appointment_time: time,
                notes:            notes,
                status:           'Pending',
                created_at:       new Date().toISOString()
            };

            // Save to localStorage
            const apts = getAppointments();
            apts.unshift(appointment); // Add to beginning
            localStorage.setItem(KEY_APTS, JSON.stringify(apts));

            // Set flash message
            localStorage.setItem('clinic_flash',
                'Your appointment with ' + doctor + ' has been booked successfully!');

            // Show loading on button
            this.textContent = 'Booking...';
            this.disabled = true;

            // Redirect to history.html
            window.location.href = 'history.html';
        });
    }

    // ── Close modal on backdrop click / Escape ─────────────────
    if (confirmModal) {
        confirmModal.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('active');
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && confirmModal) confirmModal.classList.remove('active');
    });

});
