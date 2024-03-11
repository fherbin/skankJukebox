# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) bash

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)


## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— skankJukebox DEV 🎵 ———————————————————————————————————————————————————————

assets: c=asset-map:compile ## compile assets
assets:	sf

db-upgrade:  ## update db schema
	@$(SYMFONY) doctrine:schema:update --force --no-interaction

db-upgrade-test: ## update db schema for test
	@$(SYMFONY) --env=test doctrine:schema:update --force --no-interaction

build-cache: ## Builds the Docker images with cache
	@$(DOCKER_COMP) build

fix-style: ## fix coding style with php-cs
	$(PHP) vendor/bin/php-cs-fixer -v -n fix

clear: ## clear cache and assets
	@$(PHP_CONT) rm -fR ./var
	@$(PHP_CONT) rm -fR ./public/assets

vendor-dev: ## Install dev vendors according to the current composer.lock file
vendor-dev: c=install
vendor-dev: composer

ci: ## execute ci : rector, phpstan, php-cs
	@$(PHP) vendor/bin/rector -n && $(PHP) vendor/bin/phpstan && $(PHP) vendor/bin/php-cs-fixer -v -n check
	@$(SYMFONY) lint:yaml ./
	@$(SYMFONY) lint:twig
	@$(SYMFONY) lint:container


install-dev: down clear build up db-upgrade-test vendor-dev ## install jukebox in a development environment

## —— skankJukebox use 🎵 ———————————————————————————————————————————————————————

install: down clear build-cache up db-upgrade vendor assets ## install jukebox in a production environment
