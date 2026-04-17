/**
 * doctors.js — Doctors Listing Page JavaScript
 * Clinic Scholar | DS362 Web Programming Project
 *
 * Responsibilities:
 *  1. Live search filter — filters cards by doctor name or specialty in real-time
 *  2. Specialty chips filter — filters cards by category
 *  3. Combined logic: both search and chip filter work simultaneously
 *  4. "No results" message display
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

    // DOM References
    const searchInput  = document.getElementById('doctorSearch');
    const doctorCards  = document.querySelectorAll('#doctorsGrid article');
    const filterChips  = document.querySelectorAll('#filterChips .chip');
    const noResults    = document.getElementById('noResults');

    // Current active filter
    let activeFilter = 'all';

    /**
     * applyFilters()
     * Runs both search text and chip filter simultaneously.
     * Shows only cards matching BOTH conditions.
     */
    function applyFilters() {
        const searchTerm = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        doctorCards.forEach(function (card) {
            const name      = card.dataset.name      || '';
            const specialty = card.dataset.specialty || '';
            const filter    = card.dataset.filter    || '';

            // Check search match
            const matchesSearch = !searchTerm ||
                name.includes(searchTerm) ||
                specialty.includes(searchTerm);

            // Check chip filter match
            const matchesFilter = (activeFilter === 'all') || (filter === activeFilter);

            if (matchesSearch && matchesFilter) {
                card.classList.remove('hidden-card');
                visibleCount++;
            } else {
                card.classList.add('hidden-card');
            }
        });

        // Toggle "no results" message
        if (noResults) {
            noResults.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    // ── Live Search Event ─────────────────────────────────────
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);

        // Clear search on Escape key
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                searchInput.value = '';
                applyFilters();
            }
        });
    }

    // ── Chip Filter Events ────────────────────────────────────
    filterChips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            activeFilter = this.dataset.filter;

            // Update chip styles
            filterChips.forEach(function (c) {
                c.classList.remove('chip-active');
                c.classList.add('chip-default');
            });
            this.classList.remove('chip-default');
            this.classList.add('chip-active');

            applyFilters();
        });
    });

    // ── Initial run (in case URL changed query state) ─────────
    applyFilters();

});
