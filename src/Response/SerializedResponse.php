<?php

/**
 * Serialized rsponse
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools;

use JMS\Serializer\VisitorInterface;
use Symfony\Component\HttpFoundation\Response;

class SerializedResponse extends Response
{
  private $format = 'json';
  private $cacheDir = false;
  private $jsonOptions = null;
  private $data;
  private $debug = false;

  /**
   * @var VisitorInterface
   */
  private $serializationVisitor;

  private function createSerializer()
  {
    $serializer = new Serializer();
    $serializer->setFormat($this->format);
    $serializer->setJsonOptions($this->jsonOptions);
    $serializer->setSerializationVisitor($this->serializationVisitor);
    $serializer->setDebug($this->debug);

    if ($this->cacheDir) {
      $serializer->setCacheDir($this->cacheDir);
    }

    return $serializer;
  }

  private function update()
  {
    // headers

    if ('json' === $this->format) {

      $this->headers->set('Content-type', 'application/json');

    } else if ('xml' === $this->format) {

      $this->headers->set('Content-type', 'application/xml');

    } else {
      throw new \Exception("Format '$this->format' is not supported");
    }

    // content
    $this->content = $this->createSerializer()->serialize($this->data);
  }

  public function setData($data)
  {
    $this->data = $data;
    $this->update();
  }

  /**
   * Enable JSON options
   *
   * @param int $jsonOptions
   */
  public function enableJsonOptions($jsonOptions)
  {
    $this->setJsonOptions($this->jsonOptions | $jsonOptions);
  }

  /**
   * Enable JSON options
   *
   * @param int $jsonOptions
   */
  public function disableJsonOptions($jsonOptions)
  {
    $this->setJsonOptions($this->jsonOptions ^ $jsonOptions);
  }

  //<editor-fold desc="accessors">

  public function setCacheDir($cacheDir)
  {
    $this->cacheDir = $cacheDir;
    return $this;
  }

  public function getCacheDir()
  {
    return $this->cacheDir;
  }

  public function getJsonOptions()
  {
    return $this->jsonOptions;
  }

  public function setJsonOptions($jsonOptions)
  {
    $this->jsonOptions = $jsonOptions;
    return $this;
  }

  public function getFormat()
  {
    return $this->format;
  }

  public function setFormat($format)
  {
    $this->format = $format;
    return $this;
  }

  public function getData()
  {
    return $this->data;
  }

  public function setSerializationVisitor($serializationVisitor)
  {
    $this->serializationVisitor = $serializationVisitor;
    return $this;
  }

  public function getSerializationVisitor()
  {
    return $this->serializationVisitor;
  }

  public function setDebug($debug)
  {
    $this->debug = $debug;
    return $this;
  }

  public function getDebug()
  {
    return $this->debug;
  }

  //</editor-fold>
}