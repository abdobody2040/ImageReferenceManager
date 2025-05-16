<?php
$pageTitle = 'Login - ' . getSetting('app_name', 'PharmaEvents');
$app_name = getSetting('app_name', 'PharmaEvents');
$logo = getSetting('logo', '/static/img/logo.png');

ob_start();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <img src="<?php echo $logo; ?>" alt="<?php echo $app_name; ?> Logo" class="img-fluid mb-3" style="max-height: 80px;">
                                    <h1 class="h4 text-gray-900 mb-2"><?php echo $app_name; ?></h1>
                                    <p class="text-muted">Welcome back! Please log in to continue.</p>
                                </div>
                                
                                <!-- Enhanced login form with better error handling -->
                                <form class="user" action="/login" method="post" id="login_form">
                                    <div class="form-group mb-3">
                                        <input type="email" class="form-control form-control-user" id="email" name="email" 
                                            placeholder="Enter Email Address..." required
                                            value="<?php echo isset($_SESSION['form_email']) ? htmlspecialchars($_SESSION['form_email']) : ''; ?>">
                                        <div class="invalid-feedback">Please enter a valid email address</div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="password" class="form-control form-control-user" id="password" name="password" 
                                            placeholder="Password" required>
                                        <div class="invalid-feedback">Password is required</div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                    <div class="loading-overlay" style="display: none;">
                                        <div class="spinner-border text-light" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block" id="login_button">
                                        Login
                                    </button>
                                </form>
                                
                                <!-- Login form validation script -->
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const form = document.getElementById('login_form');
                                    const loginButton = document.getElementById('login_button');
                                    
                                    if (form) {
                                        form.addEventListener('submit', function(e) {
                                            // Basic validation
                                            let isValid = true;
                                            const email = document.getElementById('email');
                                            const password = document.getElementById('password');
                                            
                                            // Reset validation state
                                            email.classList.remove('is-invalid');
                                            password.classList.remove('is-invalid');
                                            
                                            // Validate email
                                            if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                                                email.classList.add('is-invalid');
                                                isValid = false;
                                            }
                                            
                                            // Validate password
                                            if (!password.value.trim()) {
                                                password.classList.add('is-invalid');
                                                isValid = false;
                                            }
                                            
                                            if (!isValid) {
                                                e.preventDefault();
                                                return false;
                                            }
                                            
                                            // Show loading overlay
                                            const loadingOverlay = document.querySelector('.loading-overlay');
                                            if (loadingOverlay) {
                                                loadingOverlay.style.display = 'flex';
                                            }
                                            
                                            // Disable button
                                            loginButton.disabled = true;
                                            loginButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Signing in...';
                                        });
                                    }
                                });
                                </script>
                                
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="/forgot-password">Forgot Password?</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>