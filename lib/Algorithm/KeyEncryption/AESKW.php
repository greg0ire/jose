<?php

namespace SpomkyLabs\Jose\Algorithm\KeyEncryption;

use Jose\JWKInterface;
use Base64Url\Base64Url;
use Jose\Operation\KeyEncryptionInterface;

/**
 * Class AESKW
 * @package SpomkyLabs\Jose\Algorithm\KeyEncryption
 */
abstract class AESKW implements KeyEncryptionInterface
{
    /**
     * @param  JWKInterface $key
     * @param  string       $cek
     * @param  array        $header
     * @return mixed
     */
    public function encryptKey(JWKInterface $key, $cek, array &$header)
    {
        $this->checkKey($key);
        $wrapper = $this->getWrapper();

        return $wrapper->wrap(Base64Url::decode($key->getValue("k")), $cek);
    }

    /**
     * @param  JWKInterface $key
     * @param  string       $encryted_cek
     * @param  array        $header
     * @return mixed
     */
    public function decryptKey(JWKInterface $key, $encryted_cek, array $header)
    {
        $this->checkKey($key);
        $wrapper = $this->getWrapper();

        return $wrapper->unwrap(Base64Url::decode($key->getValue("k")), $encryted_cek);
    }

    /**
     * @param JWKInterface $key
     */
    protected function checkKey(JWKInterface $key)
    {
        if ("oct" !== $key->getKeyType() || null === $key->getValue("k")) {
            throw new \InvalidArgumentException("The key is not valid");
        }
    }

    /**
     * @return mixed
     */
    abstract protected function getWrapper();
}
