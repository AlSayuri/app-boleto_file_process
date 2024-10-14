docker-compose = docker compose
	

docker/build:
	@$(docker-compose) build

start:
	@COMMAND="apache2-foreground" $(docker-compose) up --remove-orphans

stop:
	@$(docker-compose) down --volumes

key/generate:
	@docker compose run --rm app php artisan key:generate

test:
	@COMMAND="php artisan test --testsuite=Unit" docker compose up app --remove-orphans

test/create:
	@docker compose run --rm app php artisan make:test BoletoTest --unit

db/start:
	@$(docker-compose) up db -d --remove-orphans

db/drop:
	@$(docker-compose) down db --remove-orphans

db/migrate:
	@docker compose run --rm app php artisan migrate

db/setup: db/start db/migrate

db/reset: db/drop db/start db/migrate
