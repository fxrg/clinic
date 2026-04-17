/**
 * auth.js — Register & Login Page JavaScript
 * Clinic Scholar | DS362 Web Programming Project
 *
 * Works without a server — uses localStorage.
 * PHP files (register.php, login.php) handle MySQL when accessed via XAMPP.
 *
 * Flow:
 *  Register: validate → save user to localStorage → go to history.html
 *  Login:    validate → check localStorage users → go to history.html
 */

'use strict';

// ── LocalStorage Keys ──────────────────────────────────────────
const KEY_USERS = 'clinic_users'; // array of registered user objects
const KEY_USER  = 'clinic_user';  // current logged-in user

// ── Utility: Read URL query parameter ────────────────────────
function getParam(name) {
    return new URLSearchParams(window.location.search).get(name);
}

// ── Get / Save users ──────────────────────────────────────────
function getUsers() {
    try { return JSON.parse(localStorage.getItem(KEY_USERS)) || []; }
    catch(e) { return []; }
}
function saveUsers(users) { localStorage.setItem(KEY_USERS, JSON.stringify(users)); }
function setCurrentUser(user) { localStorage.setItem(KEY_USER, JSON.stringify(user)); }

// ── Show Error Box ─────────────────────────────────────────────
function showErrors(boxId, listId, errors) {
    const box  = document.getElementById(boxId);
    const list = document.getElementById(listId);
    if (!box || !list) return;
    list.innerHTML = '';
    errors.forEach(function(msg) {
        const li = document.createElement('li');
        li.textContent = msg;
        list.appendChild(li);
    });
    box.style.display = 'block';
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
function hideErrors(boxId) {
    const box = document.getElementById(boxId);
    if (box) box.style.display = 'none';
}

// ── DOM Ready ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {

    // ── Seed a demo user if no users exist ────────────────────
    const existingUsers = getUsers();
    if (existingUsers.length === 0) {
        saveUsers([{
            id: 1,
            name:  'Demo Patient',
            email: 'demo@clinic.edu',
            password: 'Password123'
        }]);
    }

    // ── Scroll to correct section ──────────────────────────────
    const hash = window.location.hash;
    if (hash === '#register') {
        const sec = document.getElementById('register-section');
        if (sec) sec.scrollIntoView({ behavior: 'smooth' });
    }

    // ── Mobile: show register link inside login panel ──────────
    if (window.innerWidth < 1024) {
        const ml = document.getElementById('mobile-register-link');
        if (ml) ml.style.display = 'block';
    }

    // ══════════════════════════════════════════════════════════
    //  REGISTER FORM
    // ══════════════════════════════════════════════════════════
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();           // ALWAYS prevent – we handle with JS
            hideErrors('register-error-box');

            const name    = document.getElementById('reg-name').value.trim();
            const email   = document.getElementById('reg-email').value.trim().toLowerCase();
            const pass    = document.getElementById('reg-pass').value;
            const confirm = document.getElementById('reg-confirm').value;
            const errors  = [];

            // ── Client-side validation (popup alert, rubric requirement) ──
            if (name.length < 2)
                errors.push('Full name must be at least 2 characters.');
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
                errors.push('A valid email address is required.');
            if (pass.length < 8)
                errors.push('Password must be at least 8 characters.');
            if (!/[A-Z]/.test(pass))
                errors.push('Password must contain at least one uppercase letter.');
            if (pass !== confirm)
                errors.push('Passwords do not match.');

            if (errors.length > 0) {
                alert('⚠️  Registration Validation Failed\n─────────────────────────────\n' +
                    errors.map((e,i) => (i+1) + '. ' + e).join('\n'));
                return;
            }

            // ── Check duplicate email ──────────────────────────
            const users = getUsers();
            if (users.find(u => u.email === email)) {
                showErrors('register-error-box', 'register-error-list',
                    ['This email is already registered. Please login instead.']);
                return;
            }

            // ── Save new user ──────────────────────────────────
            const newUser = {
                id:       Date.now(),
                name:     name,
                email:    email,
                password: pass
            };
            users.push(newUser);
            saveUsers(users);

            // ── Set session & redirect ─────────────────────────
            setCurrentUser({ id: newUser.id, name: newUser.name, email: newUser.email });
            localStorage.setItem('clinic_flash', 'Welcome, ' + name + '! Your account has been created.');
            window.location.href = 'history.html';
        });
    }

    // ══════════════════════════════════════════════════════════
    //  LOGIN FORM
    // ══════════════════════════════════════════════════════════
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();           // ALWAYS prevent – we handle with JS
            hideErrors('login-error-box');

            const email = document.getElementById('login-email').value.trim().toLowerCase();
            const pass  = document.getElementById('login-pass').value;
            const errors = [];

            // ── Client-side validation (popup alert) ───────────
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
                errors.push('A valid email address is required.');
            if (!pass)
                errors.push('Password cannot be empty.');

            if (errors.length > 0) {
                alert('⚠️  Login Validation Failed\n─────────────────────────\n' +
                    errors.map((e,i) => (i+1) + '. ' + e).join('\n'));
                return;
            }

            // ── Check credentials against localStorage ─────────
            const users = getUsers();
            const user  = users.find(u => u.email === email && u.password === pass);

            if (!user) {
                showErrors('login-error-box', 'login-error-list',
                    ['Invalid email or password. Please try again.']);
                return;
            }

            // ── Set session & redirect ─────────────────────────
            setCurrentUser({ id: user.id, name: user.name, email: user.email });
            localStorage.setItem('clinic_flash', 'Welcome back, ' + user.name + '!');
            window.location.href = 'history.html';
        });
    }

});
