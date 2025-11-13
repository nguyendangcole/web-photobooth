<?php
require_once __DIR__ . '/../config.php';

$err = '';
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $err = 'Phiên không hợp lệ.';
  }

  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password']  ?? '';
  $pass2 = $_POST['password2'] ?? '';
  $countryId = !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null;
  $stateId = !empty($_POST['state_id']) ? (int)$_POST['state_id'] : null;
  $cityName = trim($_POST['city_name'] ?? '');

  if (!$err && strlen($name) < 2) $err = 'Tên quá ngắn.';
  if (!$err && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Email không hợp lệ.';
  if (!$err && strlen($pass) < 8) {
    $err = 'Mật khẩu phải có ít nhất 8 ký tự.';
  } elseif (!$err && (
    !preg_match('/[A-Z]/', $pass) || !preg_match('/[a-z]/', $pass) ||
    !preg_match('/[0-9]/', $pass) || !preg_match('/[^A-Za-z0-9]/', $pass)
  )) {
    $err = 'Mật khẩu phải bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.';
  }
  if (!$err && $pass !== $pass2) $err = 'Mật khẩu xác nhận không khớp.';
  if (!$err) {
    $stmt = db()->prepare("SELECT 1 FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $err = 'Email đã tồn tại.';
  }
  if (!$err) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    try {
      $stmt = db()->prepare("SELECT COUNT(*) FROM users WHERE ip_address = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
      $stmt->execute([$ip]);
      if ((int)$stmt->fetchColumn() >= 5) $err = 'Bạn đã đăng ký quá nhiều lần. Vui lòng thử lại sau.';
    } catch (Exception $e) {}
  }

  if (!$err) {
    $hash = defined('PASSWORD_ARGON2ID') ? password_hash($pass, PASSWORD_ARGON2ID) : password_hash($pass, PASSWORD_DEFAULT);
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    try {
      $stmt = db()->prepare("INSERT INTO users(name,email,password_hash,provider,ip_address,country_id,state_id,city_name) VALUES(?,?,?,'local',?,?,?,?)");
      $stmt->execute([$name, $email, $hash, $ip, $countryId, $stateId, $cityName]);
    } catch (Exception $e) {
      $stmt = db()->prepare("INSERT INTO users(name,email,password_hash,provider) VALUES(?,?,?,'local')");
      $stmt->execute([$name, $email, $hash]);
    }
    $id = db()->lastInsertId();
    $user = ['id'=>$id,'name'=>$name,'email'=>$email,'avatar_url'=>null,'provider'=>'local'];
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    session_regenerate_id(true);
    login_user($user);
    redirect('?p=home');
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký | PhotoBooth</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/auth.css') ?>?v=<?= time() ?>">
</head>
<body>

<section class="auth-page auth-container">
  <div class="container-fluid g-0">
    <div class="row g-0">
      <!-- Left -->
      <div class="col-12 col-md-6 auth-left bg-left">
        <div class="auth-left-inner px-4 px-lg-5">
          <form method="post" style="width:23rem" autocomplete="off" novalidate>
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <h3 class="fw-normal mb-3 pb-1">Create new account</h3>

            <?php if ($err): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($err, ENT_QUOTES) ?></div>
            <?php endif; ?>

            <div class="mb-4">
              <label class="form-label">Username</label>
              <input name="name" type="text" class="form-control form-control-lg"
                     value="<?= htmlspecialchars($name ?? '', ENT_QUOTES) ?>" required minlength="2">
            </div>

            <div class="mb-4">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control form-control-lg"
                     value="<?= htmlspecialchars($email ?? '', ENT_QUOTES) ?>" required>
            </div>

            <div class="row mb-4">
              <div class="col-12 mb-3">
                <label class="form-label">Country</label>
                <select name="country_id" id="country" class="form-select"><option value="">-- Select Country --</option></select>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">State/Province</label>
                <select name="state_id" id="state" class="form-select"><option value="">-- Select State --</option></select>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">City</label>
                <select name="city_name" id="city" class="form-select"><option value="">-- Select City --</option></select>
              </div>
            </div>

            <div class="mb-2">
              <label class="form-label">Password</label>
              <input id="password" name="password" type="password" class="form-control form-control-lg" required minlength="8" autocomplete="new-password">
            </div>
            <div class="mb-4">
              <small class="form-text text-muted">Password must be <strong>at least 8 characters</strong> and include <strong>uppercase letters</strong>, <strong>lowercase letters</strong>, <strong>numbers</strong>, and <strong>special characters</strong>.</small>
            </div>

            <div class="mb-4">
              <label class="form-label">Re-enter password</label>
              <input id="password2" name="password2" type="password" class="form-control form-control-lg" required autocomplete="new-password">
            </div>
            <div class="pt-1 mb-4 d-grid">
              <button class="btn btn-info btn-lg btn-block" type="submit">Sign up</button>
            </div>
            <p>Do you already have an account? <a href="?p=login" class="link-info">Login</a></p>
          </form>
        </div>
      </div>

      <div class="col-12 col-md-6 d-none d-md-block position-relative">
        <div class="slideshow-container h-100">
          <div class="slide" style="background-image:url('<?= asset('images/bg1.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/bg2.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/bg3.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/bg4.png') ?>');"></div>
          <div class="slide" style="background-image:url('<?= asset('images/bg5.png') ?>');"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="<?= asset('js/auth.js') ?>"></script>
<script>
function loadCities(url, citySelect, stateName = null) {
  citySelect.innerHTML = '<option value="">Loading...</option>';
  fetch(url).then(r => r.text()).then(t => {
    const d = JSON.parse(t);
    citySelect.innerHTML = '<option value="">-- Select City --</option>';
    if (d.success && d.data.length > 0) {
      citySelect.disabled = false;
      d.data.forEach(c => {
        const o = document.createElement('option');
        o.value = c.name;
        o.textContent = c.name;
        citySelect.appendChild(o);
      });
    } else {
      // Nếu không có cities, tự động set city = state (cho các nước như Việt Nam)
      if (stateName) {
        citySelect.disabled = false;
        const o = document.createElement('option');
        o.value = stateName;
        o.textContent = stateName;
        o.selected = true;
        citySelect.appendChild(o);
      } else {
        citySelect.innerHTML = '<option value="">-- Select City --</option>';
        citySelect.disabled = false;
      }
    }
  }).catch(() => {
    // Nếu lỗi, tự động set city = state (cho các nước như Việt Nam)
    if (stateName) {
      citySelect.disabled = false;
      citySelect.innerHTML = '<option value="">-- Select City --</option>';
      const o = document.createElement('option');
      o.value = stateName;
      o.textContent = stateName;
      o.selected = true;
      citySelect.appendChild(o);
    } else {
      citySelect.innerHTML = '<option value="">-- Select City --</option>';
      citySelect.disabled = false;
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const s = document.getElementById('state');
  const c = document.getElementById('city');
  const co = document.getElementById('country');
  
  if (s) {
    s.disabled = true;
  }
  
  if (c) c.disabled = true;
  
  // Function để handle state change
  function handleStateChange() {
    const stateValue = this.value;
    const stateText = this.options[this.selectedIndex].text;
    if (stateValue) {
      c.disabled = false;
      c.innerHTML = '<option value="">Loading cities...</option>';
      loadCities(`../ajax/get_cities.php?state_id=${stateValue}`, c, stateText);
    } else {
      c.innerHTML = '<option value="">-- Select City --</option>';
      c.disabled = true;
    }
  }
  
  if (co) {
    co.addEventListener('change', function() {
      const v = this.value;
      // Remove old event listener nếu có
      const newS = s.cloneNode(true);
      s.parentNode.replaceChild(newS, s);
      const sNew = document.getElementById('state');
      
      if (v) {
        sNew.disabled = false;
        sNew.innerHTML = '<option value="">Loading states...</option>';
        fetch(`../ajax/get_states.php?country_id=${v}`)
          .then(r => r.text())
          .then(t => {
            const d = JSON.parse(t);
            if (d.success) {
              if (d.data.length === 0) {
                sNew.disabled = true;
                sNew.innerHTML = '<option value="">-- Not Available --</option>';
                if (c && v) {
                  c.disabled = false;
                  c.innerHTML = '<option value="">Loading cities...</option>';
                  loadCities(`../ajax/get_cities.php?country_id=${v}`, c);
                }
              } else {
                sNew.disabled = false;
                sNew.innerHTML = '<option value="">-- Select State --</option>';
                d.data.forEach(st => {
                  const o = document.createElement('option');
                  o.value = st.id;
                  o.textContent = st.name;
                  sNew.appendChild(o);
                });
                // Attach event listener cho state select
                sNew.addEventListener('change', handleStateChange);
              }
            } else {
              sNew.innerHTML = '<option value="">Error loading states</option>';
              sNew.disabled = true;
            }
          })
          .catch(() => {
            sNew.innerHTML = '<option value="">Error loading states</option>';
            sNew.disabled = true;
          });
      } else {
        sNew.innerHTML = '<option value="">-- Select State --</option>';
        sNew.disabled = true;
        if (c) {
          c.innerHTML = '<option value="">-- Select City --</option>';
          c.disabled = true;
        }
      }
    });
  }
  
  fetch('../ajax/get_countries.php')
    .then(r => r.text())
    .then(t => {
      const d = JSON.parse(t);
      if (d.success) {
        d.data.forEach(ct => {
          const o = document.createElement('option');
          o.value = ct.id;
          o.textContent = ct.name;
          co.appendChild(o);
        });
      }
    })
    .catch(() => alert('Không thể load countries'));
});
</script>
</body>
</html>
