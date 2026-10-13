<?php

declare(strict_types=1);

require __DIR__ . '/../src/DaPigGuy/libPiggyEconomy/exceptions/MissingProviderDependencyException.php';
require __DIR__ . '/../src/DaPigGuy/libPiggyEconomy/exceptions/UnknownProviderException.php';
require __DIR__ . '/../src/DaPigGuy/libPiggyEconomy/providers/EconomyProvider.php';
require __DIR__ . '/../src/DaPigGuy/libPiggyEconomy/providers/EconomySProvider.php';
require __DIR__ . '/../src/DaPigGuy/libPiggyEconomy/providers/BedrockEconomyProvider.php';
require __DIR__ . '/../src/DaPigGuy/libPiggyEconomy/providers/SimpleEconomyProvider.php';
require __DIR__ . '/../src/DaPigGuy/libPiggyEconomy/providers/XPProvider.php';
require __DIR__ . '/../src/DaPigGuy/libPiggyEconomy/libPiggyEconomy.php';

use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\XPProvider;

libPiggyEconomy::init();
$provider = libPiggyEconomy::getProvider(['provider' => 'xp']);

if (!$provider instanceof XPProvider) {
    throw new RuntimeException('The built-in XP provider was not created by the standalone factory.');
}

echo "standalone provider factory passed\n";
