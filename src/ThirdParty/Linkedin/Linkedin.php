<?php declare(strict_types=1);

namespace Noriel\SSO\ThirdParty\Linkedin;

use GuzzleHttp;

use Noriel\SSO\Auth\Exception\InvalidTokenException;
use Noriel\SSO\ThirdParty\Exception\LinkedinException;

class Linkedin
{

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
    private $projection;

    /**
     * @var String
     * this will be used as the base_uri by default
     */
    private $DEFAULT_LINKEDIN_BASE_URI = 'https://api.linkedin.com/v2/';

    /**
     * @var String
     * this will be used as the linkedin query email projection by default
     */
    private $DEFAULT_LINKEDIN_PROJECTION = '(elements*(handle~))';

    public function __construct(String $token, ?string $projection=null, ?string $base_uri=null){
        $this->token = $token;
        $this->projection = $projection;
        $this->base_uri = $base_uri;
    }

    /**
     * @throws Exception\InvalidTokenException
     * @throws Exception\LinkedinException
    */
    function authenticate() : object{

        if(!isset($this->token) or empty($this->token) or !is_string($this->token)){
            throw new InvalidTokenException(
                sprintf('Invalid Token passed, `%s` received', $this->token)
            );
        }

        if (null === $this->base_uri){
            $this->base_uri = $this->DEFAULT_LINKEDIN_BASE_URI;
        }

        if (null === $this->projection){
            $this->projection = $this->DEFAULT_LINKEDIN_PROJECTION;
        }

        $guzzle = new GuzzleHttp\Client(
            [
                'base_uri'        => $this->base_uri,
                'timeout'         => 5,
                'connect_timeout' => 5,
            ]
        );

        try {

            $response = $guzzle->request('GET', 'emailAddress?q=members&projection='.$this->projection, ['headers' => ['Authorization'=>'Bearer '.$this->token]]);
            $email = (object)json_decode(json_encode(GuzzleHttp\json_decode($response->getBody()->getContents(), true)));
            $response = $guzzle->request('GET', 'me', ['query'=>['oauth2_access_token'=>$this->token]], ['headers' => ['Content-Type'=>'application/x-www-form-urlencoded']]);
            $user_info = (object)json_decode(json_encode(GuzzleHttp\json_decode($response->getBody()->getContents(), true)));
            $merged = array_merge((array)$email, (array)($user_info));
            return (object)$merged;

        } catch (GuzzleHttp\Exception\RequestException $exception) {
            if ($exception->hasResponse()) {
                $ex = (object) GuzzleHttp\json_decode($exception->getResponse()->getBody()->getContents(), true);
                throw new InvalidTokenException($ex->message, $ex->serviceErrorCode);
            }
            throw new LinkedinException($exception->getMessage(), $exception->getCode(), $exception);
        }catch (\Throwable $exception) {
            throw new LinkedinException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
