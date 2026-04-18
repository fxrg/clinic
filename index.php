<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="Clinic Scholar - The Academic Clinical Standard. Book appointments with world-class physicians through our disciplined curator system."/>
<title>Clinic Scholar | Academic Clinical Standard</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "secondary-container": "#e2e2e9",
            "on-secondary-container": "#505157",
            "primary-fixed": "#d7e2ff",
            "on-tertiary": "#fcf7ff",
            "on-surface": "#2b3437",
            "surface-container-highest": "#dbe4e7",
            "background": "#f8f9fa",
            "on-primary-container": "#004fa6",
            "on-background": "#2b3437",
            "error": "#9f403d",
            "outline": "#737c7f",
            "surface-container-high": "#e3e9ec",
            "surface-tint": "#115cb9",
            "on-secondary": "#f9f8ff",
            "primary-fixed-dim": "#c2d5ff",
            "surface-container-low": "#f1f4f6",
            "primary-container": "#d7e2ff",
            "primary": "#115cb9",
            "surface-dim": "#d1dce0",
            "surface-container": "#eaeff1",
            "on-error": "#fff7f6",
            "surface": "#f8f9fa",
            "surface-bright": "#f8f9fa",
            "on-surface-variant": "#586064",
            "on-primary": "#f7f7ff",
            "surface-variant": "#dbe4e7",
            "tertiary-container": "#d5d1f2",
            "outline-variant": "#abb3b7",
            "tertiary": "#5e5c78",
            "secondary": "#5d5f65",
            "on-primary-fixed": "#003d83",
            "error-container": "#fe8983",
            "primary-dim": "#0050a7",
            "on-tertiary-container": "#484661",
            "surface-container-lowest": "#ffffff",
            "secondary-fixed": "#e2e2e9",
          },
          borderRadius: {
            "DEFAULT": "0.125rem",
            "lg": "0.25rem",
            "xl": "0.5rem",
            "full": "0.75rem"
          },
          fontFamily: {
            "headline": ["Public Sans"],
            "body": ["Inter"],
            "label": ["Inter"]
          }
        },
      },
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .hero-gradient { background: linear-gradient(135deg, #115cb9 0%, #0050a7 100%); }
    .glass-nav { backdrop-filter: blur(16px); }
    .animate-fade-up { animation: fadeUp 0.6s ease-out both; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .stat-counter { animation: fadeUp 0.8s ease-out 0.3s both; }
</style>
</head>
<body class="bg-surface font-body text-on-surface">

<!-- Top Navigation -->
<header class="fixed top-0 w-full z-50 bg-[#f8f9fa]/80 backdrop-blur-lg shadow-sm">
  <div class="flex justify-between items-center h-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <a href="index.php" class="text-xl font-bold tracking-tighter text-blue-800 font-headline">Clinic Scholar</a>
    <nav class="hidden md:flex items-center gap-8 font-headline text-sm tracking-tight">
      <a href="index.php" class="text-blue-700 font-semibold border-b-2 border-blue-700 pb-1">Home</a>
      <a href="doctors.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">Doctors</a>
      <a href="book.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">Book</a>
      <a href="history.php" class="text-slate-600 font-medium hover:text-blue-500 transition-colors duration-150">History</a>
    </nav>
    <div class="flex items-center gap-4">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="text-slate-600 text-sm font-medium hidden md:block">👋 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="process/logout.php" class="bg-surface-container-highest text-on-surface px-5 py-2 rounded-xl text-sm font-semibold hover:bg-surface-container-high transition-all">Logout</a>
      <?php else: ?>
        <a href="auth.php#login" class="text-slate-600 font-medium text-sm hover:text-blue-500 transition-colors duration-150 scale-95">Login</a>
        <a href="auth.php#register" class="bg-primary text-on-primary px-6 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition-all scale-95">Register</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="bg-[#f1f4f6] h-[1px] w-full absolute bottom-0 opacity-10"></div>
</header>

<main class="pt-20">

  <!-- Hero Section -->
  <section class="relative min-h-[820px] flex items-center overflow-hidden">
    <div class="max-w-[1440px] mx-auto px-6 md:px-12 w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center py-16">
      <div class="z-10 animate-fade-up">
        <span class="inline-block py-1 px-3 rounded-full bg-primary-container text-on-primary-container text-xs font-bold tracking-widest uppercase mb-6">
          University Clinical Standard
        </span>
        <h1 class="font-headline font-black text-5xl md:text-6xl leading-tight text-on-surface mb-6">
          Excellence in <br/><span class="text-primary italic">Clinical</span> Practice.
        </h1>
        <p class="text-on-surface-variant text-lg max-w-lg mb-10 leading-relaxed">
          The Disciplined Curator System brings academic rigor to patient care. Manage your appointments with institutional trust and sophisticated ease.
        </p>
        <div class="flex flex-wrap gap-4">
          <a href="book.php" class="bg-primary text-on-primary px-8 py-4 rounded-xl font-semibold flex items-center gap-2 hover:opacity-90 transition-all shadow-lg shadow-primary/20">
            Book Appointment
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </a>
          <a href="doctors.php" class="bg-secondary-container text-on-secondary-container px-8 py-4 rounded-xl font-semibold hover:bg-surface-container-high transition-all">
            View Doctors
          </a>
        </div>
        <!-- Quick Stats -->
        <div class="mt-12 flex gap-10 stat-counter">
          <div>
            <div class="text-3xl font-black text-primary font-headline">50+</div>
            <div class="text-xs text-on-surface-variant font-medium uppercase tracking-widest">Specialists</div>
          </div>
          <div class="w-px bg-outline-variant opacity-30"></div>
          <div>
            <div class="text-3xl font-black text-primary font-headline">98%</div>
            <div class="text-xs text-on-surface-variant font-medium uppercase tracking-widest">Satisfaction</div>
          </div>
          <div class="w-px bg-outline-variant opacity-30"></div>
          <div>
            <div class="text-3xl font-black text-primary font-headline">24/7</div>
            <div class="text-xs text-on-surface-variant font-medium uppercase tracking-widest">Support</div>
          </div>
        </div>
      </div>
      <div class="relative h-[540px] hidden lg:block">
        <div class="absolute inset-0 bg-primary-fixed-dim rounded-[2rem] transform rotate-3 scale-95 opacity-20"></div>
        <div class="absolute inset-0 bg-surface-container-highest rounded-[2rem] transform -rotate-2 scale-95 opacity-50"></div>
        <img alt="Modern Clinical Research Laboratory" class="relative z-10 w-full h-full object-cover rounded-[2rem] shadow-2xl"
          src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&q=80"/>
      </div>
    </div>
  </section>

  <!-- Features Bento Grid -->
  <section class="bg-surface-container-low py-24">
    <div class="max-w-[1440px] mx-auto px-6 md:px-12">
      <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
        <div class="max-w-2xl">
          <h2 class="font-headline text-4xl font-bold text-on-surface mb-4">Sophisticated Care Modules</h2>
          <p class="text-on-surface-variant">Our system is built on decades of academic research, ensuring every patient interaction is handled with surgical precision and clinical empathy.</p>
        </div>
        <div class="flex gap-2">
          <span class="w-12 h-1 bg-primary rounded-full"></span>
          <span class="w-4 h-1 bg-outline-variant rounded-full"></span>
          <span class="w-4 h-1 bg-outline-variant rounded-full"></span>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Featured Card -->
        <div class="md:col-span-2 bg-surface-container-lowest p-10 rounded-[2rem] shadow-sm flex flex-col justify-between">
          <div>
            <span class="material-symbols-outlined text-primary text-4xl mb-6 block">clinical_notes</span>
            <h3 class="font-headline text-2xl font-bold mb-4">Advanced Diagnostics</h3>
            <p class="text-on-surface-variant leading-relaxed">Access the latest in clinical screening and diagnostic tools curated by the University Board of Medicine.</p>
          </div>
          <div class="mt-8 pt-8 border-t border-surface-container/50">
            <a href="doctors.php" class="text-primary font-semibold flex items-center gap-2 group">
              Explore Doctors <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">east</span>
            </a>
          </div>
        </div>
        <!-- Priority Booking Card -->
        <div class="hero-gradient p-10 rounded-[2rem] text-on-primary flex flex-col justify-between">
          <span class="material-symbols-outlined text-4xl mb-6 block" style="font-variation-settings:'FILL' 1;">schedule</span>
          <div>
            <h3 class="font-headline text-xl font-bold mb-2">Priority Booking</h3>
            <p class="text-primary-fixed-dim text-sm">Real-time synchronization with faculty schedules.</p>
          </div>
        </div>
        <!-- Expert Consult Card -->
        <div class="bg-surface-container-lowest p-10 rounded-[2rem] shadow-sm flex flex-col justify-between">
          <span class="material-symbols-outlined text-tertiary text-4xl mb-6 block">diversity_3</span>
          <div>
            <h3 class="font-headline text-xl font-bold mb-2">Expert Consult</h3>
            <p class="text-on-surface-variant text-sm">Direct access to leading specialists and senior residents.</p>
          </div>
        </div>
        <!-- Research Access Wide Card -->
        <div class="md:col-span-2 bg-surface-container-lowest p-10 rounded-[2rem] shadow-sm flex flex-col md:flex-row gap-8 items-center">
          <div class="flex-1">
            <span class="material-symbols-outlined text-on-surface-variant text-4xl mb-6 block">science</span>
            <h3 class="font-headline text-2xl font-bold mb-4">Research Access</h3>
            <p class="text-on-surface-variant leading-relaxed">Patients within the scholar network gain exclusive access to trial treatments and longitudinal studies.</p>
          </div>
          <div class="w-full md:w-48 h-48 bg-surface-container rounded-3xl overflow-hidden shrink-0">
            <img alt="Laboratory" class="w-full h-full object-cover"
              src="https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?w=400&q=80"/>
          </div>
        </div>
        <!-- Academic Network Card -->
        <div class="md:col-span-2 bg-tertiary-container p-10 rounded-[2rem] flex items-center justify-between overflow-hidden relative">
          <div class="relative z-10 max-w-xs">
            <h3 class="font-headline text-2xl font-bold text-on-tertiary-container mb-4">Academic Network</h3>
            <p class="text-on-tertiary-container opacity-80 text-sm">Connect with over 50 university-affiliated clinics across the region.</p>
          </div>
          <span class="material-symbols-outlined text-[120px] text-on-tertiary-container absolute -right-4 -bottom-4 opacity-5 rotate-12">account_balance</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Asymmetric Info Section -->
  <section class="py-32 bg-surface">
    <div class="max-w-[1440px] mx-auto px-6 md:px-12 grid grid-cols-12 gap-12 items-center">
      <div class="col-span-12 lg:col-span-5 relative">
        <img alt="Professional Doctor in Modern Clinic" class="w-full h-[600px] object-cover rounded-[3rem] shadow-xl"
          src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=700&q=80"/>
        <div class="absolute -bottom-8 -right-8 bg-surface-container-lowest p-8 rounded-3xl shadow-2xl max-w-xs">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-primary-container rounded-full flex items-center justify-center text-primary">
              <span class="material-symbols-outlined">verified</span>
            </div>
            <div>
              <div class="text-sm font-bold">Faculty Verified</div>
              <div class="text-xs text-on-surface-variant">Board Certified Professionals</div>
            </div>
          </div>
          <p class="text-xs text-on-surface-variant italic">"The intersection of academic rigor and compassionate care is where we reside."</p>
        </div>
      </div>
      <div class="col-span-12 lg:col-span-6 lg:col-start-7">
        <h2 class="font-headline text-5xl font-extrabold text-on-surface mb-8 leading-tight">Beyond a standard <span class="text-primary">Medical</span> facility.</h2>
        <div class="space-y-10">
          <div class="flex gap-6">
            <div class="shrink-0 h-4 w-1 bg-primary rounded-full mt-2"></div>
            <div>
              <h4 class="font-headline text-xl font-bold mb-2">Curated Specialist Matching</h4>
              <p class="text-on-surface-variant">Our system matches your specific symptoms with the most published specialists in that specific medical field.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="shrink-0 h-4 w-1 bg-tertiary rounded-full mt-2"></div>
            <div>
              <h4 class="font-headline text-xl font-bold mb-2">Longitudinal Care Tracks</h4>
              <p class="text-on-surface-variant">We don't just treat symptoms; we curate long-term wellness plans based on comprehensive academic datasets.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="shrink-0 h-4 w-1 bg-primary-fixed-dim rounded-full mt-2"></div>
            <div>
              <h4 class="font-headline text-xl font-bold mb-2">Institutional Privacy Protocol</h4>
              <p class="text-on-surface-variant">Utilizing university-grade encryption and privacy standards to protect your sensitive clinical data.</p>
            </div>
          </div>
        </div>
        <div class="mt-12">
          <a href="auth.php#register" class="bg-primary text-on-primary px-8 py-4 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-primary/20 inline-flex items-center gap-2">
            Get Started Today
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="py-24 bg-surface-container-low">
    <div class="max-w-[1440px] mx-auto px-6 md:px-12">
      <div class="text-center mb-16">
        <h2 class="font-headline text-4xl font-bold text-on-surface mb-4">How It Works</h2>
        <p class="text-on-surface-variant max-w-xl mx-auto">Three simple steps to connect with the best medical professionals in our network.</p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-surface-container-lowest p-10 rounded-[2rem] text-center shadow-sm">
          <div class="w-16 h-16 bg-primary-container rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-primary text-3xl">person_add</span>
          </div>
          <h3 class="font-headline text-xl font-bold mb-3">1. Register</h3>
          <p class="text-on-surface-variant text-sm leading-relaxed">Create your scholar account with your academic email and personal information.</p>
        </div>
        <div class="bg-primary hero-gradient p-10 rounded-[2rem] text-center text-on-primary shadow-lg">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings:'FILL' 1;">medical_services</span>
          </div>
          <h3 class="font-headline text-xl font-bold mb-3">2. Choose a Doctor</h3>
          <p class="text-primary-fixed-dim text-sm leading-relaxed">Browse our curated list of verified specialists and select the right match for your needs.</p>
        </div>
        <div class="bg-surface-container-lowest p-10 rounded-[2rem] text-center shadow-sm">
          <div class="w-16 h-16 bg-tertiary-container rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-on-tertiary-container text-3xl">event_available</span>
          </div>
          <h3 class="font-headline text-xl font-bold mb-3">3. Book & Track</h3>
          <p class="text-on-surface-variant text-sm leading-relaxed">Book your appointment and track your complete medical history in one place.</p>
        </div>
      </div>
    </div>
  </section>

</main>

<!-- Floating Book Button -->
<a href="book.php" class="fixed bottom-12 right-12 bg-primary text-on-primary w-16 h-16 rounded-full shadow-2xl flex items-center justify-center z-40 hover:scale-105 active:scale-95 transition-transform group">
  <span class="material-symbols-outlined text-3xl" style="font-variation-settings:'FILL' 1;">add</span>
  <span class="absolute right-full mr-4 bg-on-surface text-surface py-2 px-4 rounded-xl text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Book Now</span>
</a>

<!-- Footer -->
<footer class="bg-[#f1f4f6] w-full py-16">
  <div class="flex flex-col md:flex-row justify-between items-center px-6 md:px-12 gap-8 max-w-[1440px] mx-auto">
    <div class="font-headline font-black text-slate-400 text-2xl tracking-tighter">CLINIC SCHOLAR</div>
    <div class="flex flex-wrap justify-center gap-8 font-body text-xs uppercase tracking-widest">
      <a href="mailto:s230005904@seu.edu.sa" class="text-slate-500 hover:text-blue-600 underline-offset-4 hover:underline transition-all duration-200">Contact Support</a>
      <a href="doctors.php" class="text-slate-500 hover:text-blue-600 underline-offset-4 hover:underline transition-all duration-200">Our Doctors</a>
      <a href="#" class="text-slate-500 hover:text-blue-600 underline-offset-4 hover:underline transition-all duration-200">Medical Disclosure</a>
      <a href="#" class="text-slate-500 hover:text-blue-600 underline-offset-4 hover:underline transition-all duration-200">Privacy</a>
    </div>
    <div class="text-[10px] text-slate-400 font-body uppercase tracking-tighter text-center md:text-right">
      © 2026 University Clinical Standard.<br/>The Disciplined Curator System.
    </div>
  </div>
</footer>

</body>
</html>
