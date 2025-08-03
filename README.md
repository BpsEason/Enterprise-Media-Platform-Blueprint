# Enterprise Media Platform Blueprint

這是一個基於 Laravel、Clean Architecture 和 DDD 的企業級媒體平台藍圖，整合了多種技術棧以支援高可用性和可觀測性。

## 架構概覽
專案遵循「Clean Architecture」，將程式碼分為：
- **`app/Domain`**：核心業務邏輯與領域實體（如 `Article`）。
- **`app/Application`**：應用服務層，協調領域物件與基礎設施。
- **`app/Infrastructure`**：基礎設施層，處理資料庫、外部服務等細節。
- **`app/Http`**：展示層，負責 API 請求與回應。

## 系統要求
- Docker
- Docker Compose
- kubectl (若需部署至 Kubernetes)
- Google Cloud SDK (若使用 BigQuery)

## 啟動方式
1.  **複製專案**：`git clone <repo-url>`
2.  **設定環境變數**：複製 `.env.example` 到 `.env` 並修改。
3.  **啟動服務**：`docker-compose up -d`
4.  **安裝依賴**：`docker-compose exec php-fpm composer install`
5.  **產生金鑰**：`docker-compose exec php-fpm php artisan key:generate`
6.  **執行遷移與填充**：`docker-compose exec php-fpm php artisan migrate --seed`

## 服務初始化
部分服務（如 RabbitMQ 和 Elasticsearch）需要手動初始化。

1.  **RabbitMQ 佇列**：
    - 進入 PHP-FPM 容器：`docker-compose exec php-fpm bash`
    - 執行 Artisan 命令：`php artisan queue:setup`
    - 這將建立 `article_events` 佇列。

2.  **Elasticsearch 索引**：
    - 進入 PHP-FPM 容器：`docker-compose exec php-fpm bash`
    - 執行 Artisan 命令：`php artisan elasticsearch:setup`
    - 這將建立 `articles` 索引並設定 `mappings`。

3.  **BigQuery 資料集與表格**：
    - 參考 `docs/data-fabric.md` 建立資料集與表格。

## Kubernetes
本藍圖提供了一套完整的 Kubernetes 部署配置。

**1. 建立 Secret**
- **將你的 BigQuery 服務帳號檔案編碼**：
  \`cat path/to/your-service-account.json | base64\`
- **建立 Kubernetes Secret**：
  \`kubectl create secret generic emp-secrets --from-literal=POSTGRES_PASSWORD='your-db-password' --from-literal=BQ_SERVICE_ACCOUNT_JSON='<your-base64-encoded-json>'\`
- **注意**：在 `BigQueryArticleRepository` 中，服務帳號會直接從這個 Secret 中解碼。

**2. 部署服務**
\`kubectl apply -f k8s/\`

## 文件與圖表
- `docs/data-fabric.md`: 說明如何設定 BigQuery 資料流。
- `docs/mermaid-diagrams.mmd`: 包含架構圖，可使用 GitHub Pages 渲染。

## 中間件設計意圖 (`app/Http/Kernel.php`)
我們將 Web 和 API 路由的中間件群組明確分離。
- `web` 群組包含 CSRF 保護、Session 等，適用於傳統網頁應用。
- `api` 群組則使用 `sanctum` 提供的 `throttle:api` 和 `auth:sanctum`，為 API 端點提供認證與限流。

