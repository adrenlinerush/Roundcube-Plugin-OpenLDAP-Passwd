<?php

function nt_hash($passwd) {
  return strtoupper(bin2hex(mhash(MHASH_MD4, unicodify($passwd))));
}

function lm_hash($passwd) {
  $magic = pack('H16', '4B47532140232425');
  while (strlen($passwd) < 14) { $passwd .= chr(0); }
  $lm_pw = substr($passwd, 0, 14);
  $lm_pw = strtoupper($lm_pw);
  $key = convert_key(substr($lm_pw, 0, 7)) . 
    convert_key(substr($lm_pw, 7, 7));
  $td = mcrypt_module_open (MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
  mcrypt_generic_init ($td, substr($key, 0, 8) , '12352ff9');
  $enc1 = mcrypt_generic ($td, $magic);
  $td = mcrypt_module_open (MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
  mcrypt_generic_init ($td, substr($key, 8, 8) , '24os139x');
  $enc2 = mcrypt_generic ($td, $magic);
  return strtoupper(bin2hex($enc1 . $enc2));
}

function unicodify($str) {
  $newstr = '';
  for ($i = 0; $i < strlen($str); ++$i) {
    $newstr .= substr($str, $i, 1) . chr(0);
  }
  return $newstr;
}

function convert_key($in_key) {
  $byte = array();
  $result = '';
  $byte[0] = substr($in_key, 0, 1);
  $byte[1] = chr(((ord(substr($in_key, 0, 1)) << 7) & 0xFF) | 
    (ord(substr($in_key, 1, 1)) >> 1));
  $byte[2] = chr(((ord(substr($in_key, 1, 1)) << 6) & 0xFF) | 
    (ord(substr($in_key, 2, 1)) >> 2));
  $byte[3] = chr(((ord(substr($in_key, 2, 1)) << 5) & 0xFF) | 
    (ord(substr($in_key, 3, 1)) >> 3));
  $byte[4] = chr(((ord(substr($in_key, 3, 1)) << 4) & 0xFF) | 
    (ord(substr($in_key, 4, 1)) >> 4));
  $byte[5] = chr(((ord(substr($in_key, 4, 1)) << 3) & 0xFF) | 
    (ord(substr($in_key, 5, 1)) >> 5));
  $byte[6] = chr(((ord(substr($in_key, 5, 1)) << 2) & 0xFF) | 
    (ord(substr($in_key, 6, 1)) >> 6));
  $byte[7] = chr((ord(substr($in_key, 6, 1)) << 1) & 0xFF);
  for ($i = 0; $i < 8; $i++) {
    $byte[$i] = set_odd_parity($byte[$i]);
    $result .= $byte[$i];
  }
  return $result;
}

function set_odd_parity($byte) {
  $parity = 0;
  $ordbyte = '';
  $ordbyte = ord($byte);
  for ($i = 0; $i < 8; $i++) {
    if ($ordbyte & 0x01) {$parity++;}
    $ordbyte >>= 1;
  }
  $ordbyte = ord($byte);
  if ($parity % 2 == 0) {
    if ($ordbyte & 0x01) {
      $ordbyte &= 0xFE;
    } else {
      $ordbyte |= 0x01;
    }
  }
  return chr($ordbyte);
}

?>
