<?php

namespace Orm\Core;

class Hashids {
  private const _S_DIV = 3.5;
  private const _G_DIV = 12;

  private string $_salt;
  private int $_minLength;
  private string $_alphabet;
  private string $_separator = 'cfhistuCFHISTU';
  private array $_shuffleds = [];
  private string $_guard;

  public function __construct(int $minLength = 0, string $salt = '', string $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890') {
    $salt = preg_match('/^[0-9a-f]+$/', $salt) ? $salt : md5(serialize($salt));

    $this->_salt = $salt;
    $this->_minLength = $minLength;
    $this->_alphabet = implode('', array_unique($this->_split($alphabet)));

    $alphabets = $this->_split($this->_alphabet);
    $separators = $this->_split($this->_separator);

    $this->_separator = implode('', array_intersect($separators, $alphabets));
    $this->_alphabet = implode('', array_diff($alphabets, $separators));
    $this->_separator = $this->_shuffle($this->_separator, $this->_salt);

    if (!$this->_separator || (strlen($this->_alphabet) / strlen($this->_separator)) > self::_S_DIV) {
      $length = (int) ceil(strlen($this->_alphabet) / self::_S_DIV);

      if ($length > strlen($this->_separator)) {
        $diff = $length - strlen($this->_separator);
        $this->_separator .= substr($this->_alphabet, 0, $diff);
        $this->_alphabet = substr($this->_alphabet, $diff);
      }
    }

    $this->_alphabet = $this->_shuffle($this->_alphabet, $this->_salt);
    $guardCount = (int) ceil(strlen($this->_alphabet) / self::_G_DIV);

    if (strlen($this->_alphabet) < 3) {
      $this->_guard = substr($this->_separator, 0, $guardCount);
      $this->_separator = substr($this->_separator, $guardCount);
    } else {
      $this->_guard = substr($this->_alphabet, 0, $guardCount);
      $this->_alphabet = substr($this->_alphabet, $guardCount);
    }
  }

  public function encode(int ...$numbers): string {
    $return = '';

    if (!$numbers) {
      return $return;
    }

    $alphabet = $this->_alphabet;
    $numbersSize = count($numbers);
    $numbersHashInt = 0;

    foreach ($numbers as $i => $number) {
      $numbersHashInt += intval($number % ($i + 100));
    }

    $lottery = $return = substr($alphabet, $numbersHashInt % strlen($alphabet), 1);
    foreach ($numbers as $i => $number) {
      $alphabet = $this->_shuffle($alphabet, substr($lottery . $this->_salt . $alphabet, 0, strlen($alphabet)));
      $return .= $last = $this->_hash($number, $alphabet);

      if ($i + 1 < $numbersSize) {
        $number %= (ord($last) + $i);
        $sepsIndex = intval($number % strlen($this->_separator));
        $return .= substr($this->_separator, $sepsIndex, 1);
      }
    }

    if (strlen($return) < $this->_minLength) {
      $guardIndex = ($numbersHashInt + ord(substr($return, 0, 1))) % strlen($this->_guard);

      $guard = substr($this->_guard, $guardIndex, 1);
      $return = $guard . $return;

      if (strlen($return) < $this->_minLength) {
        $guardIndex = ($numbersHashInt + ord(substr($return, 2, 1))) % strlen($this->_guard);
        $guard = substr($this->_guard, $guardIndex, 1);

        $return .= $guard;
      }
    }

    $halfLength = (int) (strlen($alphabet) / 2);
    while (strlen($return) < $this->_minLength) {
      $alphabet = $this->_shuffle($alphabet, $alphabet);
      $return = substr($alphabet, $halfLength) . $return . substr($alphabet, 0, $halfLength);

      $excess = strlen($return) - $this->_minLength;
      if ($excess > 0) {
        $return = substr($return, (int) ($excess / 2), $this->_minLength);
      }
    }

    return $return;
  }
  public function decode(string $hash): array {
    $return = [];

    if (!($hash = trim($hash))) {
      return $return;
    }

    $alphabet = $this->_alphabet;

    $hashBreakdown = str_replace($this->_split($this->_guard), ' ', $hash);
    $hashes = explode(' ', $hashBreakdown);

    $i = 3 === count($hashes) || 2 === count($hashes) ? 1 : 0;

    $hashBreakdown = $hashes[$i];

    if ('' !== $hashBreakdown) {
      $lottery = substr($hashBreakdown, 0, 1);
      $hashBreakdown = substr($hashBreakdown, 1);

      $hashBreakdown = str_replace($this->_split($this->_separator), ' ', $hashBreakdown);
      $hashes = explode(' ', $hashBreakdown);

      foreach ($hashes as $subHash) {
        $alphabet = $this->_shuffle($alphabet, substr($lottery . $this->_salt . $alphabet, 0, strlen($alphabet)));
        $result = $this->_unhash($subHash, $alphabet);
        if ($result > PHP_INT_MAX) {
          $return[] = (string)$result;
        } else {
          $return[] = (int)$result;
        }
      }

      if ($this->encode(...$return) !== $hash) {
        $return = [];
      }
    }

    return $return;
  }

  private function _split(string $string): array {
    return preg_split('/(?!^)(?=.)/u', $string) ?: [];
  }
  private function _shuffle(string $alphabet, string $salt): string {
    $key = $alphabet . ' ' . $salt;

    if (isset($this->_shuffleds[$key])) {
      return $this->_shuffleds[$key];
    }

    $length = strlen($salt);
    $salts = $this->_split($salt);

    if (!$length) {
      return $alphabet;
    }
    $alphabets = $this->_split($alphabet);
    for ($i = strlen($alphabet) - 1, $v = 0, $p = 0; $i > 0; $i--, $v++) {
      $v %= $length;
      $p += $int = ord($salts[$v]);
      $j = ($int + $v + $p) % $i;

      $temp = $alphabets[$j];
      $alphabets[$j] = $alphabets[$i];
      $alphabets[$i] = $temp;
    }
    $alphabet = implode('', $alphabets);
    $this->_shuffleds[$key] = $alphabet;

    return $alphabet;
  }
  private function _hash(string $input, string $alphabet): string {
    $hash = '';
    $alphabetLength = strlen($alphabet);

    do {
      $hash = substr($alphabet, intval($input % $alphabetLength), 1) . $hash;
      $input = (int)($input / $alphabetLength);
    } while ($input > 0);

    return $hash;
  }
  private function _unhash(string $input, string $alphabet) {
    $number = 0;
    $inputLength = strlen($input);

    if ($inputLength && $alphabet) {
      $alphabetLength = mb_strlen($alphabet);
      $inputChars = $this->_split($input);

      foreach ($inputChars as $char) {
        $position = mb_strpos($alphabet, $char);
        $number = $number * $alphabetLength;
        $number = $number + $position;
      }
    }

    return $number;
  }
}
