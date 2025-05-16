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
                                
                                <form class="user" action="/login" method="post">
                                    <div class="form-group mb-3">
                                        <input type="email" class="form-control form-control-user" id="email" name="email" 
                                            placeholder="Enter Email Address..." required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="password" class="form-control form-control-user" id="password" name="password" 
                                            placeholder="Password" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                </form>
                                
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