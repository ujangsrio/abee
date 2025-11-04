<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - ArethaBeauty</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f6f3f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-box {
      background: white;
      padding: 40px;
      width: 400px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      border-radius: 0;
      position: relative;
    }

    .login-box h2 {
      margin-bottom: 20px;
      text-align: center;
      color: #4d4d4d;
      font-size: 20px;
    }

    label {
      display: block;
      font-size: 13px;
      margin-bottom: 5px;
      color: #444;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 6px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 0;
      font-size: 13px;
      background-color: #f9f0f5 !important;
      transition: border 0.3s;
      color: #000;
    }

    input:focus {
      border-color: #b47bb3;
      outline: none;
    }

    input:-webkit-autofill {
      box-shadow: 0 0 0px 1000px #f9f0f5 inset !important;
      -webkit-text-fill-color: #000 !important;
    }

    button {
      width: 100%;
      background-color: #b47bb3;
      border: none;
      color: white;
      padding: 10px;
      border-radius: 0;
      font-weight: bold;
      cursor: pointer;
      font-size: 14px;
      margin-top: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      transition: background 0.3s;
    }

    button:hover {
      background-color: #a066a5;
    }

    .error {
      color: #d32f2f;
      font-size: 14px;
      margin-bottom: 10px;
      text-align: center;
      background-color: #ffebee;
      padding: 10px;
      border-radius: 4px;
      border-left: 4px solid #d32f2f;
    }

    .success {
      color: #388e3c;
      font-size: 14px;
      margin-bottom: 10px;
      text-align: center;
      background-color: #e8f5e9;
      padding: 10px;
      border-radius: 4px;
      border-left: 4px solid #388e3c;
    }

    .register-link {
      text-align: center;
      margin-top: 15px;
    }

    .register-link a {
      color: #6e6e6e;
      text-decoration: underline;
      font-size: 12px;
    }

    /* Notification Styles */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 20px;
      border-radius: 8px;
      color: white;
      font-size: 14px;
      font-weight: 500;
      z-index: 1000;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      transform: translateX(400px);
      opacity: 0;
      transition: all 0.3s ease-in-out;
      max-width: 350px;
    }

    .notification.show {
      transform: translateX(0);
      opacity: 1;
    }

    .notification.success {
      background: linear-gradient(135deg, #4CAF50, #45a049);
      border-left: 4px solid #2E7D32;
    }

    .notification.error {
      background: linear-gradient(135deg, #f44336, #e53935);
      border-left: 4px solid #c62828;
    }

    .notification.warning {
      background: linear-gradient(135deg, #ff9800, #f57c00);
      border-left: 4px solid #ef6c00;
    }

    .notification .close-btn {
      background: none;
      border: none;
      color: white;
      font-size: 16px;
      cursor: pointer;
      margin-left: 15px;
      padding: 0;
      width: auto;
    }

    .notification .close-btn:hover {
      background: none;
      opacity: 0.8;
    }

    /* Input error state */
    .input-error {
      border-color: #f44336 !important;
      background-color: #ffebee !important;
    }

    .error-text {
      color: #f44336;
      font-size: 12px;
      margin-top: -10px;
      margin-bottom: 10px;
      display: block;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>
    
    <!-- Session Messages -->
    <?php if(session('success')): ?>
      <div class="success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="error"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <!-- Validation Errors -->
    <?php if($errors->any()): ?>
      <div class="error">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo e($error); ?><?php if(!$loop->last): ?><br><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('login.submit')); ?>" id="loginForm">
      <?php echo csrf_field(); ?>
      
      <label>Email:</label>
      <input type="email" name="email" value="<?php echo e(old('email')); ?>" required 
             class="<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
      <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="error-text"><?php echo e($message); ?></span>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

      <label>Password:</label>
      <input type="password" name="password" required 
             class="<?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
      <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="error-text"><?php echo e($message); ?></span>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

      <button type="submit">Login</button>

      <div class="register-link">
        <a href="<?php echo e(route('customer.register')); ?>">Belum punya akun? Daftar</a>
      </div>
    </form>
  </div>

  <!-- Notification Container -->
  <div id="notification" class="notification" style="display: none;">
    <span id="notification-message"></span>
    <button class="close-btn" onclick="hideNotification()">×</button>
  </div>

  <script>
    // Show notification function
    function showNotification(message, type = 'success') {
      const notification = document.getElementById('notification');
      const messageElement = document.getElementById('notification-message');
      
      notification.className = `notification ${type}`;
      messageElement.textContent = message;
      notification.style.display = 'block';
      
      setTimeout(() => {
        notification.classList.add('show');
      }, 100);

      // Auto hide after 5 seconds
      setTimeout(() => {
        hideNotification();
      }, 5000);
    }

    function hideNotification() {
      const notification = document.getElementById('notification');
      notification.classList.remove('show');
      setTimeout(() => {
        notification.style.display = 'none';
      }, 300);
    }

    // Show session messages as notifications
    document.addEventListener('DOMContentLoaded', function() {
      <?php if(session('success')): ?>
        showNotification('<?php echo e(session('success')); ?>', 'success');
      <?php endif; ?>

      <?php if(session('error')): ?>
        showNotification('<?php echo e(session('error')); ?>', 'error');
      <?php endif; ?>

      // Form submission handling
      const form = document.getElementById('loginForm');
      form.addEventListener('submit', function(e) {
        const email = form.querySelector('input[name="email"]').value;
        const password = form.querySelector('input[name="password"]').value;
        
        // Basic client-side validation
        if (!email || !password) {
          e.preventDefault();
          showNotification('Harap isi semua field yang diperlukan.', 'error');
        }
      });

      // Clear input errors on focus
      const inputs = form.querySelectorAll('input');
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.classList.remove('input-error');
        });
      });
    });
  </script>
</body>
</html><?php /**PATH C:\Users\ASVS\Documents\PBL S5\abee\resources\views/auth/login.blade.php ENDPATH**/ ?>