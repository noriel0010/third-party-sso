# Third Party SSO
[![Latest Version](https://img.shields.io/github/v/release/noriel0010/third-party-sso.svg?style=flat-square)](https://github.com/noriel0010/third-party-sso/releases)

## Installation
Install this library through [Composer](https://getcomposer.org/).
 
`composer require noriel0010/third-party-sso`

## Sample Usage

```php
use Noriel\SSO\Auth\Service\ThirdPartyService;

$service = new ThirdPartyService();

try {
    $apple = $service->apple('apple.identity.token', 'your.app.as.audience')->authenticate();
    $facebook = $service->facebook('facebook.access.token')->authenticate();
    $google = $service->google('google.access.token', 'google_key')->authenticate();
    $linkedin = $service->linkedin('linkedin.access.token')->authenticate();
    $microsoft = $service->microsoft('microsoft.access.token')->authenticate();
} catch (\Throwable $th) {
    echo $th->getMessage();
}
```
The `authenticate()` method returns [Object Data Type](https://www.php.net/manual/en/language.types.object.php)

## Apple
For Apple, this assumes that you already have generated [identityToken](https://developer.apple.com/documentation/authenticationservices/asauthorizationsinglesignoncredential/3153080-identitytoken) either from [accessToken](https://developer.apple.com/documentation/authenticationservices/asauthorizationsinglesignoncredential/3153077-accesstoken) or from [refresh_token](https://developer.apple.com/documentation/sign_in_with_apple/generate_and_validate_tokens#3605122).
Remember that _identityToken_ is valid ONLY for 10 minutes.

#### Uses
* [Apple ID API](https://appleid.apple.com/)
* [Lcobucci JSON Web Token 3.3.3](https://github.com/lcobucci/jwt#jwt)
* [Guzzle](https://docs.guzzlephp.org/en/stable/)
* `https://appleid.apple.com` as Default `issuer`

#### Test
To test Apple, you can paste your freshly generated _identityToken_ in
`test/Apple/AppleTest.php:24` and your `audience` in `test/Apple/AppleTest.php:25` then run `.\vendor\bin\phpunit test/Apple`.

## Facebook
For Facebook, this assumes that you already have generated [accessToken](https://developers.facebook.com/docs/graph-api/using-graph-api/#access-tokens).

#### Uses
* [Graph Facebook v4.0](https://developers.facebook.com/docs/graph-api)
* [Guzzle](https://docs.guzzlephp.org/en/stable/)
* `id,name,email` as Default [Facebook Fields](https://developers.facebook.com/docs/graph-api/using-graph-api/#fields)

#### Test
To test Facebook, you can paste your freshly generated _accessToken_ in
`test/Facebook/FacebookTest.php:24` then run `.\vendor\bin\phpunit test/Facebook`.

## Google
For Google, this assumes that you already have generated [accessToken](https://developers.google.com/identity/protocols/oauth2#2.-obtain-an-access-token-from-the-google-authorization-server.) and [Google API key](https://cloud.google.com/docs/authentication/api-keys).

#### Uses
* [Google Person API v1](https://developers.google.com/people)
* [Guzzle](https://docs.guzzlephp.org/en/stable/)
* `names,emailAddresses` as Default [Google personFields Query Parameter](https://developers.google.com/people/api/rest/v1/people/get?hl=en#query-parameters)

#### Test
To test Google, you can paste your freshly generated _accessToken_ in
`test/Google/GoogleTest.php:24` and your `key` in `test/Google/GoogleTest.php:25` then run `.\vendor\bin\phpunit test/Google`.

## LinkedIn
For LinkedIn, this assumes that you already have generated [accessToken](https://docs.microsoft.com/en-us/linkedin/shared/authentication/client-credentials-flow?context=linkedin/consumer/context#step-2-generate-an-access-token).

#### Uses
* [Linkedin API v2](https://docs.microsoft.com/en-us/linkedin/shared/integrations/people/profile-api?context=linkedin/consumer/context#request)
* [Guzzle](https://docs.guzzlephp.org/en/stable/)
* `(elements*(handle~))` as Default [Linkedin Email Projection](https://docs.microsoft.com/en-us/linkedin/shared/api-guide/concepts/projections?context=linkedin/context)

#### Test
To test Linkedin, you can paste your freshly generated _accessToken_ in
`test/Linkedin/LinkedinTest.php:24` then run `.\vendor\bin\phpunit test/Linkedin`.

## Microsoft
For Microsoft, this assumes that you already have generated [accessToken](https://docs.microsoft.com/en-us/graph/auth/auth-concepts?context=graph%2Fapi%2F1.0&view=graph-rest-1.0#access-tokens).

#### Uses
* [Graph Microsoft API v1](https://docs.microsoft.com/en-us/graph/api/overview?view=graph-rest-1.0&preserve-view=true)
* [Guzzle](https://docs.guzzlephp.org/en/stable/)

#### Test
To test Microsoft, you can paste your freshly generated _accessToken_ in
`test/Microsoft/MicrosoftTest.php:24` then run `.\vendor\bin\phpunit test/Microsoft`.