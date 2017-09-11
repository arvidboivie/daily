<?php

namespace Boivie\Spotify;

use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyApi
{

    protected $clientId;
    protected $clientSecret;
    protected $redirectURI;

    public function __construct(string $clientId, string $clientSecret, $redirectURI = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectURI = $redirectURI;
    }

    public function getAuthorizeUrl(array $scopes)
    {
        $session = new Session(
            $this->clientId,
            $this->clientSecret,
            $this->redirectURI
        );

        $authorizeUrl = $session->getAuthorizeUrl(array(
            'scope' => $scopes
        ));

        return $authorizeUrl;
    }

    public function getAccessToken($code)
    {
        $session = new Session(
            $this->clientId,
            $this->clientSecret,
            $this->redirectURI
        );

        // Request a access token using the code from Spotify
        $session->requestAccessToken($code);
        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();
        $expiration = $session->getTokenExpiration();

        // Create API wrapper and set access token
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        // Start using the API!
        $userInfo = $api->me();

        // Store access and refresh token
        $host = '***REMOVED***';
        $db = '***REMOVED***';
        $user = '***REMOVED***';
        $password = '***REMOVED***';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $pdo = new \PDO($dsn, $user, $password);

        $tokenStatement = $pdo->prepare('INSERT INTO auth(username, access_token, refresh_token, expires)
                                         VALUES(:username, :access_token, :refresh_token, :expires)
                                         ON DUPLICATE KEY UPDATE
                                         access_token= :access_token,
                                         refresh_token= :refresh_token,
                                         expires= :expires');

        $tokenStatement->execute([
            'username' => $userInfo->id,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires' => $expiration,
        ]);
    }

    public function getApiWrapper()
    {
        $session = new Session($this->clientId, $this->clientSecret);
        $api = new SpotifyWebAPI();

        $host = '***REMOVED***';
        $db = '***REMOVED***';
        $user = '***REMOVED***';
        $password = '***REMOVED***';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $pdo = new \PDO($dsn, $user, $password);

        $tokenStatement = $pdo->prepare(
            "SELECT
            access_token,
            refresh_token,
            expires
            FROM `auth`
            WHERE username = 'arvid.b'"
        );

        $tokenStatement->execute();

        $result = $tokenStatement->fetchObject();

        $accessToken = $result->access_token;

        if (time() > $result->expires) {
            $session->refreshAccessToken($result->refresh_token);
            $accessToken = $session->getAccessToken();
        }

        // Set the access token on the API wrapper
        $api->setAccessToken($accessToken);

        return $api;
    }
}
