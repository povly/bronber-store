# ============================================================================
#  bronber-store — Makefile
#  Удобная обёртка над composer / npm / artisan-командами.
#  Базовые скрипты живут в composer.json (scripts) и package.json.
#  Этот файл их лишь проксирует и добавляет часто используемые цели.
#
#  Использование:
#    make              — показать список целей (help)
#    make <target>     — выполнить цель
#    make test FILTER=ClassName  — пример передачи переменной
# ============================================================================

SHELL := bash
.ONESHELL:
.SHELLFLAGS := -eu -o pipefail -c
.DELETE_ON_ERROR:
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

# --- Инструменты ---
PHP      ?= php
COMPOSER ?= composer
NPM      ?= npm
ARTISAN  := $(PHP) artisan

# --- Git / метаданные ---
VERSION    ?= $(shell git describe --tags --always --dirty 2>/dev/null || echo "dev")
COMMIT     ?= $(shell git rev-parse --short HEAD 2>/dev/null || echo "unknown")
BUILD_TIME := $(shell date -u '+%Y-%m-%dT%H:%M:%SZ')

# --- Параметры по умолчанию ---
FILTER ?=  # make test FILTER=TestName — отфильтровать тесты

# Все цели — не файловые (фиктивные)
.PHONY: help setup install dev serve vite build \
        migrate fresh seed \
        test test-unit test-feature \
        pint rector quality \
        tinker routes key clean boost-update

# ============================================================================
.DEFAULT_GOAL := help

##@ Установка и запуск

help: ## Показать этот список команд
	@awk 'BEGIN {FS = ":.*##"; printf "Использование:\n  make \033[36m<target>\033[0m [ПЕРЕМ=знач]\n"} \
		/^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2} \
		/^##@/ {printf "\n\033[1m%s\033[0m\n", substr($$0, 5)}' $(MAKEFILE_LIST)

setup: ## Первичная установка проекта (делегирует в composer setup)
	$(COMPOSER) run setup

install: ## Установить зависимости PHP и JS (composer install + npm install)
	$(COMPOSER) install
	$(NPM) install --ignore-scripts

dev: ## Полная dev-среда: сервер + очередь + Pail + Vite (composer dev)
	$(COMPOSER) run dev

serve: ## Запустить только Laravel-сервер (без очереди и Vite)
	$(ARTISAN) serve

vite: ## Запустить Vite в режиме HMR (npm run dev)
	$(NPM) run dev

build: ## Продакшн-сборка фронтенда (npm run build)
	$(NPM) run build

##@ База данных

migrate: ## Запустить ожидающие миграции
	$(ARTISAN) migrate

fresh: ## Пересоздать БД с сидерами (migrate:fresh --seed). ОПАСНО: удаляет данные
	$(ARTISAN) migrate:fresh --seed

seed: ## Наполнить БД тестовыми данными (db:seed)
	$(ARTISAN) db:seed

##@ Тестирование (Pest)

test: ## Запустить все тесты (FILTER=Name — фильтр по имени)
	$(ARTISAN) test --compact $(if $(FILTER),--filter=$(FILTER))

test-unit: ## Только unit-тесты (FILTER=Name — фильтр)
	$(ARTISAN) test --compact --testsuite=Unit $(if $(FILTER),--filter=$(FILTER))

test-feature: ## Только feature-тесты (FILTER=Name — фильтр)
	$(ARTISAN) test --compact --testsuite=Feature $(if $(FILTER),--filter=$(FILTER))

##@ Качество кода

pint: ## Форматировать PHP через Pint (только изменённые файлы)
	vendor/bin/pint --dirty --format agent

rector: ## Применить миграции кода Rector (vendor/bin/rector process)
	vendor/bin/rector process

quality: pint rector test ## Полный цикл: Pint → Rector → Test (последовательно)

##@ Утилиты Laravel

tinker: ## Интерактивная REPL (php artisan tinker)
	$(ARTISAN) tinker

routes: ## Показать список роутов приложения
	$(ARTISAN) route:list

key: ## Сгенерировать APP_KEY (key:generate)
	$(ARTISAN) key:generate

clean: ## Очистить все кэши Laravel (optimize:clear)
	$(ARTISAN) optimize:clear

boost-update: ## Обновить гайдлайны Laravel Boost
	$(ARTISAN) boost:update
