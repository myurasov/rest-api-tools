<?php

/**
 * Security tokens service
 */

namespace MYurasov\RESTAPITools\Security\Tokens;

use Firebase\JWT\JWT;

class TokensService
{
  private $jwtSecret;
  private $jwtAlgo;
  private $tokenLifetime;

  /**
   * Callback for determining if token is revoked
   *
   * function(string $token): bool
   *
   * @var null|callable
   */
  private $isTokenRevokedCallback;

  /**
   * Generate token
   *
   * @param array $data
   * @param null|string $sub Subject
   * @return string
   */
  public function generateToken($data = [], $sub = null)
  {
    if (!is_null($sub)) $data['sub'] = $sub;
    $data['exp'] = time() + $this->tokenLifetime;
    return JWT::encode($data, $this->jwtSecret, $this->jwtAlgo);
  }

  /**
   * Decode token
   *
   * @param string $token
   * @param null|string $sub Subject
   * @throws TokenExpiredException
   * @return mixed
   */
  public function decodeToken($token, $sub = null)
  {
    // check if token is revoked
    if (is_callable($this->isTokenRevokedCallback)) {
      if (call_user_func($this->isTokenRevokedCallback, $token)) {
        throw new TokenValidationException('Token is revoked');
      }
    }

    $data = JWT::decode($token, $this->jwtSecret, array_keys(JWT::$supported_algs));

    if ($data->exp < time()) {
      throw new TokenExpiredException();
    }

    if (!is_null($sub)) {
      if (!isset($data->sub) || $data->sub !== $sub) {
        throw new TokenValidationException('Token subject mismatch');
      }
    }

    return $data;
  }

  // <editor-fold desc="Accessors" defaultstate="collapsed">

  public function getJwtSecret()
  {
    return $this->jwtSecret;
  }

  /**
   * @param string $jwtSecret
   * @return TokensService
   */
  public function setJwtSecret($jwtSecret)
  {
    $this->jwtSecret = $jwtSecret;
    return $this;
  }

  public function getJwtAlgo()
  {
    return $this->jwtAlgo;
  }

  /**
   * @param string $jwtAlgo
   * @return TokensService
   */
  public function setJwtAlgo($jwtAlgo)
  {
    $this->jwtAlgo = $jwtAlgo;
    return $this;
  }

  public function getTokenLifetime()
  {
    return $this->tokenLifetime;
  }

  /**
   * @param int $tokenLifetime
   * @return TokensService
   */
  public function setTokenLifetime($tokenLifetime)
  {
    $this->tokenLifetime = $tokenLifetime;
    return $this;
  }

  public function getIsTokenRevokedCallback()
  {
    return $this->isTokenRevokedCallback;
  }

  /**
   * @param mixed $isTokenRevokedCallback
   * @return TokensService
   */
  public function setIsTokenRevokedCallback($isTokenRevokedCallback)
  {
    $this->isTokenRevokedCallback = $isTokenRevokedCallback;
    return $this;
  }

  // </editor-fold>
}
