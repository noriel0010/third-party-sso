<?php declare(strict_types=1);

namespace Noriel\SSO\ThirdParty\Microsoft;

use GuzzleHttp;

use Noriel\SSO\Auth\Exception\InvalidTokenException;
use Noriel\SSO\ThirdParty\Exception\MicrosoftException;

class Microsoft
{

    /**
     * @var String
     * this will be used as the base_uri by default
     */
    private $token;

    /**
     * @var String
     */
    private $base_uri;

    /**
     * @var String
     * this will be used as the base_uri by default
     */
    private $DEFAULT_MICROSOFT_BASE_URI = 'https://graph.microsoft.com/v1.0/';

    public function __construct(String $token, ?string $base_uri=null){
        $this->token = $token;
        $this->base_uri = $base_uri;
    }

    /**
     * @throws Exception\InvalidTokenException
     * @throws Exception\MicrosoftException
    */
    function authenticate() : object{

        if(!isset($this->token) or empty($this->token) or !is_string($this->token)){
            throw new InvalidTokenException(
                sprintf('Invalid Token passed, `%s` received', $this->token)
            );
        }

        if (null === $this->base_uri){
            $this->base_uri = $this->DEFAULT_MICROSOFT_BASE_URI;
        }

        $guzzle = new GuzzleHttp\Client(
            [
                'base_uri'        => $this->base_uri,
                'timeout'         => 5,
                'connect_timeout' => 5,
                'headers' => ['Authorization' => 'Bearer ' . $this->token, 'Content-Type'=>'application/json']
            ]
        );
        try {
            $response = $guzzle->request('GET', 'me');
            return (object) GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleHttp\Exception\RequestException $exception) {
            if ($exception->hasResponse()) {
                $ex = (object) GuzzleHttp\json_decode($exception->getResponse()->getBody()->getContents(), true);
                throw new InvalidTokenException($ex->error['code'] . ' ' .$ex->error['message']);
            }
            throw new MicrosoftException($exception->getMessage(), $exception->getCode(), $exception);
        }catch (\Throwable $exception) {
            throw new MicrosoftException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
