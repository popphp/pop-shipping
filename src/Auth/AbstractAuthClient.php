<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Auth;

use Pop\Shipping\Client\AbstractShippingClient;

/**
 * Pop shipping auth abstract client class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
abstract class AbstractAuthClient extends AbstractShippingClient implements AuthClientInterface
{

    /**
     * Auth API URL
     * @var ?string
     */
    protected ?string $authApiUrl = null;

    /**
     * Account number
     * @var ?string
     */
    protected ?string $accountNumber = null;

    /**
     * Auth token
     * @var ?string
     */
    protected ?string $authToken = null;

    /**
     * Auth token type
     * @var ?string
     */
    protected ?string $tokenType = null;

    /**
     * Auth token expiration timestamp
     * @var ?int
     */
    protected ?int $expiration = null;

    /**
     * Auth token data
     * @var array
     */
    protected array $tokenData = [];

    /**
     * Set auth API URL
     *
     * @param  string $authApiUrl
     * @return AbstractAuthClient
     */
    public function setAuthApiUrl(string $authApiUrl): AbstractAuthClient
    {
        $this->authApiUrl = $authApiUrl;
        return $this;
    }

    /**
     * Get auth API URL
     *
     * @return ?string
     */
    public function getAuthApiUrl(): ?string
    {
        return $this->authApiUrl;
    }

    /**
     * Has auth API URL
     *
     * @return bool
     */
    public function hasAuthApiUrl(): bool
    {
        return !empty($this->authApiUrl);
    }

    /**
     * Set account number
     *
     * @param  string $accountNumber
     * @return AbstractAuthClient
     */
    public function setAccountNumber(string $accountNumber): AbstractAuthClient
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    /**
     * Get account number
     *
     * @return ?string
     */
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    /**
     * Has account number
     *
     * @return bool
     */
    public function hasAccountNumber(): bool
    {
        return !empty($this->accountNumber);
    }

    /**
     * Get token data
     *
     * @param  ?string $key
     * @return mixed
     */
    public function getTokenData(?string $key = null): mixed
    {
        if ($key !== null) {
            return $this->tokenData[$key] ?? null;
        } else {
            return $this->tokenData;
        }
    }

    /**
     * Has token data
     *
     * @return bool
     */
    public function hasTokenData(): bool
    {
        return (!empty($this->tokenData));
    }

    /**
     * Load token data
     *
     * @param  array $tokenData
     * @return AbstractAuthClient
     */
    public function loadTokenData(array $tokenData): AbstractAuthClient
    {
        $this->tokenData = $tokenData;

        if (!empty($this->tokenData['access_token'])) {
            $this->authToken = $this->tokenData['access_token'];
        }
        if (!empty($this->tokenData['token_type'])) {
            $this->tokenType = $this->tokenData['token_type'];
        }
        if (!empty($this->tokenData['expires_in'])) {
            $this->expiration = time() + $this->tokenData['expires_in'];
        } else if (!empty($this->tokenData['expiration'])) {
            $this->expiration = $this->tokenData['expiration'];
        }

        return $this;
    }

    /**
     * Load token data from file
     *
     * @param  string $tokenFile
     * @return AbstractAuthClient
     */
    public function loadTokenDataFromFile(string $tokenFile): AbstractAuthClient
    {
        if (file_exists($tokenFile)) {
            $this->loadTokenData(json_decode(file_get_contents($tokenFile), true));
        }
        return $this;
    }

    /**
     * Save token data to file
     *
     * @param  string $tokenFile
     * @param  ?array $tokenData
     * @return AbstractAuthClient
     */
    public function saveTokenDataToFile(string $tokenFile, ?array $tokenData = null): AbstractAuthClient
    {
        if ($tokenData !== null) {
            $this->loadTokenData($tokenData);
        }
        file_put_contents($tokenFile, json_encode([
            'access_token' => $this->tokenData['access_token'],
            'token_type'   => $this->tokenData['token_type'],
            'expiration'   => $this->expiration
        ], JSON_PRETTY_PRINT));

        return $this;
    }

    /**
     * Has token data file
     *
     * @param  string $tokenFile
     * @return bool
     */
    public function hasTokenDataFile(string $tokenFile): bool
    {
        return (file_exists($tokenFile));
    }

    /**
     * Has valid auth token
     *
     * @return bool
     */
    public function hasAuthToken(): bool
    {
        return (($this->authToken !== null) && (!$this->isExpired()));
    }

    /**
     * Get auth token
     *
     * @return ?string
     */
    public function getAuthToken(): ?string
    {
        return ($this->hasAuthToken()) ? $this->authToken : null;
    }

    /**
     * Fetch auth token, either the current valid one, or get a new/refreshed auth token
     *
     * @param  ?string $tokenFile
     * @param  int     $buffer     Buffer in seconds to check the expiration
     * @return ?string
     */
    public function fetchAuthToken(?string $tokenFile = null, int $buffer = 10): ?string
    {
        if ((!$this->hasAuthToken()) && ($tokenFile !== null)) {
            $this->loadTokenDataFromFile($tokenFile);
        }
        if ((!$this->hasAuthToken()) || ($this->willExpireIn() <= $buffer)) {
            $this->authenticate($tokenFile);
        }

        return $this->getAuthToken();
    }

    /**
     * Has auth token type
     *
     * @return bool
     */
    public function hasTokenType(): bool
    {
        return ($this->tokenType !== null);
    }

    /**
     * Get auth token type
     *
     * @return ?string
     */
    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    /**
     * Has auth token expiration
     *
     * @return bool
     */
    public function hasExpiration(): bool
    {
        return ($this->expiration !== null);
    }

    /**
     * Get auth token expiration
     *
     * @return ?int
     */
    public function getExpiration(): ?int
    {
        return $this->expiration;
    }

    /**
     * Determine if the auth token has expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return (($this->hasExpiration()) && (time() > $this->expiration));
    }

    /**
     * Determine when the token will expire in seconds
     *
     * @return int
     */
    public function willExpireIn(): int
    {
        return (($this->hasExpiration()) && (!$this->isExpired())) ? ($this->expiration - time()) : 0;
    }

    /**
     * Authenticate and get auth token
     *
     * @param  ?string $tokenFile
     * @throws Exception
     * @return AbstractAuthClient
     */
    public function authenticate(?string $tokenFile = null): AbstractAuthClient
    {
        if (!$this->hasClient()) {
            throw new Exception('Error: The auth client does not have an HTTP client.');
        }

        $response = $this->client->send($this->authApiUrl);

        if ($response->isSuccess()) {
            $this->loadTokenData($response->getParsedResponse());

            if (($this->hasTokenData()) && ($tokenFile !== null)) {
                $this->saveTokenDataToFile($tokenFile);
            }
        }
        return $this;
    }

    /**
     * Refresh auth token
     *
     * @param  ?string $tokenFile
     * @return AbstractAuthClient
     */
    public function refresh(?string $tokenFile = null): AbstractAuthClient
    {
        $this->authToken  = null;
        $this->expiration = null;
        $this->tokenData  = [];

        $this->authenticate($tokenFile);

        return $this;
    }

}
