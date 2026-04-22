.PHONY: qa qa/phpstan

qa:	qa/phpstan qa/phpcs

qa/phpstan:
	./vendor/bin/phpstan analyse --memory-limit=-1

qa/phpcs:
	./vendor/bin/php-cs-fixer check
