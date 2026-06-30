# PHPStan LCOM

[![PHPUnit](https://github.com/d435345/phpstan-lcom/actions/workflows/tests.yml/badge.svg)](https://github.com/d435345/phpstan-lcom/actions/workflows/tests.yml)

PHPStan rules to measure **Lack of Cohesion of Methods (LCOM4)** and report classes with low cohesion.

## What is LCOM4?

LCOM4 (Lack of Cohesion of Methods) measures how strongly the methods of a class are related to each other.

**How it works:**
- Each method is a node in a graph
- Two methods are connected (edge) when they access the same instance property (`$this->foo`) or one calls the other (`$this->bar()`)
- LCOM4 = number of connected components

| LCOM4 | Meaning |
|-------|---------|
| 1     | All methods are connected — high cohesion |
| 2+    | Methods form disconnected groups — consider splitting the class |

**Example:** A class with methods that work with `$this->email` and methods that work with `$this->logger` (without overlap) has LCOM4 = 2. The class should probably be split.

## Install

```bash
composer require d435345/phpstan-lcom --dev
```

With [PHPStan extension installer](https://github.com/phpstan/extension-installer) the rule is registered automatically.

## Usage

Configure the maximum allowed LCOM4 value in your `phpstan.neon`:

```neon
parameters:
    lcom4:
        maxLcom4: 2
```

Classes with LCOM4 above this threshold will be reported as errors:

```
 Class App\SomeClass has LCOM4 value of 3. Max allowed is 2. Consider refactoring!
```

### False positive prevention

Getters, setters, and magic methods (`__construct`, `__toString`, etc.) are automatically filtered out to avoid artificially inflating the LCOM4 value.

## How LCOM4 is calculated

The calculator builds a graph where:

1. **Nodes** = non-abstract, non-magic, non-getter/setter methods
2. **Edges** exist between two methods if they share at least one instance property access or one calls the other via `$this->method()`
3. **Result** = number of connected components (DFS-based counting)

## Development

```bash
# Install dependencies
composer install

# Run tests
vendor/bin/phpunit

# Run PHPStan self-analysis
vendor/bin/phpstan analyse
```

## Testing structure

Tests follow the pattern of [cognitive-complexity](https://github.com/TomasVotruba/cognitive-complexity):

```
tests/
├── Internal/
│   └── Lcom4Calculator/
│       ├── Lcom4CalculatorTest.php      # Direct calculator unit tests
│       └── Fixture/
│           ├── cohesive_lcom4_1.php.inc
│           ├── two_groups_lcom4_2.php.inc
│           └── ...
└── Rules/
    ├── MaxLcom4RuleTest.php              # PHPStan integration tests
    └── Fixture/
        └── ClassWith*.php
```

Each calculator fixture is a `.php.inc` file whose filename encodes the expected LCOM4 value (e.g., `three_groups_lcom4_3.php.inc`).

## License

MIT
