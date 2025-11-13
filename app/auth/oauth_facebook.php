<?php
require_once __DIR__ . '/../config.php';
$params = [
  'client_id'    => FB_APP_ID,
  'redirect_uri' => FB_REDIRECT_URI,
  'response_type'=> 'code',
  'scope'        => 'public_profile,email',
  'auth_type'    => 'rerequest'
];
$url = 'https://www.facebook.com/v19.0/dialog/oauth?'.http_build_query($params);
redirect($url);
