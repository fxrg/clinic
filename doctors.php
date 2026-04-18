<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="Browse our distinguished medical faculty. Find board-certified specialists at Clinic Scholar."/>
<title>Doctors Listing | Clinic Scholar</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config={darkMode:"class",theme:{extend:{colors:{"secondary-container":"#e2e2e9","on-secondary-container":"#505157","primary-container":"#d7e2ff","on-primary-container":"#004fa6","on-surface":"#2b3437","surface-container-highest":"#dbe4e7","outline":"#737c7f","surface-container-high":"#e3e9ec","primary-fixed-dim":"#c2d5ff","surface-container-low":"#f1f4f6","primary":"#115cb9","surface-container":"#eaeff1","surface":"#f8f9fa","on-surface-variant":"#586064","on-primary":"#f7f7ff","tertiary-container":"#d5d1f2","on-tertiary-container":"#484661","outline-variant":"#abb3b7","tertiary":"#5e5c78","primary-dim":"#0050a7","surface-container-lowest":"#ffffff","secondary-fixed":"#e2e2e9"},borderRadius:{"DEFAULT":"0.125rem","lg":"0.25rem","xl":"0.5rem","full":"0.75rem"},fontFamily:{"headline":["Public Sans"],"body":["Inter"],"label":["Inter"]}}}}
</script>
<style>
    .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
    .doctor-card-gradient{background:linear-gradient(135deg,#115cb9 0%,#0050a7 100%);}
    .doctor-card{transition:transform 0.15s ease,box-shadow 0.15s ease;}
    .doctor-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px -4px rgba(43,52,55,0.12);}
    .hidden-doctor{display:none!important;}
</style>
</head>
<body class="bg-surface font-body text-on-surface">

<!-- Top Navigation -->
<header class="fixed top-0 w-full z-50 bg-[#f8f9fa]/80 backdrop-blur-lg shadow-sm">
  <div class="flex justify-between items-center h-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <a href="index.php" class="text-xl font-bold tracking-tighter text-blue-800 font-headline">Clinic Scholar</a>
    <nav class="hidden md:flex items-center gap-8 font-headline text-sm tracking-tight">
      <a href="index.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">Home</a>
      <a href="doctors.php" class="text-blue-700 font-semibold border-b-2 border-blue-700 pb-1">Doctors</a>
      <a href="book.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">Book</a>
      <a href="history.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">History</a>
    </nav>
    <div class="flex items-center gap-4">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="text-slate-600 text-sm hidden md:block">👋 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="process/logout.php" class="bg-surface-container-highest text-on-surface px-5 py-2 rounded-xl text-sm font-semibold hover:bg-surface-container-high transition-all">Logout</a>
      <?php else: ?>
        <a href="auth.php#login" class="px-6 py-2 rounded-xl text-sm font-semibold bg-secondary-container text-on-secondary-container hover:bg-surface-container-high transition-all">Login</a>
        <a href="auth.php#register" class="px-6 py-2 rounded-xl text-sm font-semibold bg-primary text-on-primary hover:opacity-90 transition-all">Register</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="bg-[#f1f4f6] h-[1px] w-full absolute bottom-0 opacity-10"></div>
</header>

<main class="pt-32 pb-24 px-6 md:px-12 max-w-[1440px] mx-auto">
  <!-- Header -->
  <div class="mb-16">
    <h1 class="font-headline font-extrabold text-4xl md:text-5xl tracking-tighter text-on-surface mb-4">Distinguished Medical Faculty</h1>
    <p class="text-on-surface-variant max-w-2xl text-lg">Connect with world-class specialists and academic practitioners dedicated to evidence-based clinical excellence.</p>
  </div>

  <!-- Search & Filter Bar -->
  <div class="flex flex-col md:flex-row gap-6 mb-12 items-end">
    <div class="flex-1 w-full">
      <label class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-2 ml-1" for="doctorSearch">Search Faculty</label>
      <div class="relative">
        <input id="doctorSearch" class="w-full h-14 pl-12 pr-4 bg-surface-container-highest rounded-xl border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all" placeholder="Search by name or specialty..." type="text"/>
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
      </div>
    </div>
    <div class="flex flex-wrap gap-2" id="filterChips">
      <button class="chip-btn px-6 h-14 rounded-xl bg-primary-container text-on-primary-container font-semibold text-sm transition-all" data-filter="all">All Faculty</button>
      <button class="chip-btn px-6 h-14 rounded-xl bg-secondary-fixed text-on-secondary-container font-medium text-sm hover:bg-secondary-container transition-all" data-filter="cardiology">Cardiology</button>
      <button class="chip-btn px-6 h-14 rounded-xl bg-secondary-fixed text-on-secondary-container font-medium text-sm hover:bg-secondary-container transition-all" data-filter="neurology">Neurology</button>
      <button class="chip-btn px-6 h-14 rounded-xl bg-secondary-fixed text-on-secondary-container font-medium text-sm hover:bg-secondary-container transition-all" data-filter="pediatrics">Pediatrics</button>
      <button class="chip-btn px-6 h-14 rounded-xl bg-secondary-fixed text-on-secondary-container font-medium text-sm hover:bg-secondary-container transition-all" data-filter="internal">Internal Medicine</button>
    </div>
  </div>

  <!-- No Results Message -->
  <div id="noResults" class="hidden text-center py-16">
    <span class="material-symbols-outlined text-6xl text-outline-variant mb-4 block">search_off</span>
    <p class="text-on-surface-variant font-medium">No doctors found matching your search.</p>
  </div>

  <!-- Doctors Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="doctorsGrid">

    <?php
    $doctors = [
      ['name'=>'Dr. Alistair Thorne','specialty'=>'Cardiovascular Science','filter'=>'cardiology','badge'=>'Board Certified','badge_class'=>'bg-primary-container text-on-primary-container','bio'=>'Specializing in advanced heart failure and non-invasive diagnostic techniques with over 15 years of academic research.','img'=>'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=500&q=80','pillar'=>'bg-primary'],
      ['name'=>'Dr. Elena Rodriguez','specialty'=>'Neurological Disorders','filter'=>'neurology','badge'=>'Research Lead','badge_class'=>'bg-tertiary-container text-on-tertiary-container','bio'=>'Pioneer in neuro-regenerative therapy and clinical trials for chronic neurological conditions at University Center.','img'=>'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=500&q=80','pillar'=>'bg-tertiary'],
      ['name'=>'Dr. Julian Vance','specialty'=>'Pediatric Surgery','filter'=>'pediatrics','badge'=>'','badge_class'=>'','bio'=>'Expert in minimally invasive pediatric procedures with a focus on neonatology and congenital reconstructive surgery.','img'=>'https://images.unsplash.com/photo-1582750433449-648ed127bb54?w=500&q=80','pillar'=>'bg-primary'],
      ['name'=>'Dr. Sarah Jenkins','specialty'=>'Endocrinology / Internal Medicine','filter'=>'internal','badge'=>'','badge_class'=>'','bio'=>'Focused on metabolic health and hormone replacement therapies with published work in leading medical journals.','img'=>'https://images.unsplash.com/photo-1527613426441-4da17471b66d?w=500&q=80','pillar'=>'bg-primary'],
      ['name'=>'Dr. Marcus Webb','specialty'=>'Sports Medicine','filter'=>'internal','badge'=>'Available Today','badge_class'=>'bg-secondary-container text-on-secondary-container','bio'=>'Leading specialist in orthopedic rehabilitation and injury prevention for professional athletes and clinical patients.','img'=>'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=500&q=80','pillar'=>'bg-primary'],
      ['name'=>'Dr. Linda Chen','specialty'=>'Dermatology','filter'=>'internal','badge'=>'','badge_class'=>'','bio'=>'Specializing in oncological dermatology and advanced laser treatments with a holistic approach to skin health.','img'=>'https://images.unsplash.com/photo-1594824476967-48c8b964273f?w=500&q=80','pillar'=>'bg-primary'],
    ];
    foreach ($doctors as $doc):
      $bookUrl = 'book.php?doctor=' . urlencode($doc['name']) . '&specialty=' . urlencode($doc['specialty']);
    ?>
    <div class="doctor-card bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm flex flex-col group"
         data-filter="<?= $doc['filter'] ?>"
         data-name="<?= strtolower($doc['name']) ?>"
         data-specialty="<?= strtolower($doc['specialty']) ?>">
      <div class="h-64 overflow-hidden relative">
        <?php if ($doc['badge']): ?>
        <div class="absolute top-4 left-4 z-10">
          <span class="<?= $doc['badge_class'] ?> px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"><?= $doc['badge'] ?></span>
        </div>
        <?php endif; ?>
        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
             src="<?= $doc['img'] ?>" alt="Portrait of <?= $doc['name'] ?>"/>
      </div>
      <div class="p-8 flex-1 flex flex-col">
        <div class="flex items-start justify-between mb-4">
          <div>
            <p class="text-primary font-bold text-xs uppercase tracking-widest mb-1"><?= $doc['specialty'] ?></p>
            <h3 class="font-headline font-bold text-2xl text-on-surface"><?= $doc['name'] ?></h3>
          </div>
          <div class="w-1 h-12 <?= $doc['pillar'] ?> rounded-full shrink-0"></div>
        </div>
        <p class="text-on-surface-variant text-sm mb-8 leading-relaxed"><?= $doc['bio'] ?></p>
        <div class="mt-auto">
          <a href="<?= $bookUrl ?>" class="block w-full doctor-card-gradient text-on-primary font-semibold py-4 rounded-xl shadow-md text-center hover:opacity-90 transition-all">
            Book Appointment
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

 
</main>

<!-- Footer -->
<footer class="bg-[#f1f4f6] w-full py-16">
  <div class="flex flex-col md:flex-row justify-between items-center px-12 gap-8 max-w-[1440px] mx-auto">
    <div class="font-headline font-black text-slate-400">© 2026 University Clinical Standard. The Disciplined Curator System.</div>
    <div class="flex gap-10 font-body text-xs uppercase tracking-widest font-medium">
      <a class="text-slate-500 hover:text-blue-600 hover:underline underline-offset-4 transition-all duration-200" href="mailto:s230005904@seu.edu.sa">Contact Support</a>
      <a class="text-slate-500 hover:text-blue-600 hover:underline underline-offset-4 transition-all duration-200" href="#">Privacy</a>
    </div>
  </div>
</footer>

<!-- JavaScript: Live Search + Filter (Dynamic JS Requirement) -->
<script>
const searchInput   = document.getElementById('doctorSearch');
const doctorCards   = document.querySelectorAll('.doctor-card');
const filterBtns    = document.querySelectorAll('.chip-btn');
const noResults     = document.getElementById('noResults');
let activeFilter = 'all';

function applyFilters() {
    const term = searchInput.value.trim().toLowerCase();
    let visibleCount = 0;

    doctorCards.forEach(card => {
        const name     = card.dataset.name;
        const specialty = card.dataset.specialty;
        const filter   = card.dataset.filter;

        const matchesSearch  = !term || name.includes(term) || specialty.includes(term);
        const matchesFilter  = activeFilter === 'all' || filter === activeFilter;

        if (matchesSearch && matchesFilter) {
            card.classList.remove('hidden-doctor');
            visibleCount++;
        } else {
            card.classList.add('hidden-doctor');
        }
    });

    noResults.classList.toggle('hidden', visibleCount > 0);
}

searchInput.addEventListener('input', applyFilters);

filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        activeFilter = this.dataset.filter;
        // Update chip styles
        filterBtns.forEach(b => {
            b.classList.remove('bg-primary-container', 'text-on-primary-container');
            b.classList.add('bg-secondary-fixed', 'text-on-secondary-container');
        });
        this.classList.remove('bg-secondary-fixed', 'text-on-secondary-container');
        this.classList.add('bg-primary-container', 'text-on-primary-container');
        applyFilters();
    });
});
</script>
</body>
</html>
