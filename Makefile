##
## Symfony Server
## --------------
##
start: ## Start symfony's server
	symfony server:start -d

stop: ## Stop symfony's server
	symfony server:stop

##
## PHP Unit
## --------------
##
test: ## Run the tests
	vendor/bin/phpunit

coverage: ## Test Coverage
	vendor/bin/phpunit --coverage-html web/test-coverage

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
