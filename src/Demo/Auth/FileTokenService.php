<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2023 David Young
 * @license   https://github.com/aphiria/app/blob/1.x/LICENSE.md
 */

declare(strict_types=1);

namespace App\Demo\Auth;

use App\Demo\Users\UserViewModel;
use JsonException;

/**
 * Defines the token service backed by file storage
 */
final class FileTokenService implements ITokenService
{
    /** @var int The length of generated tokens */
    private const TOKEN_LENGTH = 32;
    /** @var int The default page size */
    private const DEFAULT_PAGE_SIZE = 100;

    /**
     * @param string $filePath The path to the file that contains the tokens
     */
    public function __construct(private readonly string $filePath)
    {
    }

    /**
     * @inheritdoc
     * @throws JsonException Thrown if there was an error decoding or encoding the JSON
     */
    public function createToken(string $userId): string
    {
        $token = \bin2hex(\random_bytes(self::TOKEN_LENGTH));
        $pageNumber = 0;
        $tokensToUserIds = [];

        while (\count($pagedTokensToUserIds = $this->readTokensFromFile($pageNumber, self::DEFAULT_PAGE_SIZE)) > 0) {
            $tokensToUserIds = [...$tokensToUserIds, ...$pagedTokensToUserIds];
            $pageNumber++;
        }

        $tokensToUserIds[$token] = $userId;
        $this->writeTokensToFile($tokensToUserIds);

        return $token;
    }

    /**
     * @inheritdoc
     * @throws JsonException Thrown if there was an error decoding or encoding the JSON
     */
    public function expireToken(string $token): void
    {
        $pageNumber = 0;
        /** @var array<string, string> $tokensToUserIds */
        $tokensToUserIds = [];

        while (\count($pagedTokensToUserIds = $this->readTokensFromFile($pageNumber, self::DEFAULT_PAGE_SIZE)) > 0) {
            $tokensToUserIds = [...$tokensToUserIds, ...$pagedTokensToUserIds];
            $pageNumber++;
        }

        unset($tokensToUserIds[$token]);
        $this->writeTokensToFile($tokensToUserIds);
    }

    /**
     * @inheritdoc
     * @throws JsonException Thrown if there was an error decoding the JSON
     */
    public function getUserIdFromToken(string $token): ?string
    {
        $pageNumber = 0;

        while (\count($pagedTokensToUserIds = $this->readTokensFromFile($pageNumber, self::DEFAULT_PAGE_SIZE)) > 0) {
            if (isset($pagedTokensToUserIds[$token])) {
                return $pagedTokensToUserIds[$token];
            }

            $pageNumber++;
        }

        return null;
    }

    /**
     * Reads the tokens from the local file
     *
     * @param int $pageNumber The page to retrieve
     * @param int $pageSize The size of the page to retrieve
     * @return array<string, string> The mapping of tokens to user IDs
     * @throws JsonException Thrown if there was an error decoding the contents of the file
     */
    private function readTokensFromFile(int $pageNumber, int $pageSize): array
    {
        if (!\file_exists($this->filePath)) {
            return [];
        }

        /** @var array<string, string>|false $tokensToUserIds */
        $tokensToUserIds = \json_decode(\file_get_contents($this->filePath), true, 512, JSON_THROW_ON_ERROR);

        if (!\is_array($tokensToUserIds)) {
            return [];
        }

        return \array_slice($tokensToUserIds, $pageNumber, $pageSize);
    }

    /**
     * Writes the tokens back to the file
     *
     * @param array<string, string> $tokensToUserIds The mapping of tokens to user IDs
     * @throws JsonException Thrown if there was an error encoding the tokens
     */
    private function writeTokensToFile(array $tokensToUserIds): void
    {
        \file_put_contents($this->filePath, \json_encode($tokensToUserIds, JSON_THROW_ON_ERROR));
    }
}
