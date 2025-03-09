<?php

namespace Orm\Core;

final class Config {
  public static function create(array $options = []): self { // php8 -> return static
    return new static($options);
  }

  private string $_hostname = '';
  private string $_username = '';
  private string $_password = '';
  private string $_database = '';
  private string $_encoding = 'utf8mb4';

  private function __construct(array $options = []) {
    $sample = array_flip(['hostname', 'username', 'password', 'database', 'encoding']);
    $options = array_intersect_key($options, $sample);

    foreach ($options as $key => $val) {
      $method = 'set' . ucfirst($key);
      if (method_exists($this, $method)) {
        $this->$method($val);
      }
    }
  }

  public function setHostname(string $hostname): self {
    $this->_hostname = $hostname;
    return $this;
  }
  public function getHostname(): string {
    return $this->_hostname;
  }
  public function setUsername(string $username): self {
    $this->_username = $username;
    return $this;
  }
  public function getUsername(): string {
    return $this->_username;
  }
  public function setPassword(string $password): self {
    $this->_password = $password;
    return $this;
  }
  public function getPassword(): string {
    return $this->_password;
  }
  public function setDatabase(string $database): self {
    $this->_database = $database;
    return $this;
  }
  public function getDatabase(): string {
    return $this->_database;
  }
  public function setEncoding(string $encoding): self {
    $this->_encoding = $encoding;
    return $this;
  }
  public function getEncoding(): string {
    return $this->_encoding;
  }
}
