# libPiggyEconomy

libPiggyEconomy is a virion for easy support of multiple economy providers.

This thebigcrafter-maintained fork keeps the upstream provider API and adds an
explicit standalone factory for its built-in providers. The factory is needed
by the [thebigcrafter DevTools](https://github.com/NhanAZ/DevTools) build
boundary so a packaged plugin does not retain an unprovable dynamic class
instantiation. A provider added by an
extension must be represented in that factory before producing a standalone
PHAR; no undeclared provider code is implicitly bundled.

## Supported Providers

- [EconomyAPI](https://poggit.pmmp.io/p/EconomyAPI) by onebone/poggit-orphanage
- [BedrockEconomy](https://poggit.pmmp.io/p/BedrockEconomy) by cooldogedev
- SimpleEconomy
- Experience (PMMP)

## Usage

### Setup

Import the library class and initialize the built-in provider registry before
requesting a provider:

```php
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;

libPiggyEconomy::init();
```

### Using Economy Providers

```php
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;

libPiggyEconomy::getProvider($providerInformation);
```

`$providerInformation` is an array with a `provider` key. Provider names are
matched case-insensitively. For providers that rely on another plugin, that
plugin must be installed and enabled on the server; otherwise
`MissingProviderDependencyException` is thrown.

#### Economy Provider Methods

| Method                                                                                            | Description                | Callback Signature                                   | Callback Description                                                      |
|---------------------------------------------------------------------------------------------------|----------------------------|------------------------------------------------------|---------------------------------------------------------------------------|
| ```EconomyProvider::getMonetaryUnit(): string```                                                  | Returns symbol of currency | `none`                                               | `none`                                                                    |
| ```EconomyProvider::getMoney(Player $player, callable $callback): void```                         | Get balance of a player    | <code>function(float&#124;int $amount) void{}</code> | Returns default balance if player wasn't found, float&#124;int otherwise. |
| ```EconomyProvider::giveMoney(Player $player, float $amount, ?callable $callback = null): void``` | Give money to a player     | `function(bool $success): void{}`                    | Returns true if money was given successfully, otherwise false.            |
| ```EconomyProvider::takeMoney(Player $player, float $amount, ?callable $callback = null): void``` | Take money from a player   | `function(bool $success): void{}`                    | Returns true if money was taken successfully, otherwise false.            |
| ```EconomyProvider::setMoney(Player $player, float $amount, ?callable $callback = null): void```  | Set balance of a player    | `function(bool $success): void{}`                    | Returns true if money was set successfully, otherwise false.              |

### Error Handling

There are several exceptions that can be thrown that you may want to handle in your plugin:

* MissingProviderDependencyException
* UnknownProviderException

## Examples

config.yml

```yaml
economy:
  provider: bedrockeconomy
```

AmazingPlugin.php

```php
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;

class AmazingPlugin extends PluginBase{
    public $economyProvider;
    
    public function onEnable(): void{
        $this->saveDefaultConfig();
        libPiggyEconomy::init();
        $this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
    }
}
```
