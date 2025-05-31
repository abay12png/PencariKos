<?php
session_start();
if (isset($_SESSION['email_admin'])) { 
    header("Location: menu.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login Admin - Boarding House</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background:
                linear-gradient(135deg, rgba(11, 61, 32, 0.80), rgba(24, 74, 42, 0.80)),
                url('image/background.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .fade-in { animation: slideFadeIn 0.8s ease-out forwards; opacity: 0; transform: translateY(30px); }
        @keyframes slideFadeIn { to { opacity: 1; transform: translateY(0); } }
        .login-card {
            border-radius: 1rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            max-width: 900px; 
            width: 100%;
            background: white;
            display: flex;
        }
        .left-panel { padding: 2.5rem 3rem; flex: 1 1 55%; color: #333; }
        .right-panel {
            background: linear-gradient(135deg, #104f28, #38761d);
            align-items: center;
            justify-content: center;
            flex: 1 1 45%;
        }
        .right-panel img { max-width: 75%; filter: drop-shadow(0 0 15px rgba(0,0,0,0.3)); } 
        .form-control:focus { border-color: #28a745; box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25); }
        .input-group .input-group-text { background-color: #fff; border-left: 0; }
        .input-group .form-control { border-right: 0; }
        .input-group > .form-control:not(:last-child) { border-top-right-radius: 0; border-bottom-right-radius: 0; }
        .input-group > .input-group-text:not(:first-child) { border-top-left-radius: 0; border-bottom-left-radius: 0; }
        #togglePasswordSpan { cursor: pointer; color: #6c757d; }
        #togglePasswordSpan:hover i { color: #28a745; }
        .btn-login { background-color: #228644; border-color: #228644; padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .btn-login:hover { background-color: #1a6833; border-color: #1a6833; transform: translateY(-2px); }
        .btn-login:active { transform: translateY(0px); }
        @media (max-width: 768px) {
            .login-card { flex-direction: column; max-width: 500px; }
            .right-panel { min-height: 200px; order: -1; } 
            .right-panel img { max-width: 50%; }
            .left-panel { padding: 2rem; }
        }
    </style>
</head>
<body>
    <div class="login-card fade-in">
        <div class="left-panel">
            <div class="text-center mb-4">
                <h1 class="fw-bold text-success mb-1" style="color: #1a6833;">Admin KosApp</h1>
                <p class="text-muted">Silakan login untuk melanjutkan</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 0.9rem; padding: 0.75rem 1rem;">
                    <?php
                        if ($_GET['error'] == '1') {
                            echo "Email atau password salah. Silakan coba lagi.";
                        } elseif ($_GET['error'] == '2') {
                            echo "Email dan password wajib diisi.";
                        } else {
                            echo "Terjadi kesalahan. Silakan coba lagi.";
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem 1rem;"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['logout'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 0.9rem; padding: 0.75rem 1rem;">
                    Anda telah berhasil logout.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem 1rem;"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="admin.php" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Masukkan Email Anda" required />
                    <div class="invalid-feedback">Mohon masukkan email yang valid.</div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group input-group-lg">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password Anda" required />
                        <span class="input-group-text" id="togglePasswordSpan">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                    <div class="invalid-feedback">Mohon masukkan password.</div>
                </div>
                <button type="submit" class="btn btn-login btn-success w-100 fw-semibold">Login</button>
            </form>
            <div class="text-center mt-4">
                <small class="text-muted">&copy; <?= date("Y") ?> KosApp</small>
            </div>
        </div>
        <div class="right-panel d-none d-md-flex"> \
            <img src="image/hotel.png" alt="Ilustrasi Boarding House" />
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            const togglePasswordSpan = document.querySelector('#togglePasswordSpan');
            const passwordInput = document.querySelector('#password');
            const toggleIcon = document.querySelector('#togglePasswordIcon');

            if (togglePasswordSpan && passwordInput && toggleIcon) {
                togglePasswordSpan.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    toggleIcon.classList.toggle('bi-eye');
                    toggleIcon.classList.toggle('bi-eye-slash');
                });
            }
        })();
    </script>
</body>
</html>