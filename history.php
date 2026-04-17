<?php
/**
 * history.php — Appointment History Page
 * DS362 Clinic Appointment System
 *
 * This page REQUIRES PHP because it fetches and renders
 * appointment data from MySQL for the logged-in user.
 *
 * External files used:
 *   - style.css  (design system)
 *   - history.js (tabs, modals, toast)
 */

require_once 'db.php';

// ── Auth guard ─────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    $encoded = urlencode(json_encode(['Please login to view your appointment history.']));
    header("Location: auth.html?login_errors=$encoded#login");
    exit;
}

$user_id   = (int)$_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Guest');

// ── Consume flash messages ─────────────────────────────────────
$flash_success = $_SESSION['flash']       ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);

// ── SELECT: Fetch all appointments for this user ───────────────
$stmt = $conn->prepare("
    SELECT id, doctor_name, specialty, appointment_date, appointment_time, notes, status
    FROM   appointments
    WHERE  user_id = ?
    ORDER BY appointment_date DESC, appointment_time DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result       = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Summary stats ──────────────────────────────────────────────
$total         = count($appointments);
$today         = date('Y-m-d');
$upcoming      = array_filter($appointments, function($a) use ($today) {
    return in_array($a['status'], ['Confirmed','Pending']) && $a['appointment_date'] >= $today;
});
$upcoming_arr  = array_values($upcoming);
$upcoming_count= count($upcoming_arr);
$next_apt      = $upcoming_arr[0] ?? null;

function statusBarClass($status) {
    return match($status) {
        'Confirmed' => 'confirmed',
        'Pending'   => 'pending',
        'Completed' => 'completed',
        'Cancelled' => 'cancelled',
        default     => 'completed'
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="View and manage your appointment history on Clinic Scholar."/>
  <title>Clinic Scholar | Appointment History</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1"/>
</head>
<body>

<header class="navbar">
  <div class="navbar-inner">
    <a href="index.html" class="navbar-brand">Clinic Scholar</a>
    <nav>
      <ul class="navbar-nav">
        <li><a href="index.html"   class="nav-link">Home</a></li>
        <li><a href="doctors.html" class="nav-link">Doctors</a></li>
        <li><a href="book.html"    class="nav-link">Book</a></li>
        <li><a href="history.php"  class="nav-link active">History</a></li>
      </ul>
    </nav>
    <div class="navbar-actions">
      <span style="font-size:0.875rem; color:var(--on-surface-variant);">&#128075; <?= $user_name ?></span>
      <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
    </div>
  </div>
</header>

<?php if ($flash_success): ?>
<div class="toast toast-success" id="toast">
  <span class="material-symbols-outlined">check_circle</span>
  <?= htmlspecialchars($flash_success) ?>
</div>
<?php elseif ($flash_error): ?>
<div class="toast toast-error" id="toast">
  <span class="material-symbols-outlined">error</span>
  <?= htmlspecialchars($flash_error) ?>
</div>
<?php endif; ?>

<main style="padding-top:80px;">
  <div class="container" style="padding-top:4rem; padding-bottom:6rem;">

    <header style="margin-bottom:3rem;">
      <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-end; gap:1.5rem;">
        <div>
          <h1 style="font-size:clamp(1.75rem, 3.5vw, 2.5rem); font-weight:900; letter-spacing:-0.03em; margin-bottom:0.5rem;">Appointment History</h1>
          <p style="color:var(--on-surface-variant); max-width:520px;">Review your clinical records, track upcoming sessions, and manage your health journey.</p>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;">
          <div style="background:var(--surface-container-low); padding:0.6rem 1rem; border-radius:0.75rem; display:flex; align-items:center; gap:0.5rem;">
            <span class="material-symbols-outlined" style="color:var(--primary); font-size:1.25rem;">calendar_today</span>
            <span style="font-size:0.875rem; font-weight:700;">Total: <?= $total ?></span>
          </div>
          <div style="background:var(--surface-container-low); padding:0.6rem 1rem; border-radius:0.75rem; display:flex; align-items:center; gap:0.5rem;">
            <span class="material-symbols-outlined" style="color:var(--tertiary); font-size:1.25rem;">pending_actions</span>
            <span style="font-size:0.875rem; font-weight:700;">Upcoming: <?= $upcoming_count ?></span>
          </div>
          <a href="book.html" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined">add</span> New Booking
          </a>
        </div>
      </div>
    </header>

    <div style="display:grid; grid-template-columns:1fr; gap:2rem;">

      <!-- SIDEBAR -->
      <aside style="display:flex; flex-direction:column; gap:1.5rem;">
        <?php if ($next_apt): ?>
        <div style="background:linear-gradient(135deg,var(--primary),var(--primary-dim)); color:var(--on-primary); padding:2rem; border-radius:1rem; box-shadow:0 8px 24px rgba(17,92,185,0.3); position:relative; overflow:hidden;">
          <div style="position:absolute; right:-3rem; top:-3rem; width:12rem; height:12rem; background:rgba(255,255,255,0.08); border-radius:50%;"></div>
          <div style="position:relative; z-index:1;">
            <span class="uppercase-label" style="opacity:0.7; display:block; margin-bottom:0.75rem; font-size:0.65rem;">Upcoming Priority</span>
            <h2 style="font-size:1.25rem; font-weight:800; margin-bottom:1.25rem;"><?= htmlspecialchars($next_apt['specialty']) ?></h2>
            <div style="display:flex; flex-direction:column; gap:0.75rem; font-size:0.875rem;">
              <div style="display:flex; align-items:center; gap:0.75rem;">
                <span class="material-symbols-outlined" style="opacity:0.7;">person</span>
                <span style="font-weight:600;"><?= htmlspecialchars($next_apt['doctor_name']) ?></span>
              </div>
              <div style="display:flex; align-items:center; gap:0.75rem;">
                <span class="material-symbols-outlined" style="opacity:0.7;">schedule</span>
                <span><?= date('M d, Y', strtotime($next_apt['appointment_date'])) ?> &bull; <?= date('h:i A', strtotime($next_apt['appointment_time'])) ?></span>
              </div>
            </div>
            <button onclick="openReschedule(<?= $next_apt['id'] ?>,'<?= $next_apt['appointment_date'] ?>','<?= substr($next_apt['appointment_time'],0,5) ?>')"
              style="margin-top:1.5rem; width:100%; background:#fff; color:var(--primary); border:none; padding:0.875rem; border-radius:0.75rem; font-weight:800; font-size:0.875rem; cursor:pointer;">
              Reschedule
            </button>
          </div>
        </div>
        <?php else: ?>
        <div style="background:var(--surface-container-low); padding:2rem; border-radius:1rem; text-align:center;">
          <span class="material-symbols-outlined" style="font-size:3rem; color:var(--outline-variant); display:block; margin-bottom:0.75rem;">event_busy</span>
          <h2 style="font-size:1rem; font-weight:700; margin-bottom:0.5rem;">No Upcoming Appointments</h2>
          <p style="font-size:0.8rem; color:var(--on-surface-variant); margin-bottom:1.25rem;">You have no scheduled visits.</p>
          <a href="book.html" class="btn btn-primary btn-sm" style="display:inline-flex;">Book Now</a>
        </div>
        <?php endif; ?>

        <div style="background:var(--surface-container-low); padding:2rem; border-radius:1rem;">
          <h3 class="uppercase-label" style="color:var(--on-surface-variant); margin-bottom:1.5rem; display:block;">Patient Summary</h3>
          <div style="display:flex; flex-direction:column; gap:1rem;">
            <div style="display:flex; justify-content:space-between;"><span style="color:var(--on-surface-variant); font-size:0.875rem;">Total Visits</span><span style="color:var(--primary); font-weight:900; font-size:1.25rem;"><?= $total ?></span></div>
            <div class="progress-track"><div class="progress-fill" style="width:<?= min(100, $total * 12) ?>%;"></div></div>
            <div style="display:flex; justify-content:space-between;"><span style="color:var(--on-surface-variant); font-size:0.875rem;">Upcoming</span><span style="color:var(--tertiary); font-weight:900; font-size:1.25rem;"><?= $upcoming_count ?></span></div>
          </div>
        </div>
      </aside>

      <!-- TABLE SECTION -->
      <section>
        <div style="background:var(--surface-container-low); border-radius:1rem; overflow:hidden;">
          <div style="padding:1.5rem 2rem; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:1rem; border-bottom:1px solid rgba(171,179,183,0.08);">
            <h2 style="font-family:var(--font-headline); font-size:1.25rem; font-weight:800;">Visit Logs</h2>
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;" id="statusTabs">
              <button class="chip chip-active tab-btn" data-status="all">All</button>
              <button class="chip chip-default tab-btn" data-status="Pending">Pending</button>
              <button class="chip chip-default tab-btn" data-status="Confirmed">Confirmed</button>
              <button class="chip chip-default tab-btn" data-status="Completed">Completed</button>
              <button class="chip chip-default tab-btn" data-status="Cancelled">Cancelled</button>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Doctor &amp; Specialty</th>
                  <th>Date &amp; Time</th>
                  <th>Notes</th>
                  <th>Status</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody id="appointmentsTableBody">
                <?php if (empty($appointments)): ?>
                <tr><td colspan="5" style="padding:4rem; text-align:center; color:var(--on-surface-variant);">
                  <span class="material-symbols-outlined" style="font-size:3rem; display:block; margin-bottom:0.75rem; color:var(--outline-variant);">event_note</span>
                  No appointments yet. <a href="book.html" style="color:var(--primary); font-weight:700;">Book one now &rarr;</a>
                </td></tr>
                <?php else: ?>
                <?php foreach ($appointments as $apt):
                    $barClass = statusBarClass($apt['status']);
                    $isPast   = ($apt['appointment_date'] < $today) || in_array($apt['status'], ['Completed','Cancelled']);
                ?>
                <tr data-status="<?= htmlspecialchars($apt['status']) ?>" style="opacity:<?= $isPast ? '0.72' : '1' ?>;">
                  <td>
                    <div style="font-weight:700; font-size:0.875rem;"><?= htmlspecialchars($apt['doctor_name']) ?></div>
                    <div style="font-size:0.75rem; color:var(--on-surface-variant);"><?= htmlspecialchars($apt['specialty']) ?></div>
                  </td>
                  <td>
                    <div style="font-weight:700; font-size:0.875rem;"><?= date('M d, Y', strtotime($apt['appointment_date'])) ?></div>
                    <div style="font-size:0.75rem; color:var(--on-surface-variant);"><?= date('h:i A', strtotime($apt['appointment_time'])) ?></div>
                  </td>
                  <td>
                    <?php if ($apt['notes']): ?>
                      <span style="font-size:0.75rem; color:var(--on-surface-variant); font-style:italic; display:block; max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($apt['notes']) ?>"><?= htmlspecialchars($apt['notes']) ?></span>
                    <?php else: ?><span style="font-size:0.75rem; color:var(--outline);">—</span><?php endif; ?>
                  </td>
                  <td>
                    <div class="status-pill">
                      <div class="status-bar <?= $barClass ?>"></div>
                      <span class="status-text <?= $barClass ?>"><?= htmlspecialchars($apt['status']) ?></span>
                    </div>
                  </td>
                  <td style="text-align:right;">
                    <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                      <?php if (!$isPast): ?>
                      <button onclick="openReschedule(<?= $apt['id'] ?>,'<?= $apt['appointment_date'] ?>','<?= substr($apt['appointment_time'],0,5) ?>')"
                        style="padding:0.4rem 0.6rem; background:var(--primary-container); color:var(--on-primary-container); border:none; border-radius:0.375rem; cursor:pointer; display:flex; align-items:center;" title="Reschedule">
                        <span class="material-symbols-outlined" style="font-size:1rem;">edit_calendar</span>
                      </button>
                      <?php endif; ?>
                      <button onclick="openDelete(<?= $apt['id'] ?>,'<?= addslashes($apt['doctor_name']) ?>')"
                        style="padding:0.4rem 0.6rem; background:#fef2f2; color:#dc2626; border:none; border-radius:0.375rem; cursor:pointer; display:flex; align-items:center;" title="Cancel/Delete">
                        <span class="material-symbols-outlined" style="font-size:1rem;">delete</span>
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <?php if (!empty($appointments)): ?>
          <div style="padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; background:rgba(227,233,236,0.25);">
            <span style="font-size:0.75rem; font-weight:700; color:var(--on-surface-variant);">Showing <?= $total ?> record<?= $total !== 1 ? 's' : '' ?></span>
            <a href="book.html" style="color:var(--primary); font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:0.25rem;">
              <span class="material-symbols-outlined" style="font-size:1rem;">add_circle</span> Book New Appointment
            </a>
          </div>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </div>
</main>

<!-- RESCHEDULE MODAL -->
<div class="modal-overlay" id="rescheduleModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal('rescheduleModal')"><span class="material-symbols-outlined">close</span></button>
    <h3 style="font-family:var(--font-headline); font-size:1.375rem; font-weight:800; margin-bottom:0.5rem;">Reschedule Appointment</h3>
    <p style="color:var(--on-surface-variant); font-size:0.875rem; margin-bottom:2rem;">Choose a new date and time.</p>
    <form id="rescheduleForm" action="update_appointment.php" method="POST" novalidate>
      <input type="hidden" name="action" value="update"/>
      <input type="hidden" name="appointment_id" id="reschedule-id"/>
      <div class="form-group" style="margin-bottom:1.5rem;">
        <label class="form-label" for="new-date">New Date</label>
        <input type="date" id="new-date" name="new_date" class="form-input" style="margin-top:0.5rem;"/>
      </div>
      <div class="form-group" style="margin-bottom:2rem;">
        <label class="form-label" for="new-time">New Time</label>
        <select id="new-time" name="new_time" class="form-input" style="margin-top:0.5rem;">
          <option value="09:00">09:00 AM</option>
          <option value="09:30">09:30 AM</option>
          <option value="10:00">10:00 AM</option>
          <option value="10:30">10:30 AM</option>
          <option value="11:00">11:00 AM</option>
          <option value="11:30">11:30 AM</option>
          <option value="14:00">02:00 PM</option>
          <option value="14:30">02:30 PM</option>
          <option value="15:00">03:00 PM</option>
          <option value="15:30">03:30 PM</option>
          <option value="16:00">04:00 PM</option>
        </select>
      </div>
      <div style="display:flex; gap:1rem;">
        <button type="button" onclick="closeModal('rescheduleModal')" class="btn btn-outline btn-full">Cancel</button>
        <button type="submit" class="btn btn-primary btn-full">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box" style="text-align:center;">
    <div style="width:4rem; height:4rem; background:#fef2f2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem;">
      <span class="material-symbols-outlined" style="color:#dc2626; font-size:2rem;">delete_forever</span>
    </div>
    <h3 style="font-family:var(--font-headline); font-size:1.25rem; font-weight:800; margin-bottom:0.5rem;">Cancel Appointment?</h3>
    <p style="color:var(--on-surface-variant); font-size:0.875rem; margin-bottom:0.5rem;">You are about to cancel your appointment with</p>
    <p style="font-weight:800; font-size:1rem; margin-bottom:0.75rem;" id="delete-doctor-name"></p>
    <p style="font-size:0.75rem; color:var(--outline); margin-bottom:2rem;">This action is permanent and cannot be undone.</p>
    <form action="delete_appointment.php" method="POST">
      <input type="hidden" name="action" value="delete"/>
      <input type="hidden" name="appointment_id" id="delete-id"/>
      <div style="display:flex; gap:1rem;">
        <button type="button" onclick="closeModal('deleteModal')" class="btn btn-outline btn-full">Keep It</button>
        <button type="submit" class="btn btn-danger btn-full">Yes, Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Footer -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-brand">CLINIC SCHOLAR</div>
    <ul class="footer-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="doctors.html">Doctors</a></li>
      <li><a href="book.html">Book</a></li>
      <li><a href="#">Privacy</a></li>
    </ul>
    <div class="footer-copy">&copy; 2026 University Clinical Standard.<br/>The Disciplined Curator System.</div>
  </div>
</footer>

<!-- External JavaScript -->
<script src="history.js"></script>

</body>
</html>
