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
 * @ODM\Document(collection="files")
 * @ODM\MappedSuperclass
 * @ODM\HasLifecycleCallbacks
 *
 * @Serializer\ExclusionPolicy("all")
 */
class File
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
    // download remote file
    if (is_string($file) && preg_match('#^http(s)?://#', $file)) {

      // create temporary file
      $this->temporaryPath = tempnam(null, '');

      $content = @file_get_contents($file);

      if (false == $content || !file_put_contents($this->temporaryPath, $content)) {

        if (file_exists($this->temporaryPath)) {
          @unlink($this->temporaryPath);
        }

        throw new \RuntimeException('Failed to download remote file');
      }

      $this->localFileIsTemporary = true;
      $file = $this->temporaryPath;
    }

    $this->file = $file;
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

  /**
   * @ODM\PreFlush
   */
  public function onPreFlush()
  {
    $this->updateFileInfo();
  }

  /**
   * Update file information
   */
  protected function updateFileInfo()
  {
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

    } else {

      if ($this->file instanceof GridFSFile) {

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
   * @return File
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
   * @return File
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
   * @return File
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
   * @return File
   */
  public function setTemporary($temporary)
  {
    $this->temporary = $temporary;
    return $this;
  }

  // </editor-fold>
}