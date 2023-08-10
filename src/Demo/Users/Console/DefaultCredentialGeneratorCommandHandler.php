<?php

declare(strict_types=1);

namespace App\Demo\Users\Console;

use Aphiria\Console\Commands\Attributes\Command;
use Aphiria\Console\Commands\Attributes\Option;
use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Input\OptionType;
use Aphiria\Console\Output\IOutput;
use Aphiria\Console\Output\Prompts\Confirmation;
use Aphiria\Console\Output\Prompts\Prompt;
use RuntimeException;

/**
 * Defines the default credential generator command handler
 */
#[Command('user:generate-default-credentials', description: 'Generates the default user credentials and updates the .env file with them')]
#[Option('show-password', OptionType::NoValue, 's', 'Show the generated password', false)]
final class DefaultCredentialGeneratorCommandHandler implements ICommandHandler
{
    /** @const The length of the default user password */
    private const DEFAULT_USER_PASSWORD_LENGTH = 32;

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output): void
    {
        $prompt = new Prompt();

        if (!$prompt->ask(new Confirmation('Would you like to update the default user credentials in your .env file? [Y/N] '), $output)) {
            $output->writeln('<comment>For security, remember to update these credentials before deploying to production</comment>');

            return;
        }

        $output->writeln('<info>Generating default user credentials...</info>');
        $defaultUserEmail = '';

        while (\strlen(\trim($defaultUserEmail)) === 0) {
            $output->write('Enter the email address of the default admin user: ');
            $defaultUserEmail = $output->readLine();
        }

        $defaultUserPassword = \bin2hex(\random_bytes(self::DEFAULT_USER_PASSWORD_LENGTH));
        $dotEnvFilePath = __DIR__ . '/../../../../.env';

        if (!\file_exists($dotEnvFilePath)) {
            throw new RuntimeException("No .env file found at $dotEnvFilePath");
        }

        $dotEnvContents = \file_get_contents($dotEnvFilePath);
        $dotEnvContents = \preg_replace('/^USER_DEFAULT_EMAIL=.*$/m', "USER_DEFAULT_EMAIL=$defaultUserEmail", $dotEnvContents);
        $dotEnvContents = \preg_replace('/^USER_DEFAULT_PASSWORD=.*$/m', "USER_DEFAULT_PASSWORD=$defaultUserPassword", $dotEnvContents);
        \file_put_contents($dotEnvFilePath, $dotEnvContents);

        $output->writeln('<success>.env file updated</success>');

        if (\array_key_exists('show-password', $input->options)) {
            $output->writeln("<info>Password:</info> $defaultUserPassword");
        }
    }
}
