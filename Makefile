PHPUSERCONNECT = docker exec -it --user www-data $$(docker container ps -q --filter "name=php")

##
## Set up application
## --------------------
install: up composerinstall dbinit phpunitconfig githooksconfig

composerinstall: ## Composer install
	$(PHPUSERCONNECT) composer install

dbinit: ## Init database and fixtures persist
	$(PHPUSERCONNECT) bin/console doctrine:database:create
	$(PHPUSERCONNECT) bin/console doctrine:schema:update -f
	$(PHPUSERCONNECT) bin/console doctrine:fixtures:load --no-interaction

githooksconfig: ## Init hooks folder
	git config core.hooksPath "./hooks"

##
## Docker
## --------------------
reset: down up
up: ## Start docker-compose
	docker-compose -f docker-compose.yml up --build -d
down: ## Stop docker-compose
	docker-compose -f docker-compose.yml down --remove-orphans
ps: ## List actif containers
	docker ps
userphp: ## Connect on actif php container with www-data user
	docker exec -it --user www-data $$(docker container ps -q --filter "name=php") bash
rootphp:## Connect on actif php container with root user
	docker exec -it $$(docker container ps -q --filter "name=php") bash

##
## PHP Unit
## --------------------
phpunitconfig: ## Config phpunit before generate code coverage or tests
	$(PHPUSERCONNECT) vendor/bin/phpunit --generate-configuration
test: ## Run the tests
	$(PHPUSERCONNECT) vendor/bin/phpunit tests --color --testdox
coverage: ## Test Coverage
	$(PHPUSERCONNECT) vendor/bin/phpunit --coverage-html public/test-coverage

##
## PHP CodeSniffer
## --------------------
sniffer:
	$(PHPUSERCONNECT) vendor/bin/phpcs src/Controller
sniffer_default: ## Analyse DefaultController
	$(PHPUSERCONNECT) vendor/bin/phpcs ./src/Controller/DefaultController.php
sniffer_security: ## Analyse SecurityController
	$(PHPUSERCONNECT) vendor/bin/phpcs ./src/Controller/SecurityController.php
sniffer_task: ## Analyse TaskController
	$(PHPUSERCONNECT) vendor/bin/phpcs ./src/Controller/TaskController.php
sniffer_user: ## Analyse UserController
	$(PHPUSERCONNECT) vendor/bin/phpcs ./src/Controller/UserController.php

##
## PHP Stan
## --------------------
stan: ## Make a PHP Stan analyse level 8
	$(PHPUSERCONNECT) vendor/bin/phpstan analyse src

##
## SQL Query
## --------------------
allusers: ## Select * FROM user
	$(PHPUSERCONNECT) bin/console dbal:run-sql 'SELECT * FROM user'
alltasks: ## Select * FROM task
	$(PHPUSERCONNECT) bin/console dbal:run-sql 'SELECT * FROM task'


##
## Reset Test Database
## --------------------
resetTestDatabase: dropTestDatabase updateTestDatabase addFixturesTestDatabase
dropTestDatabase: ## Drop test Database
	$(PHPUSERCONNECT) bin/console --env=test doctrine:schema:drop --force
updateTestDatabase: ## Update test Database
	$(PHPUSERCONNECT) bin/console --env=test doctrine:schema:update --force
addFixturesTestDatabase: ## Add fixtures on test Database
	$(PHPUSERCONNECT) bin/console --env=test doctrine:fixtures:load --no-interaction

##
## Reset Test Database
## --------------------
resetDatabase: dropDatabase updateDatabase addFixturesDatabase
dropDatabase: ## Drop Database
	$(PHPUSERCONNECT) bin/console doctrine:schema:drop --force
updateDatabase: ## Update test Database
	$(PHPUSERCONNECT) bin/console doctrine:schema:update --force
addFixturesDatabase: ## Add fixtures on Database
	$(PHPUSERCONNECT) bin/console doctrine:fixtures:load --no-interaction

##

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
