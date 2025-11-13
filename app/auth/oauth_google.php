<?php
require_once __DIR__ . '/../config.php';
$params = [
  'client_id'     => GOOGLE_CLIENT_ID,
  'redirect_uri'  => GOOGLE_REDIRECT_URI,
  'response_type' => 'code',
  'scope'         => 'openid email profile',
  'access_type'   => 'online',
  'include_granted_scopes' => 'true',
  'prompt'        => 'select_account'
];
$url = 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query($params);
redirect($url);
