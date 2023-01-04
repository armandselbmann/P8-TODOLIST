##
## Symfony Server
## --------------
start: ## Start symfony's server
	symfony server:start -d

stop: ## Stop symfony's server
	symfony server:stop

##
## PHP Unit
## --------------
test: ## Run the tests
	vendor/bin/phpunit

coverage: ## Test Coverage
	vendor/bin/phpunit --coverage-html web/test-coverage

##
## PHP CodeSniffer
## ---------------
snifDefault: ## Analyse DefaultController
	./vendor/bin/phpcs ./src/AppBundle/Controller/DefaultController.php
snifSecurity: ## Analyse SecurityController
	./vendor/bin/phpcs ./src/AppBundle/Controller/SecurityController.php
snifTask: ## Analyse TaskController
	./vendor/bin/phpcs ./src/AppBundle/Controller/TaskController.php
snifUser: ## Analyse UserController
	./vendor/bin/phpcs ./src/AppBundle/Controller/UserController.php

##
## PHP Stan
## --------
stan: ## Make a PHP Stan analyse level 8
	vendor/bin/phpstan analyse

##
## PHP Cs Fixer
## ------------
fixer: ## Verifies and enforces PSR-1 and PSR-2 standards
	php php-cs-fixer.phar fix src web

##

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
