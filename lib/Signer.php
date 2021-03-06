<?php

namespace SpomkyLabs\Jose;

use Base64Url\Base64Url;
use Jose\JWKInterface;
use Jose\JWTInterface;
use Jose\JWKSetInterface;
use Jose\SignerInterface;
use Jose\JSONSerializationModes;
use Jose\Operation\SignatureInterface;
use Jose\SignatureInstructionInterface;

/**
 * Class representing a JSON Web Token Manager.
 */
abstract class Signer implements SignerInterface
{
    use PayloadConverter;

    /**
     * @return \Jose\JWAManagerInterface
     */
    abstract protected function getJWAManager();

    /**
     * @param  array|JWKInterface|JWKSetInterface|JWTInterface|string $input
     * @param  array                                                  $instructions
     * @param  string                                                 $serialization
     * @return array|mixed|string
     */
    public function sign($input, array $instructions, $serialization = JSONSerializationModes::JSON_COMPACT_SERIALIZATION)
    {
        $this->checkInput($input);
        if (empty($instructions)) {
            throw new \InvalidArgumentException("No instruction.");
        }

        $jwt_payload = Base64Url::encode($input->getPayload());

        $signatures = array();
        foreach ($instructions as $instruction) {
            if (!$instruction instanceof SignatureInstructionInterface) {
                throw new \InvalidArgumentException("Bad instruction. Must implement SignatureInstructionInterface.");
            }
            $protected_header   = array_merge($input->getProtectedHeader(), $instruction->getProtectedHeader());
            $unprotected_header = array_merge($input->getUnprotectedHeader(), $instruction->getUnprotectedHeader());
            $complete_header = array_merge($protected_header, $protected_header);

            $jwt_protected_header   = Base64Url::encode(json_encode($protected_header));
            $alg = array_key_exists("alg", $complete_header) ? $complete_header["alg"] : null;

            if (null === $alg) {
                throw new \InvalidArgumentException("No 'alg' parameter set in the header or the key.");
            }

            $algorithm = $this->getJWAManager()->getAlgorithm($alg);
            if (null === $algorithm || !$algorithm instanceof SignatureInterface) {
                throw new \InvalidArgumentException("The algorithm '$alg' is not supported.");
            }

            $signature = $algorithm->sign($instruction->getKey(), $jwt_protected_header.".".$jwt_payload);
            $jwt_signature = Base64Url::encode($signature);
            switch ($serialization) {
                case JSONSerializationModes::JSON_COMPACT_SERIALIZATION:
                    $signatures[] = $jwt_protected_header.".".$jwt_payload.".".$jwt_signature;
                    break;
                case JSONSerializationModes::JSON_FLATTENED_SERIALIZATION:
                    $result = array(
                        "payload" => $jwt_payload,
                        "protected" => $jwt_protected_header,
                        "signature" => $jwt_signature,
                    );
                    if (!empty($unprotected_header)) {
                        $result["header"] = $unprotected_header;
                    }
                    $signatures[] = json_encode($result);
                    break;
                case JSONSerializationModes::JSON_SERIALIZATION:
                    $result = array(
                        "protected" => $jwt_protected_header,
                        "signature" => $jwt_signature,
                    );
                    if (!empty($unprotected_header)) {
                        $result["header"] = $unprotected_header;
                    }
                    $signatures['signatures'][] = $result;
                    break;
                default:
                    throw new \InvalidArgumentException("The serialization method '$serialization' is not supported.");
            }
        }

        if (JSONSerializationModes::JSON_SERIALIZATION === $serialization) {
            $signatures['payload'] = $jwt_payload;

            return json_encode($signatures);
        }

        return count($signatures) === 1 ? current($signatures) : $signatures;
    }
}
