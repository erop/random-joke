build:
	composer install

test:
	./bin/phpunit

run:
	symfony serve -d  && ./bin/console messenger:consume -vv
