<?php

namespace SpomkyLabs\Jose\Tests;

use Base64Url\Base64Url;
use SpomkyLabs\Jose\JWK;
use SpomkyLabs\Jose\JWKSet;
use SpomkyLabs\Jose\EncryptionInstruction;
use Jose\JSONSerializationModes;

/**
 * Class EncrypterTest
 * @package SpomkyLabs\Jose\Tests
 */
class EncrypterTest extends TestCase
{
    /**
     *
     */
    public function testEncryptAndLoadFlattenedWithDeflateCompression()
    {
        $encrypter = $this->getEncrypter();
        $loader = $this->getLoader();

        $instruction = new EncryptionInstruction();
        $instruction->setRecipientPublicKey($this->getRSARecipientKey());

        $encrypted = $encrypter->encrypt($this->getKeyToEncrypt(), array($instruction), array("kid" => "123456789", "enc" => "A128GCM", "alg" => "RSA-OAEP-256", "zip" => "DEF"), array(), JSONSerializationModes::JSON_FLATTENED_SERIALIZATION);

        $loaded = $loader->load($encrypted);

        $this->assertInstanceOf("Jose\JWEInterface", $loaded);
        $this->assertInstanceOf("Jose\JWKInterface", $loaded->getPayload());
        $this->assertEquals("RSA-OAEP-256", $loaded->getAlgorithm());
        $this->assertEquals("A128GCM", $loaded->getEncryptionAlgorithm());
        $this->assertEquals("DEF", $loaded->getZip());
        $this->assertEquals($this->getKeyToEncrypt(), $loaded->getPayload());
    }

    /**
     *
     */
    public function testEncryptAndLoadCompactWithDirectKeyEncryption()
    {
        $encrypter = $this->getEncrypter();
        $loader = $this->getLoader();

        $instruction = new EncryptionInstruction();
        $instruction->setRecipientPublicKey($this->getDirectKey());

        $encrypted = $encrypter->encrypt($this->getKeySetToEncrypt(), array($instruction), array("kid" => "DIR_1", "enc" => "A256GCM", "alg" => "dir"), array());

        $loaded = $loader->load($encrypted);

        $this->assertInstanceOf("Jose\JWEInterface", $loaded);
        $this->assertInstanceOf("Jose\JWKSetInterface", $loaded->getPayload());
        $this->assertEquals("dir", $loaded->getAlgorithm());
        $this->assertEquals("A256GCM", $loaded->getEncryptionAlgorithm());
        $this->assertNull($loaded->getZip());
        $this->assertEquals($this->getKeySetToEncrypt(), $loaded->getPayload());
    }

    /**
     *
     */
    public function testEncryptAndLoadCompactKeyAgreement()
    {
        $encrypter = $this->getEncrypter();
        $loader = $this->getLoader();

        $instruction = new EncryptionInstruction();
        $instruction->setRecipientPublicKey($this->getECDHRecipientPublicKey())
                    ->setSenderPrivateKey($this->getECDHSenderPrivateKey());

        $encrypted = $encrypter->encrypt(array("user_id" => "1234", "exp" => 3600), array($instruction), array("kid" => "e9bc097a-ce51-4036-9562-d2ade882db0d", "enc" => "A256GCM", "alg" => "ECDH-ES"), array());

        $loaded = $loader->load($encrypted);

        $this->assertInstanceOf("Jose\JWEInterface", $loaded);
        $this->assertTrue(is_array($loaded->getPayload()));
        $this->assertEquals("ECDH-ES", $loaded->getAlgorithm());
        $this->assertEquals("A256GCM", $loaded->getEncryptionAlgorithm());
        $this->assertNull($loaded->getZip());
        $this->assertEquals(array("user_id" => "1234", "exp" => 3600), $loaded->getPayload());
    }

    /**
     *
     */
    public function testEncryptAndLoadCompactKeyAgreementWithWrapping()
    {
        $encrypter = $this->getEncrypter();
        $loader = $this->getLoader();

        $instruction = new EncryptionInstruction();
        $instruction->setRecipientPublicKey($this->getECDHRecipientPublicKey())
                    ->setSenderPrivateKey($this->getECDHSenderPrivateKey());

        $encrypted = $encrypter->encrypt("Je suis Charlie", array($instruction), array("kid" => "e9bc097a-ce51-4036-9562-d2ade882db0d", "enc" => "A256GCM", "alg" => "ECDH-ES+A256KW"), array());

        $loaded = $loader->load($encrypted);

        $this->assertInstanceOf("Jose\JWEInterface", $loaded);
        $this->assertTrue(is_string($loaded->getPayload()));
        $this->assertEquals("ECDH-ES+A256KW", $loaded->getAlgorithm());
        $this->assertEquals("A256GCM", $loaded->getEncryptionAlgorithm());
        $this->assertNull($loaded->getZip());
        $this->assertEquals("Je suis Charlie", $loaded->getPayload());
    }

    /**
     * @return JWK
     */
    protected function getKeyToEncrypt()
    {
        $key = new JWK();
        $key->setValues(array(
            "kty" => "EC",
            "crv" => "P-256",
            "x" => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
            "y" => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
            "d" => "jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI",
        ));

        return $key;
    }

    /**
     * @return JWKSet
     */
    protected function getKeySetToEncrypt()
    {
        $key = new JWK();
        $key->setValues(array(
            "kty" => "EC",
            "crv" => "P-256",
            "x" => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
            "y" => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
            "d" => "jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI",
        ));

        $key_set = new JWKSet();
        $key_set->addKey($key);

        return $key_set;
    }

    /**
     * @return JWK
     */
    protected function getRSARecipientKey()
    {
        $key = new JWK();
        $key->setValues(array(
            "kty" => "RSA",
            'n' => 'tpS1ZmfVKVP5KofIhMBP0tSWc4qlh6fm2lrZSkuKxUjEaWjzZSzs72gEIGxraWusMdoRuV54xsWRyf5KeZT0S-I5Prle3Idi3gICiO4NwvMk6JwSBcJWwmSLFEKyUSnB2CtfiGc0_5rQCpcEt_Dn5iM-BNn7fqpoLIbks8rXKUIj8-qMVqkTXsEKeKinE23t1ykMldsNaaOH-hvGti5Jt2DMnH1JjoXdDXfxvSP_0gjUYb0ektudYFXoA6wekmQyJeImvgx4Myz1I4iHtkY_Cp7J4Mn1ejZ6HNmyvoTE_4OuY1uCeYv4UyXFc1s1uUyYtj4z57qsHGsS4dQ3A2MJsw',
            'e' => 'AQAB',
        ));

        return $key;
    }

    /**
     * @return JWK
     */
    protected function getECDHRecipientPublicKey()
    {
        $key = new JWK();
        $key->setValues(array(
            "kty" => "EC",
            "crv" => "P-256",
            "x"   => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
            "y"   => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
        ));

        return $key;
    }

    /**
     * @return JWK
     */
    protected function getECDHSenderPrivateKey()
    {
        $key = new JWK();
        $key->setValues(array(
            "kty" => "EC",
            "crv" => "P-256",
            "x"   => "gI0GAILBdu7T53akrFmMyGcsF3n5dO7MmwNBHKW5SV0",
            "y"   => "SLW_xSffzlPWrHEVI30DHM_4egVwt3NQqeUD7nMFpps",
            "d"   => "0_NxaRPUMQoAJt50Gz8YiTr8gRTwyEaCumd-MToTmIo",
        ));

        return $key;
    }

    /**
     * @return JWK
     */
    protected function getDirectKey()
    {
        $key = new JWK();
        $key->setValues(array(
            "kid" => "DIR_1",
            "kty" => "dir",
            'dir' => Base64Url::encode(hex2bin("00112233445566778899AABBCCDDEEFF000102030405060708090A0B0C0D0E0F")),
        ));

        return $key;
    }
}
