<?php
require_once 'db.php';
// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_errors'] = ['Please login to book an appointment.'];
    header("Location: auth.php#login");
    exit;
}
$book_errors = $_SESSION['book_errors'] ?? [];
$book_input  = $_SESSION['book_input'] ?? [];
unset($_SESSION['book_errors'], $_SESSION['book_input']);

// Pre-fill from URL params (when coming from doctors page)
$pre_doctor   = htmlspecialchars($_GET['doctor'] ?? $book_input['doctor_name'] ?? '');
$pre_specialty = htmlspecialchars($_GET['specialty'] ?? $book_input['specialty'] ?? '');

$doctors = [
  ['name'=>'Dr. Alistair Thorne',  'specialty'=>'Cardiovascular Science'],
  ['name'=>'Dr. Elena Rodriguez',  'specialty'=>'Neurological Disorders'],
  ['name'=>'Dr. Julian Vance',     'specialty'=>'Pediatric Surgery'],
  ['name'=>'Dr. Sarah Jenkins',    'specialty'=>'Endocrinology / Internal Medicine'],
  ['name'=>'Dr. Marcus Webb',      'specialty'=>'Sports Medicine'],
  ['name'=>'Dr. Linda Chen',       'specialty'=>'Dermatology'],
];
$doctor_map = [];
foreach ($doctors as $d) $doctor_map[$d['name']] = $d['specialty'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="Book an appointment with a specialist on Clinic Scholar."/>
<title>Book Appointment | Clinic Scholar</title>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config={darkMode:"class",theme:{extend:{colors:{"secondary-container":"#e2e2e9","on-secondary-container":"#505157","primary-container":"#d7e2ff","on-primary-container":"#004fa6","on-surface":"#2b3437","surface-container-highest":"#dbe4e7","outline":"#737c7f","surface-container-high":"#e3e9ec","primary-fixed-dim":"#c2d5ff","surface-container-low":"#f1f4f6","primary":"#115cb9","surface-container":"#eaeff1","surface":"#f8f9fa","on-surface-variant":"#586064","on-primary":"#f7f7ff","tertiary-container":"#d5d1f2","on-tertiary-container":"#484661","outline-variant":"#abb3b7","tertiary":"#5e5c78","primary-dim":"#0050a7","surface-container-lowest":"#ffffff","secondary-fixed":"#e2e2e9"},borderRadius:{"DEFAULT":"0.125rem","lg":"0.25rem","xl":"0.5rem","full":"0.75rem"},fontFamily:{"headline":["Public Sans"],"body":["Inter"],"label":["Inter"]}}}}
</script>
<style>
    .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
    body{font-family:'Inter',sans-serif;}
    h1,h2,h3{font-family:'Public Sans',sans-serif;}
    .form-input{width:100%;background-color:#eaeff1;border:none;border-bottom:2px solid rgba(115,124,127,0.3);border-radius:0.375rem 0.375rem 0 0;padding:0.875rem 1rem;transition:all 0.15s;font-family:'Inter',sans-serif;}
    .form-input:focus{outline:none;border-bottom-color:#115cb9;background-color:#ffffff;}
    select.form-input option{background:#fff;color:#2b3437;}
</style>
</head>
<body class="bg-surface text-on-surface">

<!-- Top Navigation -->
<header class="fixed top-0 w-full z-50 bg-[#f8f9fa]/80 backdrop-blur-lg shadow-sm">
  <div class="flex justify-between items-center h-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <a href="index.php" class="text-xl font-bold tracking-tighter text-blue-800">Clinic Scholar</a>
    <nav class="hidden md:flex gap-8 items-center font-headline text-sm tracking-tight">
      <a href="index.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">Home</a>
      <a href="doctors.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">Doctors</a>
      <a href="book.php" class="text-blue-700 font-semibold border-b-2 border-blue-700 pb-1">Book</a>
      <a href="history.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">History</a>
    </nav>
    <div class="flex items-center gap-4">
      <span class="text-slate-600 text-sm hidden md:block">👋 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
      <a href="process/logout.php" class="px-5 py-2 bg-surface-container-highest text-on-surface rounded-xl font-semibold text-sm hover:bg-surface-container-high transition-all">Logout</a>
    </div>
  </div>
  <div class="bg-[#f1f4f6] h-[1px] w-full absolute bottom-0 opacity-10"></div>
</header>

<main class="pt-32 pb-20 px-6 max-w-5xl mx-auto">

  <!-- Progress Stepper -->
  <div class="mb-12">
    <div class="flex justify-between items-end mb-6">
      <div>
        <span class="text-on-surface-variant font-label text-xs uppercase tracking-widest block mb-2">Book Your Appointment</span>
        <h1 class="text-4xl font-black text-on-surface tracking-tight">Appointment Details</h1>
      </div>
    </div>
    <div class="relative h-1.5 w-full bg-surface-container-high rounded-full overflow-hidden">
      <div class="absolute left-0 top-0 h-full w-1/2 bg-primary transition-all duration-500"></div>
    </div>
    <div class="flex justify-between mt-4 text-xs font-bold uppercase tracking-tighter text-on-surface-variant">
      <div class="flex items-center gap-2 text-primary">
        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">check_circle</span> Select Doctor
      </div>
      <div class="flex items-center gap-2 text-primary">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-on-primary text-[10px]">2</span> Date & Time
      </div>
      <div class="flex items-center gap-2 text-outline">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-surface-container-highest text-outline text-[10px]">3</span> Confirm
      </div>
    </div>
  </div>

  <!-- PHP Server Errors -->
  <?php if (!empty($book_errors)): ?>
  <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-8">
    <p class="text-sm font-bold text-red-700 mb-2">Please correct these errors:</p>
    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
      <?php foreach ($book_errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
    <!-- Sidebar Info -->
    <aside class="lg:col-span-3 space-y-8">
      <section class="p-6 bg-surface-container-lowest rounded-xl shadow-sm">
        <div class="flex items-center gap-3 mb-4">
          <span class="material-symbols-outlined text-primary">info</span>
          <h4 class="text-sm font-bold text-on-surface">Need Help?</h4>
        </div>
        <p class="text-xs text-on-surface-variant leading-relaxed">If you are experiencing an emergency, please call 911 immediately or visit the nearest emergency room.</p>
      </section>
      <section class="p-6 bg-primary-container rounded-xl">
        <h4 class="text-sm font-bold text-on-primary-container mb-3">Booking Tips</h4>
        <ul class="text-xs text-on-primary-container space-y-2 list-disc list-inside opacity-90">
          <li>Book at least 24 hours in advance</li>
          <li>Bring your insurance card</li>
          <li>Arrive 10 minutes early</li>
          <li>Add notes for specific concerns</li>
        </ul>
      </section>
    </aside>

    <!-- Main Form -->
    <div class="lg:col-span-9">
      <div class="bg-surface-container-lowest p-8 md:p-10 rounded-2xl shadow-sm">
        <h2 class="font-headline text-xl font-bold mb-8 text-on-surface">Complete Your Booking</h2>

        <form id="bookForm" action="process/book_appointment.php" method="POST" class="space-y-8" novalidate>
          <input type="hidden" name="action" value="book"/>

          <!-- Doctor Selection -->
          <div class="space-y-2">
            <label class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="doctor_select">Select Doctor</label>
            <select id="doctor_select" name="doctor_name" class="form-input" onchange="updateSpecialty(this)">
              <option value="">— Choose a doctor —</option>
              <?php foreach ($doctors as $doc): ?>
                <option value="<?= htmlspecialchars($doc['name']) ?>"
                        data-specialty="<?= htmlspecialchars($doc['specialty']) ?>"
                        <?= ($pre_doctor === $doc['name']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($doc['name']) ?> — <?= htmlspecialchars($doc['specialty']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Specialty (auto-filled) -->
          <div class="space-y-2">
            <label class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1">Specialty</label>
            <input id="specialty_display" type="text" readonly class="form-input cursor-not-allowed opacity-70"
              name="specialty"
              value="<?= $pre_specialty ?>"/>
          </div>

          <!-- Date & Time -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="appt-date">Appointment Date</label>
              <input id="appt-date" type="date" name="appointment_date" class="form-input"
                min="<?= date('Y-m-d') ?>"
                value="<?= htmlspecialchars($book_input['date'] ?? '') ?>"/>
            </div>
            <div class="space-y-2">
              <label class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="appt-time">Appointment Time</label>
              <select id="appt-time" name="appointment_time" class="form-input">
                <option value="">— Select time —</option>
                <option value="09:00:00" <?= (($book_input['time'] ?? '') === '09:00:00') ? 'selected' : '' ?>>09:00 AM</option>
                <option value="09:30:00" <?= (($book_input['time'] ?? '') === '09:30:00') ? 'selected' : '' ?>>09:30 AM</option>
                <option value="10:00:00" <?= (($book_input['time'] ?? '') === '10:00:00') ? 'selected' : '' ?>>10:00 AM</option>
                <option value="10:30:00" <?= (($book_input['time'] ?? '') === '10:30:00') ? 'selected' : '' ?>>10:30 AM</option>
                <option value="11:00:00" <?= (($book_input['time'] ?? '') === '11:00:00') ? 'selected' : '' ?>>11:00 AM</option>
                <option value="11:30:00" <?= (($book_input['time'] ?? '') === '11:30:00') ? 'selected' : '' ?>>11:30 AM</option>
                <option value="14:00:00" <?= (($book_input['time'] ?? '') === '14:00:00') ? 'selected' : '' ?>>02:00 PM</option>
                <option value="14:30:00" <?= (($book_input['time'] ?? '') === '14:30:00') ? 'selected' : '' ?>>02:30 PM</option>
                <option value="15:00:00" <?= (($book_input['time'] ?? '') === '15:00:00') ? 'selected' : '' ?>>03:00 PM</option>
                <option value="15:30:00" <?= (($book_input['time'] ?? '') === '15:30:00') ? 'selected' : '' ?>>03:30 PM</option>
                <option value="16:00:00" <?= (($book_input['time'] ?? '') === '16:00:00') ? 'selected' : '' ?>>04:00 PM</option>
              </select>
            </div>
          </div>

          <!-- Notes -->
          <div class="space-y-2">
            <label class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="appt-notes">Additional Notes <span class="normal-case font-normal text-outline">(optional)</span></label>
            <textarea id="appt-notes" name="notes" rows="4" class="form-input resize-none" placeholder="Describe your symptoms or reason for visit..."><?= htmlspecialchars($book_input['notes'] ?? '') ?></textarea>
          </div>

          <!-- Submit -->
          <div class="flex items-center justify-between pt-4 border-t border-surface-container">
            <a href="doctors.php" class="flex items-center gap-2 text-on-surface-variant font-bold text-xs uppercase tracking-widest hover:text-primary transition-colors">
              <span class="material-symbols-outlined">arrow_back</span> Back to Doctors
            </a>
            <button type="submit" id="bookSubmitBtn" class="bg-primary text-on-primary px-10 py-4 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
              <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">event_available</span>
              Confirm Booking
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<!-- Footer -->
<footer class="bg-[#f1f4f6] w-full py-16">
  <div class="flex flex-col md:flex-row justify-between items-center px-12 gap-8 max-w-[1440px] mx-auto">
    <div class="font-headline font-black text-slate-400">Clinic Scholar</div>
    <div class="flex flex-wrap justify-center gap-8">
      <a class="text-slate-500 font-body text-xs uppercase tracking-widest hover:text-blue-600 hover:underline underline-offset-4 transition-all" href="#">Contact Support</a>
      <a class="text-slate-500 font-body text-xs uppercase tracking-widest hover:text-blue-600 hover:underline underline-offset-4 transition-all" href="#">Privacy</a>
    </div>
    <div class="text-slate-500 font-body text-[10px] uppercase tracking-[0.2em] text-center md:text-right">
      © 2026 University Clinical Standard. The Disciplined Curator System.
    </div>
  </div>
</footer>

<script>
// Doctor data map for auto-filling specialty
const doctorSpecialties = <?= json_encode($doctor_map) ?>;

function updateSpecialty(select) {
    const selected = select.value;
    document.getElementById('specialty_display').value = doctorSpecialties[selected] || '';
}

// Initialize if a doctor is pre-selected
window.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('doctor_select');
    if (sel.value) updateSpecialty(sel);
});

// ===== JavaScript Form Validation with Popup (JS Requirement) =====
document.getElementById('bookForm').addEventListener('submit', function(e) {
    const doctor = document.getElementById('doctor_select').value;
    const date   = document.getElementById('appt-date').value;
    const time   = document.getElementById('appt-time').value;
    const today  = new Date().toISOString().split('T')[0];
    const errors = [];

    if (!doctor) errors.push('Please select a doctor.');
    if (!date)   errors.push('Appointment date is required.');
    else if (date < today) errors.push('Appointment date cannot be in the past.');
    if (!time)   errors.push('Please select an appointment time.');

    if (errors.length > 0) {
        e.preventDefault();
        alert('⚠️ Booking Validation Failed:\n\n• ' + errors.join('\n• '));
    }
});
</script>
</body>
</html>
