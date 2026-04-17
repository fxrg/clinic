<?php
require_once 'db.php';
// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: history.php");
    exit;
}
$register_errors = $_SESSION['register_errors'] ?? [];
$login_errors    = $_SESSION['login_errors'] ?? [];
$register_input  = $_SESSION['register_input'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['login_errors'], $_SESSION['register_input']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="Register or login to Clinic Scholar - Access the University Clinical Standard appointment system."/>
<title>Clinic Scholar | Access Gateway</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
      darkMode:"class",
      theme:{extend:{colors:{"secondary-container":"#e2e2e9","on-secondary-container":"#505157","primary-fixed":"#d7e2ff","on-surface":"#2b3437","surface-container-highest":"#dbe4e7","on-primary-container":"#004fa6","outline":"#737c7f","surface-container-high":"#e3e9ec","primary-fixed-dim":"#c2d5ff","surface-container-low":"#f1f4f6","primary-container":"#d7e2ff","primary":"#115cb9","surface-container":"#eaeff1","surface":"#f8f9fa","on-surface-variant":"#586064","on-primary":"#f7f7ff","outline-variant":"#abb3b7","tertiary":"#5e5c78","secondary":"#5d5f65","primary-dim":"#0050a7","surface-container-lowest":"#ffffff","secondary-fixed":"#e2e2e9"},borderRadius:{"DEFAULT":"0.125rem","lg":"0.25rem","xl":"0.5rem","full":"0.75rem"},fontFamily:{"headline":["Public Sans"],"body":["Inter"],"label":["Inter"]}}}
    }
</script>
<style>
    .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
    .auth-split-gradient{background:linear-gradient(135deg,#115cb9 0%,#0050a7 100%);}
    .ghost-border{outline:1px solid rgba(171,179,183,0.15);}
    .input-field{width:100%;background-color:#dbe4e7;border:none;border-bottom:2px solid rgba(115,124,127,0.3);border-radius:0.375rem 0.375rem 0 0;padding:0.75rem 1rem;transition:all 0.15s;}
    .input-field:focus{outline:none;border-bottom-color:#115cb9;background-color:#ffffff;}
</style>
</head>
<body class="bg-surface font-body text-on-surface antialiased">

<!-- Nav -->
<nav class="fixed top-0 w-full z-50 bg-[#f8f9fa]/80 backdrop-blur-lg shadow-sm">
  <div class="flex justify-between items-center h-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <a href="index.php" class="text-xl font-bold tracking-tighter text-blue-800">Clinic Scholar</a>
    <div class="flex items-center gap-6">
      <a href="index.php" class="font-headline text-sm tracking-tight text-slate-600 hover:text-blue-500 transition-colors">← Back to Home</a>
    </div>
  </div>
  <div class="bg-[#f1f4f6] h-[1px] w-full absolute bottom-0 opacity-10"></div>
</nav>

<main class="min-h-screen flex items-stretch pt-20">

  <!-- Left: Register -->
  <section class="hidden lg:flex w-1/2 bg-surface-container-low relative overflow-hidden items-center justify-center p-12" id="register-section">
    <div class="absolute inset-0 opacity-30">
      <img class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=800&q=80" alt="Clinical facility"/>
    </div>
    <div class="relative z-10 w-full max-w-lg">
      <div class="mb-10">
        <h1 class="font-headline text-4xl font-extrabold tracking-tighter text-on-surface mb-4">Join the Scholar Network</h1>
        <p class="text-on-surface-variant font-medium leading-relaxed">Access the University Clinical Standard for streamlined healthcare management and peer-reviewed clinical scheduling.</p>
      </div>
      <!-- PHP Server-side register errors display (shown if JS is off) -->
      <?php if (!empty($register_errors)): ?>
      <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <p class="text-sm font-bold text-red-700 mb-2">Please fix the following errors:</p>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
          <?php foreach ($register_errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm ghost-border">
        <h2 class="font-headline text-xl font-bold mb-8 text-primary">Register</h2>
        <form id="registerForm" action="process/register.php" method="POST" class="space-y-6" novalidate>
          <input type="hidden" name="action" value="register"/>
          <div class="space-y-2">
            <label class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="reg-name">Full Name</label>
            <input id="reg-name" class="input-field" name="full_name" placeholder="Your Full Name" type="text" value="<?= htmlspecialchars($register_input['full_name'] ?? '') ?>" autocomplete="name"/>
          </div>
          <div class="space-y-2">
            <label class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="reg-email">Academic Email</label>
            <input id="reg-email" class="input-field" name="email" placeholder="your.email@university.edu" type="email" value="<?= htmlspecialchars($register_input['email'] ?? '') ?>" autocomplete="email"/>
          </div>
          <div class="space-y-2">
            <label class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="reg-pass">Secure Password</label>
            <input id="reg-pass" class="input-field" name="password" placeholder="•••••••••" type="password" autocomplete="new-password"/>
          </div>
          <div class="space-y-2">
            <label class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="reg-confirm">Confirm Password</label>
            <input id="reg-confirm" class="input-field" name="confirm_password" placeholder="•••••••••" type="password" autocomplete="new-password"/>
          </div>
          <div class="pt-4">
            <button type="submit" id="registerBtn" class="w-full bg-primary text-on-primary py-4 rounded-xl font-bold text-sm tracking-tight hover:opacity-90 transition-all shadow-lg shadow-primary/20">
              Create Scholar Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Right: Login -->
  <section class="w-full lg:w-1/2 auth-split-gradient flex items-center justify-center p-8 md:p-12" id="login-section">
    <div class="w-full max-w-md bg-surface-container-lowest/10 backdrop-blur-xl p-1 shadow-2xl rounded-2xl">
      <div class="bg-surface-container-lowest p-10 lg:p-12 rounded-2xl">
        <div class="flex justify-center mb-10">
          <div class="w-16 h-16 rounded-full bg-primary-container flex items-center justify-center">
            <span class="material-symbols-outlined text-primary text-3xl">lock_open</span>
          </div>
        </div>
        <div class="text-center mb-10">
          <h2 class="font-headline text-3xl font-black tracking-tighter text-on-surface">Sign In</h2>
          <p class="text-on-surface-variant text-sm mt-2">Welcome back to the Disciplined Curator system.</p>
        </div>
        <!-- PHP Login errors -->
        <?php if (!empty($login_errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
          <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            <?php foreach ($login_errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
        <form id="loginForm" action="process/login.php" method="POST" class="space-y-6" novalidate>
          <input type="hidden" name="action" value="login"/>
          <div class="space-y-2">
            <label class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="login-email">Email Address</label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-lg">person</span>
              <input id="login-email" class="w-full bg-surface-container border-0 border-b-2 border-outline/20 focus:border-primary focus:ring-0 focus:bg-surface-container-lowest transition-all pl-12 pr-4 py-4 rounded-t-lg text-sm" name="email" type="email" placeholder="your.email@university.edu" autocomplete="email"/>
            </div>
          </div>
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <label class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-1" for="login-pass">Password</label>
              <a class="text-[10px] uppercase font-bold text-primary tracking-widest hover:underline" href="#">Forgot?</a>
            </div>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-lg">key</span>
              <input id="login-pass" class="w-full bg-surface-container border-0 border-b-2 border-outline/20 focus:border-primary focus:ring-0 focus:bg-surface-container-lowest transition-all pl-12 pr-4 py-4 rounded-t-lg text-sm" name="password" type="password" placeholder="••••••••" autocomplete="current-password"/>
            </div>
          </div>
          <div class="flex items-center gap-3 py-2">
            <input class="w-4 h-4 rounded-sm border-outline/30 text-primary focus:ring-primary/20" id="remember" name="remember" type="checkbox"/>
            <label class="text-xs font-medium text-on-surface-variant" for="remember">Keep me authenticated for 30 days</label>
          </div>
          <button type="submit" id="loginBtn" class="w-full bg-primary text-on-primary py-4 rounded-xl font-bold text-sm tracking-tight hover:opacity-90 transition-all shadow-lg shadow-primary/20">
            Sign In to Dashboard
          </button>
        </form>
        <!-- Mobile Register Button -->
        <div class="lg:hidden text-center mt-8 pt-8 border-t border-surface-container">
          <p class="text-xs text-on-surface-variant mb-4">Don't have an account yet?</p>
          <a href="auth.php#register" class="block w-full bg-secondary-container text-on-secondary-container py-3 rounded-xl font-bold text-xs tracking-widest uppercase text-center">
            Register Now
          </a>
        </div>
        <p class="text-center text-xs text-on-surface-variant mt-6">
          Demo: <strong>demo@clinic.edu</strong> / <strong>Password123</strong>
        </p>
      </div>
    </div>
  </section>

</main>

<!-- Footer -->
<footer class="bg-[#f1f4f6] w-full py-12">
  <div class="flex flex-col md:flex-row justify-between items-center px-12 gap-8 max-w-[1440px] mx-auto">
    <div class="font-headline font-black text-slate-400">© 2026 University Clinical Standard. The Disciplined Curator System.</div>
    <div class="flex flex-wrap justify-center gap-8">
      <a class="font-body text-xs uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-all hover:underline underline-offset-4" href="#">Contact Support</a>
      <a class="font-body text-xs uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-all hover:underline underline-offset-4" href="#">Privacy</a>
    </div>
  </div>
</footer>

<script>
// ===== JavaScript Form Validation (JS Requirement) =====
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const name    = document.getElementById('reg-name').value.trim();
    const email   = document.getElementById('reg-email').value.trim();
    const pass    = document.getElementById('reg-pass').value;
    const confirm = document.getElementById('reg-confirm').value;
    const errors  = [];

    if (!name) errors.push('Full name is required.');
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('A valid email address is required.');
    if (pass.length < 8) errors.push('Password must be at least 8 characters long.');
    if (pass !== confirm) errors.push('Passwords do not match.');

    if (errors.length > 0) {
        e.preventDefault();
        alert('⚠️ Please fix the following errors:\n\n• ' + errors.join('\n• '));
    }
});

document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email = document.getElementById('login-email').value.trim();
    const pass  = document.getElementById('login-pass').value;
    const errors = [];

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('A valid email address is required.');
    if (!pass) errors.push('Password is required.');

    if (errors.length > 0) {
        e.preventDefault();
        alert('⚠️ Please fix the following errors:\n\n• ' + errors.join('\n• '));
    }
});

// Scroll to the right section based on hash
window.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#register') {
        document.getElementById('register-section').scrollIntoView({ behavior: 'smooth' });
    }
});
</script>
</body>
</html>
