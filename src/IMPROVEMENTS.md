# Анализ проекта и рекомендации по улучшению

## Выполненные изменения

### 1. Индексы для БД (~10k записей)
Добавлена миграция `2026_03_26_100000_add_indexes_for_report_performance.php`:

- **price**: составной индекс `(product_id, price_date)` — ускоряет JOIN и фильтр по дате в отчёте min/max
- **product**: индекс `(category_id)` — ускоряет фильтр по категории
- **report_process**: индекс `(ps_id)` — ускоряет фильтрацию по статусу

### 2. FK report_process → process_status
Миграция `2026_03_26_100001_add_report_process_status_foreign_key.php`: ссылочная целостность для `ps_id`.

### 3. Исправление формата времени
В `ReportProcessViewTransformer`: `H:m:i` заменён на `H:i:s` (минуты вместо месяца).

---

## Рекомендации по улучшению

### Архитектура и абстракции

1. **Вынести логику отчёта в отдельный сервис**
   - Сейчас `MinMaxPriceForLastSevenDaysReportJob` выполняет построение запроса, генерацию CSV и обновление процесса
   - Имеет смысл вынести в `MinMaxPriceReportService` методы `buildReportData()`, `exportToCsv()`, чтобы Job оставался тонким слоем
   - Упрощает тестирование и повторное использование логики

2. **Рефакторинг `AbstractRepository`**
   - Сейчас он зависит от `Request`, что мешает использовать репозиторий в Jobs, Commands и тестах
   - Сделать `Request` опциональным или вынести в отдельный `RequestScopedRepository`; базовый репозиторий — без Request

3. **Использовать DTO / Value Objects**
   - Вместо ассоциативных массивов в трансформере — DTO `ReportProcessViewDto` (typed properties, IDE-friendly)
   - Для строк CSV — DTO строки отчёта

4. **Action-классы вместо «толстых» контроллеров**
   - `DownloadReportFileAction`, `ListReportProcessesAction` — единая точка входа, проще тестировать

### Laravel 13 и современные возможности

5. **Валидация в Command**
   - Использовать `Validator::make()` или `$this->validate()` вместо ручных проверок `is_numeric` и т.д.
   - Можно вынести правила в Form Request для аргументов команды (если Laravel это поддерживает)

6. **Атрибуты моделей**
   - Уже используются `#[Table]` — хорошо
   - Можно рассмотреть `#[WithCasts]` для cast-ов

7. **Очереди**
   - Указать `$tries`, `$timeout`, `$backoff` в Job (например, 3 попытки, backoff для failed)
   - `ShouldBeUnique` — если один отчёт на category_id не должен дублироваться в очереди

8. **Жизненный цикл Job**
   - Использовать `$this->release()` при временных ошибках вместо immediate fail

9. **Events**
   - `ReportCompleted`, `ReportFailed` для логирования и уведомлений без завязки на Job

### Таблицы и БД

10. **Таблица category**
    - У `product` есть `category_id`, но нет FK и таблицы `category`
    - Стоит добавить таблицу и FK, либо документировать, что category_id — внешний идентификатор

11. **Поле category_id в report_process**
    - Отчёт привязан к `category_id`, но в `report_process` его нет — сложно понять, для какой категории отчёт, без анализа Job
    - Добавить `rp_category_id` в `report_process` для истории

12. **Индексы для jobs/cache**
    - Для `jobs` (Laravel Queue) индекс по `queue` ускоряет выборку; при использовании Redis — не нужно

### Безопасность и надёжность

13. **Middleware для /process_control и /download_file**
    - Добавить `auth` (или кастомную проверку) — иначе страница и скачивание доступны всем

14. **Проверка ownership при скачивании**
    - Сейчас проверяется только наличие файла; при авторизации — проверять, что файл принадлежит пользователю/проекту

15. **Очистка старых файлов**
    - Периодическая команда `app:cleanup-old-reports` для удаления CSV старше N дней
    - Либо `Storage::disk` с TTL/политикой очистки

### Код-стайл и качество

16. **Типизация**
    - `ReportProcessRepository::getById()` — возвращать `?ReportProcess` вместо `mixed`
    - Больше `@param`/`@return` для публичных методов

17. **Константы вместо magic strings**
    - В `failed()` Job: `'failed'` → `ProcessStatusEnum::FAILED`

18. **Blade**
    - Для failed-процессов показывать «—» вместо пустой ссылки, когда `rp_file_save_path` пустой

19. **Локализация**
    - Вынести строки (`'Файл не найден'`, `'Записей пока нет'`) в `lang/ru.json`

### Тестирование

20. **Feature-тесты**
    - `MinMaxPriceForLastSevenDaysReportTest` — команда создаёт процесс и ставит Job
    - `MinMaxPriceForLastSevenDaysReportJobTest` — Job генерирует CSV и обновляет статус
    - `FileControllerTest` — stream download, 404 при отсутствии файла

21. **Pest**
    - Проект уже использует Pest — можно описывать сценарии в `describe()->it()` для отчётов и скачивания

---

## Итог

Минимально необходимое уже сделано: индексы, FK, правка формата времени. Дальнейшие шаги по приоритету:

1. Добавить `rp_category_id` в `report_process`
2. Вынести логику отчёта в сервис
3. Добавить auth-слой для контроллеров
4. Добавить DTO и Action-классы
5. Очистка старых отчётов
6. Feature-тесты
