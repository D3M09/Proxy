<?php
/**
 * Proxy configuration. Generated/updated by the admin panel / setup installer.
 */

return array (
  'upstream' => 'https://www.1333bk.com',
  'brand_from' => '1333bet',
  'brand_to' => 'LottoGames',
  // Seconds to keep proxied static assets on local disk. 0 disabled the cache
  // entirely, so every /res/* request made a fresh upstream round trip. Assets
  // are cached lazily on first request, so no page view waits on a burst.
  'cache_ttl' => 3600,
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
  'referral_code' => 'ggp0537',
  'referral_affiliate_code' => '',
  'reg_mobile_pattern' => '^01[3-9]\\d{8}$',
  'reg_username_pattern' => '^[A-Za-z][A-Za-z0-9]*$',
  'disable_affiliate_redirect' => true,
  'admin' => 
  array (
    'key' => '5fd39a22b45ee03373d70a615737e2bb',
    'user' => 'admin',
    'pass_hash' => '$2y$10$zuEGwzAo61bfO8ceTwpbS.9DLtxkuDRg45fX739vhy5bP5a9Jixmu',
    'cookie' => 'px_sid',
  ),
);
