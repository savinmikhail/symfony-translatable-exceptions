<?php

namespace SavinMikhail\TranslatableExceptionsPlugin;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

final readonly class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        require_once __DIR__ . '/NoHardcodedMessagesInExceptionsChecker.php';
        $registration->registerHooksFromClass(NoHardcodedMessagesInExceptionsChecker::class);
    }
}
