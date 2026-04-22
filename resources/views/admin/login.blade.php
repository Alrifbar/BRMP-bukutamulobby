<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Buku Tamu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin-login.css') }}" rel="stylesheet">
    <style>
        :root {
            --bg-image: url('{{ asset('images/bc depan.jpg') }}');
        }
    </style>
</head>
<body>
    <!-- Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle" title="Toggle Theme">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
    </button>
    
    
    <div class="background-overlay"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <img src="{{ \App\Models\Setting::getLoginLogoUrl() }}" alt="Logo" class="logo-img">
                <h1 class="welcome-text">{{ \App\Models\Setting::getValue('admin_login_title', 'Hello, welcome!') }}</h1>
                <p class="subwelcome-text">{{ \App\Models\Setting::getValue('admin_login_subtitle', 'Silakan login untuk mengakses sistem buku tamu digital') }}</p>
            </div>

            <div class="login-form">
                @if ($errors->any())
                    <div class="error-message">
                        {{ $errors->first('email') ?: $errors->first('password') ?: 'Email atau password salah' }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email / ID Admin</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="email" 
                                name="email" 
                                class="form-input"
                                required
                                value="{{ old('email') }}"
                                autocomplete="username"
                            >
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input"
                                required
                                autocomplete="current-password"
                            >
                            <i class="bi bi-lock input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="bi bi-eye-slash" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-button" id="loginBtn">
                        <span id="btnText">Login</span>
                    </button>
                </form>

                <div class="login-footer">
                    <div style="margin-bottom: 12px;">
                        <a href="{{ route('buku-tamu.create') }}" style="color: var(--accent-color); text-decoration: none; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; transition: opacity 0.2s;">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Form Buku Tamu</span>
                        </a>
                    </div>
                    <span style="color: #64748b; font-size: 14px;">© {{ now()->year }} Buku Tamu Admin</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        }
        document.getElementById('togglePassword').addEventListener('click', togglePassword);

        // Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            btnText.textContent = 'Logging in...';
        });

        // Add input focus effects
        

        // Initialize particles on load
        

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const activeElement = document.activeElement;
                if (activeElement.id === 'email') {
                    e.preventDefault();
                    document.getElementById('password').focus();
                }
            }
        });
        
        // Theme Synchronization
        function applyTheme(theme) {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            
            // Remove existing theme classes
            body.classList.remove('light-mode', 'dark-mode');
            
            // Apply new theme
            if (theme === 'dark') {
                body.classList.add('dark-mode');
                themeIcon.className = 'bi bi-sun-fill';
            } else {
                body.classList.add('light-mode');
                themeIcon.className = 'bi bi-moon-fill';
            }
            
            // Save to localStorage
            localStorage.setItem('admin-login-theme', theme);
            localStorage.setItem('theme', theme);
        }
        
        // Theme toggle handler
        document.getElementById('themeToggle').addEventListener('click', function() {
            const currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        });
        
        // Check for theme changes from other pages
        function checkThemeSync() {
            const globalTheme = localStorage.getItem('theme');
            const currentTheme = localStorage.getItem('admin-login-theme');
            
            if (globalTheme && globalTheme !== currentTheme) {
                applyTheme(globalTheme);
            }
        }
        
        // Initial theme setup
        function initializeTheme() {
            // Check global theme first
            const globalTheme = localStorage.getItem('theme');
            const savedTheme = localStorage.getItem('admin-login-theme');
            
            // Use global theme if available, otherwise use saved theme, default to light
            const theme = globalTheme || savedTheme || 'light';
            applyTheme(theme);
        }
        
        // Initialize theme on load
        initializeTheme();
        
        
    </script>
</body>
</html>
