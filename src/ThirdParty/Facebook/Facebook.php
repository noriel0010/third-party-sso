<?php declare(strict_types=1);

namespace Noriel\SSO\ThirdParty\Facebook;

use GuzzleHttp;

use Noriel\SSO\Auth\Exception\InvalidTokenException;
use Noriel\SSO\ThirdParty\Exception\FacebookException;

class Facebook
{
    private $fields;

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
    private $DEFAULT_FB_BASE_URI = 'https://graph.facebook.com/v4.0/';

    /**
     * @var String
     * this will be used as the facebook query fields by default
     */
    private $DEFAULT_FB_FIELDS = 'id,name,email';

    public function __construct(String $token, ?string $fields=null, ?string $base_uri=null){
        $this->token = $token;
        $this->fields = $fields;
        $this->base_uri = $base_uri;
    }
    
    /**
     * @throws Exception\InvalidTokenException
     * @throws Exception\FacebookException
    */
    function authenticate() : object{

        if(!isset($this->token) or empty($this->token) or !is_string($this->token)){
            throw new InvalidTokenException(
                sprintf('Invalid Token passed, `%s` received', $this->token)
            );
        }

        if (null === $this->base_uri){
            $this->base_uri = $this->DEFAULT_FB_BASE_URI;
        }
        
        if (null === $this->fields){
            $this->fields = $this->DEFAULT_FB_FIELDS;
        }

        $guzzle = new GuzzleHttp\Client(
            [
                'base_uri'        => $this->base_uri,
                'timeout'         => 5,
                'connect_timeout' => 5,
                'headers' => ['Content-type' => 'application/x-www-form-urlencoded']
            ]
        );
        try {
            $response = $guzzle->request('GET', 'me', ['query'=>['fields'=>$this->fields, 'access_token'=>$this->token]]);
            return (object)json_decode(json_encode(GuzzleHttp\json_decode($response->getBody()->getContents(), true)));
        } catch (GuzzleHttp\Exception\RequestException $exception) {
            if ($exception->hasResponse()) {
                $ex = (object) GuzzleHttp\json_decode($exception->getResponse()->getBody()->getContents(), true);
                throw new InvalidTokenException($ex->error['message'], $ex->error['code']);
            }
            throw new FacebookException($exception->getMessage(), $exception->getCode(), $exception);
        }catch (\Throwable $exception) {
            throw new FacebookException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
