<?php

declare(strict_types=1);

namespace App\Auth\Schemes;

use Aphiria\Authentication\AuthenticationResult;
use Aphiria\Authentication\AuthenticationScheme;
use Aphiria\Authentication\Schemes\CookieAuthenticationHandler as BaseCookieAuthenticationHandler;
use Aphiria\Authentication\Schemes\CookieAuthenticationOptions;
use Aphiria\Net\Http\IRequest;
use Aphiria\Net\Http\IResponse;
use Aphiria\Security\IPrincipal;
use Aphiria\Security\PrincipalBuilder;
use App\Auth\InvalidCredentialsException;
use App\Auth\ITokenService;
use App\Users\IUserService;
use App\Users\UserNotFoundException;
use JsonException;

/**
 * Defines a cookie authentication scheme handler
 */
final class CookieAuthenticationHandler extends BaseCookieAuthenticationHandler
{
    /** @var int The default cookie TTL in case one is not set in the cookie options */
    private const int DEFAULT_COOKIE_TTL_SECONDS = 3600;

    /**
     * @param ITokenService $tokens The token service to create/retrieve tokens from
     * @param IUserService $users The user service to retrieve users from
     */
    public function __construct(
        private readonly ITokenService $tokens,
        private readonly IUserService $users
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     * @param AuthenticationScheme<CookieAuthenticationOptions> $scheme The scheme
     * @throws InvalidCredentialsException Thrown if there was an error decoding the cookie value
     */
    public function logOut(IRequest $request, IResponse $response, AuthenticationScheme $scheme): void
    {
        $cookieValue = $this->getCookieValueFromRequest($request, $scheme);

        if ($cookieValue !== null) {
            try {
                /** @var array{userId: int, token: string} $decodedCookieValue */
                $decodedCookieValue = \json_decode(\base64_decode($cookieValue), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $ex) {
                throw new InvalidCredentialsException('Cookie contained invalid JSON', 0, $ex);
            }

            $userId = isset($decodedCookieValue['userId']) ? (int)$decodedCookieValue['userId'] : null;
            $token = $decodedCookieValue['token'] ?? null;

            if ($userId !== null && $token !== null) {
                $this->tokens->expireToken($userId, $token);
            }
        }

        parent::logOut($request, $response, $scheme);
    }

    /**
     * @inheritdoc
     * @param AuthenticationScheme<CookieAuthenticationOptions> $scheme The scheme
     * @throws UserNotFoundException Thrown if no user was found with the retrieved user ID
     */
    protected function createAuthenticationResultFromCookie(string $cookieValue, IRequest $request, AuthenticationScheme $scheme): AuthenticationResult
    {
        try {
            /** @var array{userId: int, token: string} $decodedCookieValue */
            $decodedCookieValue = \json_decode(\base64_decode($cookieValue), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return AuthenticationResult::fail('Token format is invalid', $scheme->name);
        }

        if (!isset($decodedCookieValue['userId'], $decodedCookieValue['token'])) {
            return AuthenticationResult::fail('Token format is invalid', $scheme->name);
        }

        $userId = (int)$decodedCookieValue['userId'];
        $token = (string)$decodedCookieValue['token'];

        if (!$this->tokens->validateToken($userId, $token)) {
            return AuthenticationResult::fail('Invalid token', $scheme->name);
        }

        $user = $this->users->getUserById($userId);

        return AuthenticationResult::pass(
            (new PrincipalBuilder($scheme->options->claimsIssuer ?? $scheme->name))->withNameIdentifier($user->id)
                ->withEmail($user->email)
                ->withRoles($user->roles)
                ->withAuthenticationSchemeName($scheme->name)
                ->build(),
            $scheme->name
        );
    }

    /**
     * @inheritdoc
     * @param AuthenticationScheme<CookieAuthenticationOptions> $scheme The scheme
     * @throws JsonException Thrown if there was an error encoding the JSON (very unlikely)
     */
    protected function createCookieValueForUser(IPrincipal $user, AuthenticationScheme $scheme): string|int|float
    {
        $cookieTtlSeconds = $scheme->options->cookieMaxAge ?? self::DEFAULT_COOKIE_TTL_SECONDS;
        $userId = (int)$user->primaryIdentity?->nameIdentifier;
        $token = $this->tokens->createToken($userId, $cookieTtlSeconds);

        return \base64_encode(\json_encode(['userId' => $userId, 'token' => $token], JSON_THROW_ON_ERROR));
    }
}
