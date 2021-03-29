<?php declare(strict_types=1);

namespace Noriel\SSO\ThirdParty\Google;

use GuzzleHttp;

use Noriel\SSO\Auth\Exception\InvalidTokenException;
use Noriel\SSO\ThirdParty\Exception\GoogleException;

class Google
{
    /**
     * @var String
     */
    private $personFields;

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
    private $key;

    /**
     * @var String
     * this will be used as the base_uri by default
     */
    private $DEFAULT_GOOGLE_BASE_URI = 'https://people.googleapis.com/v1/people/';

    /**
     * @var String
     * this will be used as the google persons query fields by default
     */
    private $DEFAULT_GOOGLE_PERSON_FIELDS = 'names,emailAddresses';

    public function __construct(String $token, String $key, ?string $personFields=null, ?string $base_uri=null){
        $this->token = $token;
        $this->key = $key;
        $this->personFields = $personFields;
        $this->base_uri = $base_uri;
    }

    /**
     * @throws Exception\InvalidTokenException
     * @throws Exception\GoogleException
    */
    function authenticate() : object{

        if(!isset($this->token) or empty($this->token) or !is_string($this->token)){
            throw new InvalidTokenException(
                sprintf('Invalid Token passed, `%s` received', $this->token)
            );
        }

        if(!isset($this->key) or empty($this->key) or !is_string($this->key)){
            throw new GoogleException('Google `key` is Invalid');
        }

        if (null === $this->base_uri){
            $this->base_uri = $this->DEFAULT_GOOGLE_BASE_URI;
        }
        
        if (null === $this->personFields){
            $this->personFields = $this->DEFAULT_GOOGLE_PERSON_FIELDS;
        }

        $guzzle = new GuzzleHttp\Client(
            [
                'base_uri'        => $this->base_uri,
                'timeout'         => 5,
                'connect_timeout' => 5,
                'headers' => ['Authorization' => 'Bearer ' . $this->token]
            ]
        );
        try {
            $response = $guzzle->request('GET', 'me', ['query'=>['personFields'=>$this->personFields, 'key'=>$this->key]]);
            return (object)json_decode(json_encode(GuzzleHttp\json_decode($response->getBody()->getContents(), true)));
        } catch (GuzzleHttp\Exception\RequestException $exception) {
            if ($exception->hasResponse()) {
                $ex = (object) GuzzleHttp\json_decode($exception->getResponse()->getBody()->getContents(), true);
                throw new InvalidTokenException($ex->error['message'], $ex->error['code']);
            }
            throw new GoogleException($exception->getMessage(), $exception->getCode(), $exception);
        }catch (\Throwable $exception) {
            throw new GoogleException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
