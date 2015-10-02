<?php

/**
 * Serialization trait for documnets
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\ModelTraits;

use Doctrine\Common\Collections\Collection;

trait SerializationTrait
{
  /**
   * Serialize reference
   *
   * @param $document
   * @param $collectionName
   * @return array|null
   */
  protected function serializeReference($document, $collectionName)
  {
    if (is_object($document) && method_exists($document, 'getId')) {

      return [
        '@id' => $document->getId(),
        '@href' => $collectionName . '/' . $document->getId()
      ];

    } else {
      return null;
    }
  }

  /**
   * Serialize array of references
   *
   * @param Collection $collection
   * @param $collectionName
   * @return Collection|null
   */
  protected function serializeReferences(Collection $collection, $collectionName)
  {
    if (!is_null($collection)) {

      return $collection->map(
        function ($document) use ($collectionName) {
          return $this->serializeReference($document, $collectionName);
        }
      );

    } else {
      return null;
    }
  }
} 