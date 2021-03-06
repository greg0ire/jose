<?php

namespace SpomkyLabs\Jose\Tests;

use SpomkyLabs\Jose\JWK;
use SpomkyLabs\Jose\Algorithm\KeyEncryption\ECDHESA128KW;
use SpomkyLabs\Jose\Algorithm\KeyEncryption\ECDHESA192KW;
use SpomkyLabs\Jose\Algorithm\KeyEncryption\ECDHESA256KW;

/**
 * Class ECDHESKeyAgreementTest
 * @package SpomkyLabs\Jose\Tests
 */
class ECDHESKeyAgreementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-39#appendix-C
     */
    public function testGetAgreementKey()
    {
        $receiver = new JWK();
        $receiver->setValue('kty', 'EC')
                 ->setValue('crv', 'P-256')
                 ->setValue('x', "weNJy2HscCSM6AEDTDg04biOvhFhyyWvOHQfeF_PxMQ")
                 ->setValue('y', "e8lnCO-AlStT-NJVX-crhB7QRYhiix03illJOVAOyck")
                 ->setValue('d', "VEmDZpDXXK8p8N0Cndsxs924q6nS1RXFASRl6BfUqdw");

        $sender = new JWK();
        $sender->setValue("kty", "EC")
               ->setValue("crv", "P-256")
               ->setValue("x", "gI0GAILBdu7T53akrFmMyGcsF3n5dO7MmwNBHKW5SV0")
               ->setValue("y", "SLW_xSffzlPWrHEVI30DHM_4egVwt3NQqeUD7nMFpps")
               ->setValue("d", "0_NxaRPUMQoAJt50Gz8YiTr8gRTwyEaCumd-MToTmIo");

        $this->markTestIncomplete("This test passed when 'apu' and 'apv' parameters are forced. They are not yet supported by this implementation and this test is marked as incomplete. This mark will be removed when these parameter will be supported.");

        //$header = array("enc" => "A128GCM");
        //$expected = Base64Url::decode("VqqN6vgjbSBcIijNcacQGg");
        //$ecdh_es = new ECDH_ES();
        //$this->assertEquals($expected, $ecdh_es->getAgreementKey($receiver, 128, $header));
    }

    /**
     *
     */
    public function testGetAgreementKeyWithA128KeyWrap()
    {
        $header = array("enc" => "A128GCM");
        $receiver = new JWK();
        $receiver->setValue('kty', 'EC')
                 ->setValue('crv', 'P-256')
                 ->setValue('x', "weNJy2HscCSM6AEDTDg04biOvhFhyyWvOHQfeF_PxMQ")
                 ->setValue('y', "e8lnCO-AlStT-NJVX-crhB7QRYhiix03illJOVAOyck")
                 ->setValue('d', "VEmDZpDXXK8p8N0Cndsxs924q6nS1RXFASRl6BfUqdw");

        $sender = new JWK();
        $sender->setValue("kty", "EC")
               ->setValue("crv", "P-256")
               ->setValue("x", "gI0GAILBdu7T53akrFmMyGcsF3n5dO7MmwNBHKW5SV0")
               ->setValue("y", "SLW_xSffzlPWrHEVI30DHM_4egVwt3NQqeUD7nMFpps")
               ->setValue("d", "0_NxaRPUMQoAJt50Gz8YiTr8gRTwyEaCumd-MToTmIo");

        $cek = [4, 211, 31, 197, 84, 157, 252, 254, 11, 100, 157, 250, 63, 170, 106, 206, 107, 124, 212, 45, 111, 107, 9, 219, 200, 177, 0, 240, 143, 156, 44, 207];
        foreach ($cek as $key => $value) {
            $cek[$key] = str_pad(dechex($value), 2, "0", STR_PAD_LEFT);
        }
        $cek = hex2bin(implode("", $cek));

        $ecdh_es = new ECDHESA128KW();
        $encrypted_cek = $ecdh_es->wrapAgreementKey($sender, $receiver, $cek, 128, $header);

        $this->assertEquals($cek, $ecdh_es->unwrapAgreementKey($receiver, $encrypted_cek, 128, $header));
    }

    /**
     *
     */
    public function testGetAgreementKeyWithA192KeyWrap()
    {
        $header = array("enc" => "A192GCM");
        $receiver = new JWK();
        $receiver->setValue('kty', 'EC')
                 ->setValue('crv', 'P-256')
                 ->setValue('x', "weNJy2HscCSM6AEDTDg04biOvhFhyyWvOHQfeF_PxMQ")
                 ->setValue('y', "e8lnCO-AlStT-NJVX-crhB7QRYhiix03illJOVAOyck")
                 ->setValue('d', "VEmDZpDXXK8p8N0Cndsxs924q6nS1RXFASRl6BfUqdw");

        $sender = new JWK();
        $sender->setValue("kty", "EC")
               ->setValue("crv", "P-256")
               ->setValue("x", "gI0GAILBdu7T53akrFmMyGcsF3n5dO7MmwNBHKW5SV0")
               ->setValue("y", "SLW_xSffzlPWrHEVI30DHM_4egVwt3NQqeUD7nMFpps")
               ->setValue("d", "0_NxaRPUMQoAJt50Gz8YiTr8gRTwyEaCumd-MToTmIo");

        $cek = [4, 211, 31, 197, 84, 157, 252, 254, 11, 100, 157, 250, 63, 170, 106, 206, 107, 124, 212, 45, 111, 107, 9, 219, 200, 177, 0, 240, 143, 156, 44, 207];
        foreach ($cek as $key => $value) {
            $cek[$key] = str_pad(dechex($value), 2, "0", STR_PAD_LEFT);
        }
        $cek = hex2bin(implode("", $cek));

        $ecdh_es = new ECDHESA192KW();
        $encrypted_cek = $ecdh_es->wrapAgreementKey($sender, $receiver, $cek, 192, $header);

        $this->assertEquals($cek, $ecdh_es->unwrapAgreementKey($receiver, $encrypted_cek, 192, $header));
    }

    /**
     *
     */
    public function testGetAgreementKeyWithA256KeyWrap()
    {
        $header = array("enc" => "A256GCM");
        $receiver = new JWK();
        $receiver->setValue('kty', 'EC')
                 ->setValue('crv', 'P-256')
                 ->setValue('x', "weNJy2HscCSM6AEDTDg04biOvhFhyyWvOHQfeF_PxMQ")
                 ->setValue('y', "e8lnCO-AlStT-NJVX-crhB7QRYhiix03illJOVAOyck")
                 ->setValue('d', "VEmDZpDXXK8p8N0Cndsxs924q6nS1RXFASRl6BfUqdw");

        $sender = new JWK();
        $sender->setValue("kty", "EC")
               ->setValue("crv", "P-256")
               ->setValue("x", "gI0GAILBdu7T53akrFmMyGcsF3n5dO7MmwNBHKW5SV0")
               ->setValue("y", "SLW_xSffzlPWrHEVI30DHM_4egVwt3NQqeUD7nMFpps")
               ->setValue("d", "0_NxaRPUMQoAJt50Gz8YiTr8gRTwyEaCumd-MToTmIo");

        $cek = [4, 211, 31, 197, 84, 157, 252, 254, 11, 100, 157, 250, 63, 170, 106, 206, 107, 124, 212, 45, 111, 107, 9, 219, 200, 177, 0, 240, 143, 156, 44, 207];
        foreach ($cek as $key => $value) {
            $cek[$key] = str_pad(dechex($value), 2, "0", STR_PAD_LEFT);
        }
        $cek = hex2bin(implode("", $cek));

        $ecdh_es = new ECDHESA256KW();
        $encrypted_cek = $ecdh_es->wrapAgreementKey($sender, $receiver, $cek, 256, $header);

        $this->assertEquals($cek, $ecdh_es->unwrapAgreementKey($receiver, $encrypted_cek, 256, $header));
    }
}
