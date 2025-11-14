<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['code'])) redirect('?p=login');

$code = $_GET['code'];
// 1) Lấy access token
$resp = fetch('https://oauth2.googleapis.com/token', [
  'code' => $code,
  'client_id' => GOOGLE_CLIENT_ID,
  'client_secret' => GOOGLE_CLIENT_SECRET,
  'redirect_uri' => GOOGLE_REDIRECT_URI,
  'grant_type' => 'authorization_code',
]);

if (empty($resp['access_token'])) redirect('?p=login');

$token = $resp['access_token'];
// 2) Lấy userinfo
$ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
curl_setopt_array($ch, [
  CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"],
  CURLOPT_RETURNTRANSFER => true
]);
$info = json_decode(curl_exec($ch), true);
curl_close($ch);

$email = $info['email'] ?? null;
$name  = $info['name']  ?? ($info['given_name'] ?? 'Google User');
$pid   = $info['id']    ?? null;
$avatar= $info['picture'] ?? null;

if (!$email || !$pid) redirect('?p=login');

// 3) Upsert user
$stmt=db()->prepare("SELECT * FROM users WHERE provider='google' AND provider_id=? LIMIT 1");
$stmt->execute([$pid]); $u=$stmt->fetch();
if (!$u){
  // nếu chưa có theo provider_id, thử tìm theo email
  $stmt=db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->execute([$email]); $u=$stmt->fetch();
  if ($u){
    db()->prepare("UPDATE users SET provider='google', provider_id=?, avatar_url=? WHERE id=?")
       ->execute([$pid,$avatar,$u['id']]);
  } else {
    db()->prepare("INSERT INTO users(name,email,provider,provider_id,avatar_url,email_verified) VALUES(?,?,?,?,?,1)")
       ->execute([$name,$email,'google',$pid,$avatar]);
    $u = db()->query("SELECT * FROM users WHERE id=".db()->lastInsertId())->fetch();
  }
} 
if (!$u){
  $stmt=db()->prepare("SELECT * FROM users WHERE provider='google' AND provider_id=? LIMIT 1");
  $stmt->execute([$pid]); $u=$stmt->fetch();
}
login_user($u);
redirect('?p=studio');

function fetch($url, $data){
  $ch=curl_init($url);
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>http_build_query($data),
    CURLOPT_HTTPHEADER=>['Content-Type: application/x-www-form-urlencoded']
  ]);
  $out=json_decode(curl_exec($ch),true);
  curl_close($ch);
  return $out;
}
