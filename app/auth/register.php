<?php
require_once __DIR__ . '/../config.php';

$err = '';
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $err = 'Invalid session.';
  }

  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password']  ?? '';
  $pass2 = $_POST['password2'] ?? '';
  $countryId = !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null;
  $stateId = !empty($_POST['state_id']) ? (int)$_POST['state_id'] : null;
  $cityName = trim($_POST['city_name'] ?? '');

  if (!$err && strlen($name) < 2) $err = 'Name is too short.';
  if (!$err && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Invalid email.';
  if (!$err && strlen($pass) < 8) {
    $err = 'Password must be at least 8 characters.';
  } elseif (!$err && (
    !preg_match('/[A-Z]/', $pass) || !preg_match('/[a-z]/', $pass) ||
    !preg_match('/[0-9]/', $pass) || !preg_match('/[^A-Za-z0-9]/', $pass)
  )) {
    $err = 'Password must include uppercase, lowercase, numbers and special characters.';
  }
  if (!$err && $pass !== $pass2) $err = 'Password confirmation does not match.';
  if (!$err) {
    $stmt = db()->prepare("SELECT 1 FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $err = 'Email already exists.';
  }
  if (!$err) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    try {
      $stmt = db()->prepare("SELECT COUNT(*) FROM users WHERE ip_address = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
      $stmt->execute([$ip]);
      if ((int)$stmt->fetchColumn() >= 5) $err = 'You have registered too many times. Please try again later.';
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
    // Session đã được init trong config.php
    session_regenerate_id(true);
    login_user($user);
    redirect('?p=studio');
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="<?= asset('images/S.png') ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | PhotoBooth</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/auth.css') ?>?v=<?= time() ?>&t=<?= rand(1000,9999) ?>">
</head>
<body>

<section class="auth-page auth-container">
  <div class="container-fluid g-0">
    <div class="row g-0">
      <!-- Left -->
      <div class="col-12 col-md-6 auth-left bg-left">
        <div class="auth-left-inner px-3 px-lg-4">
          <form method="post" style="width:20rem" autocomplete="off" novalidate>
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <h3 class="fw-normal mb-2 pb-1" style="font-size:1.5rem">Create new account</h3>

            <?php if ($err): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($err, ENT_QUOTES) ?></div>
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label" style="font-size:0.9rem">Username</label>
              <input name="name" type="text" class="form-control" style="font-size:0.9rem;padding:0.5rem"
                     value="<?= htmlspecialchars($name ?? '', ENT_QUOTES) ?>" required minlength="2">
            </div>

            <div class="mb-3">
              <label class="form-label" style="font-size:0.9rem">Email</label>
              <input name="email" type="email" class="form-control" style="font-size:0.9rem;padding:0.5rem"
                     value="<?= htmlspecialchars($email ?? '', ENT_QUOTES) ?>" required>
            </div>

            <div class="row mb-3 g-2">
              <div class="col-4">
                <label class="form-label" style="font-size:0.9rem;margin-bottom:0.4rem;display:block">Country</label>
                <select name="country_id" id="country" class="form-select" style="font-size:0.85rem;padding:0.5rem;height:38px"><option value=""></option></select>
              </div>
              <div class="col-4">
                <label class="form-label" style="font-size:0.9rem;margin-bottom:0.4rem;display:block">State/Province</label>
                <select name="state_id" id="state" class="form-select" style="font-size:0.85rem;padding:0.5rem;height:38px"><option value=""></option></select>
              </div>
              <div class="col-4">
                <label class="form-label" style="font-size:0.9rem;margin-bottom:0.4rem;display:block">City</label>
                <select name="city_name" id="city" class="form-select" style="font-size:0.85rem;padding:0.5rem;height:38px"><option value=""></option></select>
              </div>
            </div>

            <div class="mb-2" style="position:relative">
              <label class="form-label" style="font-size:0.9rem">Password</label>
              <input id="password" name="password" type="password" class="form-control" style="font-size:0.9rem;padding:0.5rem" required minlength="8" autocomplete="new-password">
            </div>
            <div class="mb-3">
              <small class="form-text" style="font-size:0.75rem;color:#ffffff">Password must be <strong>at least 8 characters</strong> and include <strong>uppercase letters</strong>, <strong>lowercase letters</strong>, <strong>numbers</strong>, and <strong>special characters</strong>.</small>
            </div>

            <div class="mb-3" style="position:relative">
              <label class="form-label" style="font-size:0.9rem">Re-enter password</label>
              <input id="password2" name="password2" type="password" class="form-control" style="font-size:0.9rem;padding:0.5rem" required autocomplete="new-password">
            </div>
            <div class="pt-1 mb-3 d-grid">
              <button class="btn btn-info" style="font-size:0.9rem;padding:0.5rem" type="submit">Sign up</button>
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
      // If no cities, automatically set city = state (for countries like Vietnam)
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
    // If error, automatically set city = state (for countries like Vietnam)
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
  
  // Function to handle state change
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
      // Remove old event listener if any
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
    .catch(() => alert('Cannot load countries'));
});
</script>
</body>
</html>
