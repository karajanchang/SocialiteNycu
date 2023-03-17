<?php

namespace SocialiteProviders\Nycu;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'NYCU';
    public const BASE_URL = 'https://id.nycu.edu.tw/api/';
    protected $scopes = ['profile', 'status'];

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(self::BASE_URL.'/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return self::BASE_URL.'/token';
    }

    protected function getTokenFields($code): array
    {
        return array_merge([
            'grant_type' => 'authorization_code',
        ], parent::getTokenFields());
    }

    /**
     * {@inheritDoc}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getUserByToken($token): array
    {
        // Check if the credentials response body already has the data provided to us
        // If not, fetch the data from their API
        if (empty($this->credentialsResponseBody) || empty($this->credentialsResponseBody['sub'])) {
            $response = $this->getHttpClient()->post(self::BASE_URL.'/userinfo', [
                RequestOptions::HEADERS => [
                    'Authorization' => "Bearer $token",
                    'Accept'        => 'application/json',
                ],
            ]);

            return json_decode((string) $response->getBody(), true);
        }

        return $this->credentialsResponseBody;
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User())
            ->setRaw($user)
            ->map([
                'id'    => $user['sub'],
                'name'  => $user['name'],
                'email' => $user['email'],
            ]);
    }

}