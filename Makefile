PROJECT_NAME=yii2-test
DOCKER_COMPOSE=docker compose
PHP_CONT=php

up:
	$(DOCKER_COMPOSE) up -d

stop:
	$(DOCKER_COMPOSE) stop

down:
	$(DOCKER_COMPOSE) down -v

logs:
	$(DOCKER_COMPOSE) logs -f

composer-install:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONT) composer install

migrate:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONT) php yii migrate --interactive=0

migrate-down:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONT) php yii migrate/down 1 --interactive=0

fixtures:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONT) php yii fixture/load '*'

serve:
	$(DOCKER_COMPOSE) up -d php

bash:
	$(DOCKER_COMPOSE) run --rm $(PHP_CONT) bash

