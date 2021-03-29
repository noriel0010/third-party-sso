<?php declare(strict_types=1);

namespace Noriel\SSO\Auth\Service;

use Noriel\SSO\Auth\Exception\InvalidBaseUriException;
use Noriel\SSO\ThirdParty\Facebook\Facebook;
use Noriel\SSO\ThirdParty\Google\Google;
use Noriel\SSO\ThirdParty\Microsoft\Microsoft;
use Noriel\SSO\ThirdParty\Linkedin\Linkedin;
use Noriel\SSO\ThirdParty\Apple\Apple;

class ThirdPartyService
{

    /**
     * @throws Exception\InvalidBaseUriException
    */
    function facebook(String $token, ?string $fields=null, ?string $base_uri=null){
        $this->check_base_uri($base_uri);
        return new Facebook($token, $fields, $base_uri);
    }

    function google(String $token, String $key, ?string $personFields=null, ?string $base_uri=null){
        $this->check_base_uri($base_uri);
        return new Google($token, $key, $personFields, $base_uri);
    }

    function microsoft(String $token, ?string $base_uri=null){
        $this->check_base_uri($base_uri);
        return new Microsoft($token, $base_uri);
    }

    function linkedin(String $token, ?string $projection=null, ?string $base_uri=null){
        $this->check_base_uri($base_uri);
        return new Linkedin($token, $projection, $base_uri);
    }

    function apple(String $token, ?string $aud=null, ?string $issuer=null, ?string $base_uri=null){
        $this->check_base_uri($base_uri);
        return new Apple($token, $aud, $issuer, $base_uri);
    }

    private function check_base_uri(string $base_uri=null){
        if(null !== $base_uri){
            if (filter_var($base_uri, FILTER_VALIDATE_URL) === FALSE) {
                throw new InvalidBaseUriException(sprintf('Invalid `base_uri` passed, `%s` received', $base_uri));
            }
        }
    }
}
