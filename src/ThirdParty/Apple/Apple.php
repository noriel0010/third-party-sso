<?php declare(strict_types=1);

namespace Noriel\SSO\ThirdParty\Apple;

use GuzzleHttp;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger;

use BadMethodCallException;
use Noriel\SSO\Auth\Exception\InvalidTokenException;
use Noriel\SSO\ThirdParty\Exception\AppleException;

class Apple
{
    /**
     * @var Array
     */
    private $authKeys = [];

    /**
     * @var RSA
     */
    private $rsa;

    /**
     * @var String
     */
    private $token;

    /**
     * @var String
     */
    private $base_uri;

    /**
     * @var String
     */
    private $aud;

    /**
     * @var String
     */
    private $issuer;

    /**
     * @var String
     * this will be used as the base_uri by default
     */
    private $DEFAULT_APPLE_BASE_URI = 'https://appleid.apple.com';

    /**
     * @var String
     * this will be used as the issuer by default
     */
    private $DEFAULT_APPLE_ISSUER = 'https://appleid.apple.com';

    public function __construct(String $token, ?string $aud=null, ?string $issuer=null, ?string $base_uri=null){
        $this->token = $token;
        $this->aud = $aud;
        $this->issuer = $issuer;
        $this->base_uri = $base_uri;
        $this->rsa = new RSA();
    }

    /**
     * @throws Exception\InvalidTokenException
    */
    function authenticate() : object {

        if(!isset($this->token) or empty($this->token) or !is_string($this->token)){
            throw new InvalidTokenException(
                sprintf('Invalid Token passed, `%s` received', $this->token)
            );
        }

        if (null === $this->base_uri){
            $this->base_uri = $this->DEFAULT_APPLE_BASE_URI;
        }

        if (null === $this->issuer){
            $this->issuer = $this->DEFAULT_APPLE_ISSUER;
        }

        $appleKeys = $this->getAppleKeys();
        
        $parsedJwt = null;
        try {
            $parsedJwt = (new Parser())->parse($this->token);
        } catch (\Throwable $th) {
            throw new InvalidTokenException(
                sprintf('Invalid Token passed, `%s` received', $this->token)
            );
        }

        if (!$this->verify($parsedJwt, $appleKeys)) {
            throw new InvalidTokenException(
                sprintf(
                    'Verification of given `%s` token failed. '
                    . 'Possibly incorrect public key used or token is malformed.',
                    $this->token
                )
            );
        }

        if (!$this->isValid($parsedJwt)) {
            throw new InvalidTokenException('Validation of given token failed. Possibly token expired.');
        }

        $claims = $parsedJwt->getClaims();
        $obj_claims = new \stdClass();
        foreach($claims as $key=>$value){
            $obj_claims->{$key} = $parsedJwt->getClaim($key);
        }
        return $obj_claims;
    }

    /**
     * @throws Exception\AppleException
    */
    private function getAppleKeys() : Array{
        $guzzle = new GuzzleHttp\Client(
            [
                'base_uri'        => $this->base_uri,
                'timeout'         => 5,
                'connect_timeout' => 5,
                'headers' => ['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1']
            ]
        );

        try {
            $response = $guzzle->send(new GuzzleHttp\Psr7\Request('GET', 'auth/keys'));
            $responseBody = GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleHttp\Exception\GuzzleException $exception) {
            throw new AppleException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $appleKeys=[];

        foreach ($responseBody['keys'] as $authKey) {
            $appleKeys[$authKey['kid']] = [
                'kty'=>$authKey['kty'],
                'kid'=>$authKey['kid'],
                'use'=>$authKey['use'],
                'alg'=>$authKey['alg'],
                'n'=>$authKey['n'],
                'e'=>$authKey['e']
            ];
        }
        return $appleKeys;
    }

    private function isValid(Token $jwt): bool {
        $validationData = new ValidationData();
        $validationData->setIssuer($this->issuer);
        if(null !== $this->aud && !empty($this->aud)){
            $validationData->setAudience($this->aud);
        }
        return $jwt->validate($validationData) && !$jwt->isExpired();
    }

    /**
     * @throws Exception\AppleException
    */
    private function verify(Token $jwt, $appleKeys): bool {
        $kid = $jwt->getHeader('kid');
        $this->loadRsaKey($appleKeys[$kid]);

        try {
            return $jwt->verify(new Sha256(), $this->rsa->getPublicKey());
        } catch (BadMethodCallException $exception) {
            throw  new AppleException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function loadRsaKey($authKeys): void
    {
        /**
         * Phpspeclib is parsing phpinfo(); output to determine OpenSSL Library and Header versions,
         * basing on that set if MATH_BIGINTEGER_OPENSSL_ENABLED or MATH_BIGINTEGER_OPENSSL_DISABLED const.
         * It crashes tests so it is possible that it might crash production, that is why constants are overwritten.
         *
         * @see vendor/phpseclib/phpseclib/phpseclib/Math/BigInteger.php:273
         */
        if (!defined('MATH_BIGINTEGER_OPENSSL_ENABLED')) {
            define('MATH_BIGINTEGER_OPENSSL_ENABLED', true);
        }

        $this->rsa->loadKey(
            [
                'exponent' => new BigInteger(base64_decode(strtr($authKeys['e'], '-_', '+/')), 256),
                'modulus'  => new BigInteger(base64_decode(strtr($authKeys['n'], '-_', '+/')), 256),
            ]
        );
    }
}
