<?php
require_once __DIR__ . '/../config.php';
if (!isset($_GET['code'])) redirect('?p=login');

$code=$_GET['code'];

// 1) Exchange code → access token
$tokenResp = http_get_json('https://graph.facebook.com/v19.0/oauth/access_token', [
  'client_id'=>FB_APP_ID,
  'redirect_uri'=>FB_REDIRECT_URI,
  'client_secret'=>FB_APP_SECRET,
  'code'=>$code
]);
if (empty($tokenResp['access_token'])) redirect('?p=login');
$token=$tokenResp['access_token'];

// 2) Lấy profile
$me = http_get_json('https://graph.facebook.com/me', [
  'fields'=>'id,name,email,picture.type(large)',
  'access_token'=>$token
]);
$pid   = $me['id'] ?? null;
$name  = $me['name'] ?? 'Facebook User';
$email = $me['email'] ?? ($pid.'@facebook.local'); // FB có thể không trả email
$avatar= $me['picture']['data']['url'] ?? null;
if (!$pid) redirect('?p=login');

// 3) Upsert
$stmt=db()->prepare("SELECT * FROM users WHERE provider='facebook' AND provider_id=? LIMIT 1");
$stmt->execute([$pid]); $u=$stmt->fetch();
if (!$u){
  $stmt=db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->execute([$email]); $u=$stmt->fetch();
  if ($u){
    db()->prepare("UPDATE users SET provider='facebook', provider_id=?, avatar_url=? WHERE id=?")
       ->execute([$pid,$avatar,$u['id']]);
  } else {
    db()->prepare("INSERT INTO users(name,email,provider,provider_id,avatar_url,email_verified) VALUES(?,?,?,?,?,1)")
       ->execute([$name,$email,'facebook',$pid,$avatar]);
    $u = db()->query("SELECT * FROM users WHERE id=".db()->lastInsertId())->fetch();
  }
}
if (!$u){
  $stmt=db()->prepare("SELECT * FROM users WHERE provider='facebook' AND provider_id=? LIMIT 1");
  $stmt->execute([$pid]); $u=$stmt->fetch();
}
login_user($u);
redirect('?p=home');

function http_get_json($url, $params){
  $ch = curl_init($url.'?'.http_build_query($params));
  curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true]);
  $out=json_decode(curl_exec($ch),true);
  curl_close($ch);
  return $out;
}
