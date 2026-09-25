.PHONY: install lint test

install:
	composer install

lint:
	find src -name "*.php" -print0 | xargs -0 -n1 php -l

test:
	vendor/bin/phpunit tests
