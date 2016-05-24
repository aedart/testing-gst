[![Latest Stable Version](https://poser.pugx.org/aedart/testing-gts/v/stable)](https://packagist.org/packages/aedart/testing-gts)
[![Total Downloads](https://poser.pugx.org/aedart/testing-gts/downloads)](https://packagist.org/packages/aedart/testing-gts)
[![Latest Unstable Version](https://poser.pugx.org/aedart/testing-gts/v/unstable)](https://packagist.org/packages/aedart/testing-gts)
[![License](https://poser.pugx.org/aedart/testing-gts/license)](https://packagist.org/packages/aedart/testing-gts)

# Testing-GTS

Utilities that allows you to test special "getter setter traits", which are found throughout many of my packages.

# Contents

* [When to use this](#when-to-use-this)
* [How to install](#how-to-install)
* [Quick start](#quick-start)
* [License](#license)

## When to use this

If you are generating lots of getter and setter traits, which adhere to the style (or perhaps standard) that I do, then this utility can speed
up a lot of your testing.

## How to install

```console

composer require aedart/testing-gst
```

## Quick Start

Inside your test case, use the given trait and invoke the `assertGetterSetterTraitMethods` method:

```php
class MyTraitTest extends Test
{

    // ... setup not shown ... //

    /**
     * @test
     */
    public function canTestAllOfTraitsMethods()
    {
        $this->assertGetterSetterTraitMethods(
            PersonNameTrait::class,
            $this->faker->unique()->name,
            $this->faker->unique()->name
        );
    }
}
```

## License

[BSD-3-Clause](http://spdx.org/licenses/BSD-3-Clause), Read the LICENSE file included in this package