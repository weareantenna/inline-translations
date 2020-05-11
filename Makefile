.DEFAULT_GOAL := help

## Display all available commands
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [command]\n\n"
	printf "${COLOR_COMMENT}Available commands:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Run linting and static analysis (phpcs + phpstan)
lint: phpcs phpstan

phpcs:
	printf "\n${COLOR_INFO}Running phpcs${COLOR_RESET}\n\n"
	vendor/bin/phpcbf -p

phpstan:
	printf "\n${COLOR_INFO}Running phpstan${COLOR_RESET}\n\n"
	vendor/bin/phpstan analyze src --memory-limit=2G

phpunit:
	vendor/bin/phpunit
