<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .login-header {
            background: linear-gradient(135deg, #0f3460, #1a1a2e);
            border-radius: 16px 16px 0 0;
            padding: 30px;
            text-align: center;
        }
        .btn-login {
            background: linear-gradient(135deg, #1a1a2e, #0f3460);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .btn-login:hover { background: linear-gradient(135deg, #0f3460, #16213e); box-shadow: 0 4px 15px rgba(15,52,96,0.5); transform: translateY(-1px); transition: all 0.2s ease; }
        .btn-login { transition: all 0.2s ease; }
        .form-control:focus { border-color: #e94560; box-shadow: 0 0 0 0.2rem rgba(233,69,96,0.25); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card">
                <div class="login-header">
                    <i class="fas fa-wifi fa-3x text-white mb-3"></i>
                    <h4 class="text-white fw-bold mb-0">ISP BILLING SYSTEM</h4>
                    <small class="text-white-50">Panel Administrator</small>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Email atau password salah!
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-danger"></i> Email
                            </label>
                            <input type="email" name="email" class="form-control form-control-lg"
                                   placeholder="admin@isp.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-danger"></i> Password
                            </label>
                            <input type="password" name="password" class="form-control form-control-lg"
                                   placeholder="••••••••" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-login btn-lg text-white">
                                <i class="fas fa-sign-in-alt me-2"></i> MASUK
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center text-muted py-3">
                    <small>ISP Billing System &copy; {{ date('Y') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
