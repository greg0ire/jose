<?php

namespace SpomkyLabs\Jose\Algorithm\KeyEncryption;

/**
 * Class A192GCMKW
 * @package SpomkyLabs\Jose\Algorithm\KeyEncryption
 */
class A192GCMKW extends AESGCMKW
{
    /**
     * @return int
     */
    protected function getKeySize()
    {
        return 192;
    }

    /**
     * @return string
     */
    public function getAlgorithmName()
    {
        return "A192GCMKW";
    }
}
