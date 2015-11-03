<?php

/**
 * File document
 */

namespace MYurasov\RESTAPITools\Model\ODM\MongoDB;

use Doctrine\MongoDB\GridFSFile;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as Serializer;

use MYurasov\RESTAPITools\ModelTraits\ODM\MongoDB\TimestampTrait;

/**
 * @ODM\MappedSuperclass
 * @Serializer\ExclusionPolicy("all")
 */
abstract class File
{
  use TimestampTrait;

  // temporary file path
  protected $temporaryPath;

  // file is temporary - delete on destroy
  protected $localFileIsTemporary = false;

  /**
   * @ODM\Id
   * @Serializer\Expose
   */
  protected $id;

  /** @ODM\File */
  protected $file;

  /**
   * @ODM\String
   * @Serializer\Expose
   */
  protected $mimeType;

  /**
   * @ODM\Int
   * @Serializer\Expose
   */
  protected $length;

  /**
   * @ODM\String
   * @ODM\Index
   *
   * @Serializer\Expose
   */
  protected $md5;

  /**
   * @ODM\Boolean
   * @Serializer\Expose
   */
  protected $temporary = false;

  /**
   * Create file
   *
   * @param string|GridFSFile $file
   * @throws \RuntimeException
   */
  public function __construct($file = null)
  {
    $this->file = $file;
  }

  protected function downloadRemoteFile()
  {
    // download remote file
    if (is_string($this->file) && preg_match('#^http(s)?://#', $this->file)) {

      // create temporary file
      $this->temporaryPath = tempnam(null, '');

      $content = @file_get_contents($this->file);

      if (false == $content || !file_put_contents($this->temporaryPath, $content)) {

        if (file_exists($this->temporaryPath)) {
          @unlink($this->temporaryPath);
        }

        throw new \RuntimeException('Failed to download remote file');
      }

      $this->localFileIsTemporary = true;
      $this->file = $this->temporaryPath;
    }
  }

  /**
   * Remove temporary file
   */
  public function cleanup()
  {
    if ($this->localFileIsTemporary && !is_null($this->temporaryPath) && file_exists($this->temporaryPath)) {
      @unlink($this->temporaryPath);
      $this->temporaryPath = null;
    }
  }

  public function updateFileInfo()
  {
    $this->downloadRemoteFile();

    if (is_string($this->file)) {

      if (file_exists($this->file)) {

        // length
        if (is_null($this->length)) {
          $this->length = filesize($this->file);
        }

        // mime type
        if (is_null($this->mimeType)) {
          $fi = finfo_open(\FILEINFO_MIME_TYPE);
          $this->mimeType = finfo_file($fi, $this->file);
          finfo_close($fi);
        }

        // md5
        if (is_null($this->md5)) {
          $this->md5 = md5_file($this->file);
        }

      } else {
        throw new \Exception('File does not exist');
      }

    } else if ($this->file instanceof GridFSFile) {

        // mime type
        if (is_null($this->mimeType)) {
          $fi = finfo_open(\FILEINFO_MIME_TYPE);
          $this->mimeType = finfo_buffer($fi, $this->file->getBytes());
          finfo_close($fi);
        }

        // length
        if (is_null($this->length)) {
          $this->length = $this->file->getSize();
        }

        // md5
        if (is_null($this->md5)) {
          $this->md5 = md5($this->file->getBytes());
        }

    }
  }

  private function resetFileInfo()
  {
    $this->length = null;
    $this->md5 = null;
    $this->mimeType = null;
  }

  public function getMimeType()
  {
    if (is_null($this->mimeType)) {
      $this->updateFileInfo();
    }

    return $this->mimeType;
  }

  public function getLength()
  {
    if (is_null($this->length)) {
      $this->updateFileInfo();
    }

    return $this->length;
  }

  public function setFile($file)
  {
    $this->file = $file;
    $this->resetFileInfo();

    return $this;
  }

  public function getMd5()
  {
    if (is_null($this->md5)) {
      $this->updateFileInfo();
    }

    return $this->md5;
  }

  // <editor-fold defaultstate="collapsed" desc="Accessors">

  public function getTemporaryPath()
  {
    return $this->temporaryPath;
  }

  /**
   * @param string $temporaryPath
   * @return $this
   */
  public function setTemporaryPath($temporaryPath)
  {
    $this->temporaryPath = $temporaryPath;
    return $this;
  }

  public function getLocalFileIsTemporary()
  {
    return $this->localFileIsTemporary;
  }

  /**
   * @param boolean $localFileIsTemporary
   * @return $this
   */
  public function setLocalFileIsTemporary($localFileIsTemporary)
  {
    $this->localFileIsTemporary = $localFileIsTemporary;
    return $this;
  }

  public function getId()
  {
    return $this->id;
  }

  /**
   * @param mixed $id
   * @return $this
   */
  public function setId($id)
  {
    $this->id = $id;
    return $this;
  }

  public function getTemporary()
  {
    return $this->temporary;
  }

  /**
   * @param mixed $temporary
   * @return $this
   */
  public function setTemporary($temporary)
  {
    $this->temporary = $temporary;
    return $this;
  }

  /**
   * @param mixed $mimeType
   * @return $this
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
    return $this;
  }

  /**
   * @param mixed $length
   * @return $this
   */
  public function setLength($length)
  {
    $this->length = $length;
    return $this;
  }

  /**
   * @param mixed $md5
   * @return $this
   */
  public function setMd5($md5)
  {
    $this->md5 = $md5;
    return $this;
  }

  public function getFile()
  {
    return $this->file;
  }

  // </editor-fold>
}