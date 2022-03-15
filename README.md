# Commission Calculator

![prod-build](https://github.com/jafaripur/commission-calculator/actions/workflows/build-prod.yml/badge.svg)
![test](https://github.com/jafaripur/commission-calculator/actions/workflows/run-test.yml/badge.svg)

This project is refactoring of this [Task](https://gist.github.com/PayseraGithub/634074b26e1a2a5e4b8d39b8eb050f9f) (Task - PHP - Refactoring)

Detect country by Bin and calculate commission based on country.

## Installation

The minimal configuration need for applying API key for fetching currencies or Bin information. 

Override some configuration for deployment and test environment. Sample of config available in `.env` file. For overriding configuration for deployment environment, create `.env.local` file. For the test environment creates file `.env.test.local`. 

After installing composer with install command this file will be created if not exist:

```
.env.local
.env.test.local
```

Get API key:


```
https://currencyfreaks.com   Env variable: CURRENCY_FREAK_API
https://www.bincodes.com     Env variable: BIN_TABLE_API
https://bintable.com         Env variable: BIN_CODES_API

```

### Without Docker

First download [Composer](https://getcomposer.org/download/).

```bash

./composer.phar install -o

```

Run application:

```bash

php bin/console commission:calculate input.txt

```

## With Docker

Build image:

```bash

export DOCKER_BUILDKIT=1 && docker build \
    -f "./docker/Dockerfile.prod" \
    -t "commission-calculator-prod:latest" . \
    --build-arg APP_SECRET=<api_key> \
    --build-arg CURRENCY_FREAK_API=<api_key> \
    --build-arg BIN_TABLE_API=<api_key> \
    --build-arg BIN_CODES_API=<api_key>

```

If `.env.local` file exists and configured, omit the docker image builds argument.

Running application from docker image:

```bash

docker run -it --init --rm \
    -v "${PWD}/input.txt:/app/input.txt" \
    commission-calculator-prod:latest input.txt

```

Create `input.txt` with this format:

```

{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}


```

### Run Test

The tests can run with and without Docker.

Without docker:

```bash

php vendor/bin/phpunit

```

Build the test docker image:

```bash

export DOCKER_BUILDKIT=1 && docker build \
    -f "./docker/Dockerfile" \
    -t "commission-calculator-test:latest" . \
    --build-arg APP_SECRET=<api_key> \
    --build-arg CURRENCY_FREAK_API=<api_key> \
    --build-arg BIN_TABLE_API=<api_key> \
    --build-arg BIN_CODES_API=<api_key>

```

if `.env.test.local` file exists and configured, the omit docker image builds argument.

Run test on docker container:

```bash

docker run -it --init --rm commission-calculator-test:latest

```