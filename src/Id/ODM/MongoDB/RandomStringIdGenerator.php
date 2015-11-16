<?php

/**
 * Random string Id generator
 */

namespace MYurasov\RESTAPITools\Id\ODM\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Id\AbstractIdGenerator;

class RandomStringIdGenerator extends AbstractIdGenerator
{
  const ALPHABET_BIN = '01';
  const ALPHABET_OC = '01234567';
  const ALPHABET_HEX = '01234567890abcdef';
  const ALPHABET_ALNUM = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
  const ALPHABET_ALNUM_LOWCASE = '0123456789abcdefghijklmnopqrstuvwxyz';
  const ALPHABET_DIGITS = '0123456789';

  private $bitStrength = 128;
  private $alphabet = self::ALPHABET_ALNUM;

  /**
   * Generates an identifier for a document.
   *
   * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
   * @param object                                $document
   * @return string
   */
  public function generate(DocumentManager $dm, $document)
  {
    return $this->createRandomString(null, $this->alphabet, $this->bitStrength);
  }

  public function setBitStrength($bitStrength)
  {
    $this->bitStrength = $bitStrength;
  }

  public function setAlphabet($alphabet)
  {
    $this->alphabet = $alphabet;
  }

  /**
   * Create string from random characters
   *
   * @param int    $length
   * @param string $alphabet
   * @param int    $bitStrength If $bitStrength is passed, $length is computed based on it
   * @return string
   */
  private function createRandomString($length, $alphabet, $bitStrength = null)
  {
    // bit_strength = log2(alphabet_length) * num_chars
    // num_chars = bit_strength_required / log2(alphabet_lenght)
    $base = strlen($alphabet);

    if (!is_null($bitStrength)) {
      $length = (int) ceil($bitStrength / log($base, 2));
    }

    $result = '';

    for ($i = 0; $i < $length; $i++) {
      $order = mt_rand(0, $base - 1);
      $result = substr($alphabet, $order, 1) . $result;
    }

    return $result;
  }
}