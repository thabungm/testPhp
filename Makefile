.PHONY: install lint test serve

install:
	composer install

lint:
	find src -name "*.php" -print0 | xargs -0 -n1 php -l

test:
	vendor/bin/phpunit tests

serve:
	php -S localhost:8000 -t public
