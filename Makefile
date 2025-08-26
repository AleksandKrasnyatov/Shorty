.DEFAULT_GOAL := help
MAKEFLAGS += --silent

# Цвета для вывода
RED    = \033[0;31m
GREEN  = \033[0;32m
YELLOW = \033[0;33m
BLUE   = \033[0;36m
NC     = \033[0m

# Контейнеры
SERVICE_CONTAINER = link-service
TEST_CONTAINER    = link-service
COMPOSE_FILE      = docker-compose.yml

# Команды
YII      = php yii
COMPOSER = composer
CODECEPT = vendor/bin/codecept
PHPSTAN  = vendor/bin/phpstan
PHPCS    = vendor/bin/phpcs
PHPCBF   = vendor/bin/phpcbf
PSALM    = vendor/bin/psalm

# Помощь
.PHONY: help
help: ## Показать эту справку
	@echo ""
	@echo "🛠️  Shorty — сервис коротких ссылок"
	@echo ""
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / { \
		printf "  ${BLUE}%-25s${NC} ${YELLOW}%s${NC}\n", $$1, $$2 \
	}' $(MAKEFILE_LIST)
	@echo ""

# Управление окружением
.PHONY: up
up: ## Запустить всё (в фоне)
	docker-compose -f $(COMPOSE_FILE) up -d

.PHONY: down
down: ## Остановить всё
	docker-compose -f $(COMPOSE_FILE) down

.PHONY: build
build: ## Пересобрать контейнеры
	docker-compose -f $(COMPOSE_FILE) build

.PHONY: logs
logs: ## Показать логи
	docker-compose -f $(COMPOSE_FILE) logs -f

# Yii2 команды
.PHONY: yii
yii: ## Запустить Yii2 команду: make yii migrate
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(YII) $(filter-out $@,$(MAKECMDGOALS))

%:
	@:

.PHONY: migrate
migrate: ## Применить миграции
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(YII) migrate --interactive=0

.PHONY: migrate-down
migrate-down: ## Откатить N миграций (по умолчанию 1)
	@COUNT=$(or $(filter-out $@,$(MAKECMDGOALS)),1); \
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(YII) migrate/down $$COUNT --interactive=0

.PHONY: migrate-new
migrate-new: ## Создать миграцию: make migrate-new create_links_table
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(YII) migrate/create $(filter-out $@,$(MAKECMDGOALS))

# Composer
.PHONY: composer
composer: ## Запустить Composer: make composer install
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(COMPOSER) $(filter-out $@,$(MAKECMDGOALS))

.PHONY: codecept
test: ## Запустить Codeception: make test functional
	docker-compose -f $(COMPOSE_FILE) exec $(TEST_CONTAINER) $(CODECEPT) run $(filter-out $@,$(MAKECMDGOALS))

.PHONY: build-tester
build-tester: ## Пересобрать FunctionalTester
	docker-compose -f $(COMPOSE_FILE) exec $(TEST_CONTAINER) $(CODECEPT) build

# Качество кода
.PHONY: phpstan
phpstan: ## Запустить PHPStan
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(PHPSTAN) analyse $(filter-out $@,$(MAKECMDGOALS))

.PHONY: phpcs
phpcs: ## php _CodeSniffer
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(PHPCS) $(filter-out $@,$(MAKECMDGOALS))

.PHONY: phpcbf
phpcbf: ## Исправить стиль кода автоматически
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(PHPCBF) $(filter-out $@,$(MAKECMDGOALS))

.PHONY: psalm
psalm: ## Psalm
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(PSALM) $(filter-out $@,$(MAKECMDGOALS))

.PHONY: psalm-fix
psalm-fix: ## Psalm auto fix
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) $(PSALM) --alter --issues=InvalidReturnType --dry-run

# Shell
.PHONY: shell
shell: ## Зайти в контейнер
	docker-compose -f $(COMPOSE_FILE) exec $(SERVICE_CONTAINER) sh

.PHONY: db-shell
db-shell: ## Подключиться к PostgreSQL
	docker-compose -f $(COMPOSE_FILE) exec postgres psql -U shorty -d shorty