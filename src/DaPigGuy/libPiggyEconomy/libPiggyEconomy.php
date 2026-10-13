<?php

declare(strict_types=1);

namespace DaPigGuy\libPiggyEconomy;

use DaPigGuy\libPiggyEconomy\exceptions\MissingProviderDependencyException;
use DaPigGuy\libPiggyEconomy\exceptions\UnknownProviderException;
use DaPigGuy\libPiggyEconomy\providers\BedrockEconomyProvider;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use DaPigGuy\libPiggyEconomy\providers\EconomySProvider;
use DaPigGuy\libPiggyEconomy\providers\SimpleEconomyProvider;
use DaPigGuy\libPiggyEconomy\providers\XPProvider;

class libPiggyEconomy
{
    public static bool $hasInitiated = false;

    /**
     * @var string[] $economyProviders
     * @phpstan-var array<class-string<EconomyProvider>>
     */
    public static array $economyProviders;

    public static function init(): void
    {
        if (!self::$hasInitiated) {
            self::$hasInitiated = true;

            self::registerProvider(["economys", "economyapi"], EconomySProvider::class);
            self::registerProvider(["bedrockeconomy"], BedrockEconomyProvider::class);
            self::registerProvider(["simpleeconomy"], SimpleEconomyProvider::class);
            self::registerProvider(["xp", "exp", "experience"], XPProvider::class);
        }
    }

    /**
     * @phpstan-param list<string> $providerNames
     * @phpstan-param class-string<EconomyProvider> $economyProvider
     */
    public static function registerProvider(array $providerNames, string $economyProvider): void
    {
        foreach ($providerNames as $providerName) {
            if (isset(self::$economyProviders[strtolower($providerName)])) continue;
            self::$economyProviders[strtolower($providerName)] = $economyProvider;
        }
    }

    /**
     * @phpstan-param array{provider: string} $providerInformation
     * @throws UnknownProviderException
     * @throws MissingProviderDependencyException
     */
    public static function getProvider(array $providerInformation): EconomyProvider
    {
        if (!isset(self::$economyProviders[strtolower($providerInformation["provider"])])) {
            throw new UnknownProviderException("Provider " . $providerInformation["provider"] . " not found.");
        }
        $provider = self::$economyProviders[strtolower($providerInformation["provider"])];
        if (!self::providerDependenciesAvailable($provider)) {
            throw new MissingProviderDependencyException("Dependencies for provider " . $providerInformation["provider"] . " not found.");
        }
        return self::createProvider($provider, $providerInformation);
    }

    private static function providerDependenciesAvailable(string $provider): bool
    {
        return match ($provider) {
            EconomySProvider::class => EconomySProvider::checkDependencies(),
            BedrockEconomyProvider::class => BedrockEconomyProvider::checkDependencies(),
            SimpleEconomyProvider::class => SimpleEconomyProvider::checkDependencies(),
            XPProvider::class => XPProvider::checkDependencies(),
            default => false,
        };
    }

    /**
     * Create one of the providers shipped by this virion without a dynamic
     * class instantiation. This keeps standalone Virion shading deterministic.
     *
     * Providers registered by an extension must be added to this explicit
     * factory before the virion is packaged; silently resolving an undeclared
     * class would make the resulting PHAR depend on development-only code.
     *
     * @param array{provider: string} $providerInformation
     * @throws UnknownProviderException
     */
    private static function createProvider(string $provider, array $providerInformation): EconomyProvider
    {
        return match ($provider) {
            EconomySProvider::class => new EconomySProvider(),
            BedrockEconomyProvider::class => new BedrockEconomyProvider(),
            SimpleEconomyProvider::class => new SimpleEconomyProvider(),
            XPProvider::class => new XPProvider(),
            default => throw new UnknownProviderException(
                "Provider {$providerInformation['provider']} is not included in the standalone provider factory.",
            ),
        };
    }
}
