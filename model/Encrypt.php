<?php

namespace model;

class Encrypt
{
  public static function encrypt($var, $httpQuery = true)
  {
    $secret_key = "cda4a3b9-b921-4824-87a0-689c3a9609e9";
    $method = "AES-256-CBC";
    // $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $iv = openssl_random_pseudo_bytes(16);
    $param = openssl_encrypt($var, $method, $secret_key, 0, $iv);
    $ivBase64 = base64_encode($iv);
    $parametrosGET = http_build_query(['token' => $param, 'iv' => $ivBase64]);

    if ($httpQuery) {
      return $parametrosGET;
    }

    if (!$httpQuery) {
      return ['token' => $param, 'iv' => $ivBase64];
    }
  }

  public static function decrypParams($data)
  {
    $secret_key = "cda4a3b9-b921-4824-87a0-689c3a9609e9";
    $method = "AES-256-CBC";
    $iv = base64_decode($data['iv']);
    return openssl_decrypt($data['token'], $method, $secret_key, 0, $iv);
  }

  public static function secure_encrypt($data)
  {
    $first_key = base64_decode('Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');
    $second_key = base64_decode('EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');

    $method = "aes-256-cbc";
    $iv_length = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($iv_length);

    $first_encrypted = openssl_encrypt($data, $method, $first_key, OPENSSL_RAW_DATA, $iv);
    $second_encrypted = hash_hmac('sha512', $first_encrypted, $second_key, TRUE);

    $output = base64_encode($iv . $second_encrypted . $first_encrypted);
    $encryptedData = strtr($output, ['+' => '-', '/' => '_']);
    return $encryptedData;
  }
}
