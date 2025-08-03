# Enterprise Media Platform Blueprint

這是一個基於 **Laravel**、**Domain-Driven Design (DDD)**、**Clean Architecture** 與 **DevOps** 最佳實踐的企業級媒體平台藍圖。本專案提供核心程式碼與架構設計，協助您快速建構一個可擴展、高可用性與高可觀測性的後端系統。

## 專案亮點

1.  **全面的架構設計**：

      - 採用 **DDD** 與 **Clean Architecture**，實現清晰的層次分離（Domain、Application、Infrastructure）。
      - 支援多儲存後端（PostgreSQL、Elasticsearch、BigQuery），可透過 `.env` 配置動態切換。
      - 抽象化的 `IArticleRepository` 介面，確保儲存層解耦，易於擴展。

2.  **容器化與 DevOps 整合**：

      - **Docker** 與 **Docker Compose** 提供本地開發環境，包含 Nginx、PHP-FPM、Redis、RabbitMQ、Elasticsearch。
      - **Kubernetes** 配置（`k8s/`）支援雲原生部署，包含 HPA（水平自動擴展）與資源限制，符合 FinOps 原則。
      - **GitHub Actions CI/CD** 自動執行 Lint（PHPStan）、測試（PHPUnit）與 Docker 映像構建與推送。

3.  **可觀測性與監控**：

      - 整合 **Sentry** 進行錯誤追蹤與性能監控，於 `bootstrap/app.php` 中配置自動捕捉例外。
      - 提供 `/metrics` 端點，輸出 Prometheus 格式的監控數據。
      - 日誌與事件追蹤透過 RabbitMQ 實現非同步處理。

4.  **API 中心化與安全性**：

      - RESTful API 設計，支援 CRUD、搜尋與 AI 查詢（`/articles/ask-ai`），使用 **Laravel Sanctum** 進行認證。
      - Nginx 作為輕量級 API Gateway，支援高併發與安全配置（如 X-Frame-Options、CORS）。
      - OpenAPI 3.0 規範文件（`docs/openapi.yaml`）提供標準化的 API 文檔。

5.  **自動化與可維護性**：

      - 提供 Artisan 命令（`queue:setup`、`elasticsearch:setup`）自動配置 RabbitMQ 佇列與 Elasticsearch 索引。
      - 包含 **Mermaid 架構圖**（`docs/mermaid-diagrams.mmd`）與詳細文檔（`docs/data-fabric.md`），便於團隊協作與維護。

6.  **測試與品質保證**：

      - 完整的 PHPUnit 測試套件，涵蓋 API 端點與 Sanctum 認證邏輯。
      - 靜態分析（PHPStan）與程式碼格式化（PHP-CS-Fixer）確保程式碼質量。
      - 工廠模式（Factories）與資料填充（Seeders）簡化測試資料生成。

-----

## 目錄結構

```plaintext
Enterprise-Media-Platform-Blueprint/
├── app/
│   ├── Application/              # 應用層：業務邏輯協調
│   ├── Domain/                   # 領域層：核心業務邏輯與實體
│   ├── Infrastructure/           # 基礎設施層：資料庫、外部服務實現
│   ├── Http/                     # 展示層：控制器與中間件
│   ├── ValueObjects/             # 值物件：封裝不可變數據
│   └── Console/Commands/         # 自訂 Artisan 命令
├── docs/                         # 文件：架構圖、OpenAPI、資料織網
├── docker/                       # Docker 配置：Nginx、PHP-FPM
├── k8s/                          # Kubernetes 部署配置
├── .github/workflows/            # CI/CD 流程
├── .env.example                  # 環境變數範例
├── composer.json                 # 依賴管理
├── phpstan.neon                  # 靜態分析配置
├── .php-cs-fixer.dist.php        # 程式碼格式化配置
└── README.md
```

-----

## 快速上手

本倉庫僅提供關鍵程式碼檔案與配置。請遵循以下步驟，將其整合到您的 Laravel 專案中。

1.  **建立新的 Laravel 專案**

    ```bash
    composer create-project laravel/laravel my-project
    cd my-project
    ```

2.  **複製核心檔案**
    將本倉庫中的 `app/`、`config/`、`database/`、`routes/` 等核心目錄複製到您新建立的專案根目錄。

3.  **安裝依賴與設定**

    ```bash
    # 安裝額外依賴
    composer require google/cloud-bigquery php-amqplib sentry/sentry-laravel

    # 複製 .env 範例並修改
    cp .env.example .env

    # 編輯 .env 檔案，填入 DB、Redis、Elasticsearch、RabbitMQ、BigQuery、Sentry 等參數
    ```

4.  **啟動服務**

    ```bash
    docker-compose up -d
    ```

5.  **初始化專案**

    ```bash
    # 進入 php-fpm 容器
    docker-compose exec php-fpm bash

    # 產生 APP_KEY
    php artisan key:generate

    # 執行資料庫遷移與填充
    php artisan migrate --seed

    # 執行自訂 Artisan 命令以設定服務
    php artisan queue:setup        # 初始化 RabbitMQ 佇列
    php artisan elasticsearch:setup # 初始化 Elasticsearch 索引

    # 離開容器
    exit
    ```

    現在，您可以透過 `http://localhost:8080` 訪問專案。

6.  **執行測試與品質檢查**

    ```bash
    # 進入 php-fpm 容器
    docker-compose exec php-fpm bash

    # 靜態分析
    ./vendor/bin/phpstan analyse

    # 程式碼格式化檢查
    ./vendor/bin/php-cs-fixer fix --dry-run --diff

    # 執行單元與功能測試
    ./vendor/bin/phpunit
    ```

7.  **部署至 Kubernetes**

    ```bash
    kubectl apply -f k8s/
    ```

-----

## 核心設計理念問答

### Q：為何此專案採用 Domain-Driven Design (DDD) 與 Clean Architecture？

**A：** 採用 DDD 是為了將業務領域的複雜性與程式碼結構緊密結合，使系統更易於理解與維護。而 Clean Architecture 則確保系統的核心業務邏輯獨立於外部框架（如 Laravel）與基礎設施（如資料庫）。這使得專案的業務核心不受技術變更的影響，同時提高了可測試性與靈活性。

### Q：專案中的 `IArticleRepository` 介面有什麼作用？

**A：** `IArticleRepository` 是一個抽象的介面，定義了對文章資料的操作方法（如 `save`、`findById`）。透過在應用層依賴這個介面而非具體的實現（例如 `PostgresArticleRepository` 或 `BigQueryArticleRepository`），我們實現了儲存層的解耦。這意味著您可以輕鬆切換不同的資料庫後端，而無需修改核心業務邏輯，提供了極佳的擴展性。

### Q：專案如何應對高併發情境？

**A：** 本專案透過多種機制來應對高併發：

  - **API Gateway**：使用 Nginx 作為輕量級 API Gateway 處理大量請求。
  - **異步處理**：透過 RabbitMQ 佇列實現事件驅動架構，將耗時的任務（例如日誌記錄、外部通知）從同步請求中分離，提升 API 響應速度。
  - **水平擴展**：提供 Kubernetes HPA (Horizontal Pod Autoscaling) 配置，使應用服務能夠根據負載自動擴展，確保高可用性。

### Q：為什麼選擇使用 RabbitMQ 進行非同步處理？

**A：** RabbitMQ 作為一個成熟的訊息佇列系統，可以有效地解耦服務之間的依賴，並實現非同步的事件處理。例如，當一篇文章被創建時，可以發送一個 `ArticleCreated` 事件到佇列，其他服務（如搜尋索引服務或通知服務）可以訂閱這個事件進行後續處理，而不會阻塞原始的 API 請求。

-----

## 關鍵代碼展示（含中文註解）

以下選取專案中的核心檔案，展示其設計意圖並加入中文註解，突顯 DDD、Clean Architecture 與可觀測性的實現。

### 1\. 領域實體：`src/app/Domain/Article.php`

```php
<?php

namespace App\Domain;

use App\ValueObjects\ArticleId;
use App\ValueObjects\ArticleStatus;
use App\Exceptions\ArticleStatusException;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class Article implements Arrayable
{
    public ArticleId $id; // 文章唯一標識
    public string $title; // 文章標題
    public string $content; // 文章內容
    public ArticleStatus $status; // 文章狀態（草稿/已發布）
    public Carbon $createdAt; // 建立時間
    public Carbon $updatedAt; // 更新時間

    // 私有建構函數，確保物件通過工廠方法創建
    private function __construct(
        ArticleId $id,
        string $title,
        string $content,
        ArticleStatus $status,
        Carbon $createdAt,
        Carbon $updatedAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // 工廠方法：創建新文章，預設為草稿狀態
    public static function create(string $title, string $content): self
    {
        return new self(
            new ArticleId(null), // ID 由 Repository 生成
            $title,
            $content,
            ArticleStatus::draft(),
            Carbon::now(),
            Carbon::now()
        );
    }

    // 發布文章，包含狀態檢查與更新時間
    public function publish(): void
    {
        if ($this->status->isPublished()) {
            throw new ArticleStatusException("文章已發布，無法重複發布。");
        }
        $this->status = ArticleStatus::published();
        $this->updatedAt = Carbon::now();
    }

    // 將物件轉為陣列，便於序列化
    public function toArray(): array
    {
        return [
            'id' => $this->id->toInt(),
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status->toString(),
            'created_at' => $this->createdAt->toIso8601String(),
            'updated_at' => $this->updatedAt->toIso8601String(),
        ];
    }
}
```

**亮點**：

  - 遵循 DDD 的 **Entity** 設計，封裝文章的核心屬性與行為（如 `publish`）。
  - 使用 **值物件**（`ArticleId`、`ArticleStatus`）確保數據不變性與型別安全。
  - 提供 `toArray` 方法，支援 JSON 序列化，與 API 層無縫整合。

-----

### 2\. 應用服務：`src/app/Application/ArticleService.php`

```php
<?php

namespace App\Application;

use App\Domain\Article;
use App\Domain\Events\ArticleCreated;
use App\Domain\IArticleRepository;
use App\ValueObjects\ArticleId;
use App\ValueObjects\CreateArticleDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class ArticleService implements IArticleService
{
    private IArticleRepository $articleRepository; // 抽象 Repository 介面，解耦儲存層

    public function __construct(IArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    // 獲取所有文章（分頁）
    public function getAllArticles(): Collection
    {
        return $this->articleRepository->findAll();
    }

    // 根據 ID 獲取單篇文章
    public function getArticleById(string $id): ?Article
    {
        return $this->articleRepository->findById(new ArticleId($id));
    }

    // 創建新文章，觸發領域事件
    public function createArticle(CreateArticleDto $dto): Article
    {
        // 創建領域物件
        $article = Article::create($dto->title, $dto->content);
        // 持久化到儲存層
        $this->articleRepository->save($article);
        // 觸發文章創建事件，供後續非同步處理
        Event::dispatch(new ArticleCreated($article));
        return $article;
    }

    // 搜尋文章（標題或內容）
    public function searchArticles(string $query): Collection
    {
        return $this->articleRepository->searchByTitleOrContent($query);
    }
}
```

**亮點**：

  - 實現 **Clean Architecture** 的應用層，作為控制器與領域層的橋樑。
  - 依賴抽象的 `IArticleRepository`，支援多種儲存後端（Postgres、Elasticsearch、BigQuery）。
  - 透過 `Event::dispatch` 觸發領域事件，實現事件驅動架構，利於非同步處理。

-----

### 3\. BigQuery 儲存實現：`src/app/Infrastructure/Persistence/BigQueryArticleRepository.php`

```php
<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Article;
use App\Domain\IArticleRepository;
use App\ValueObjects\ArticleId;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as BasePaginator;
use Illuminate\Support\Collection;

class BigQueryArticleRepository implements IArticleRepository
{
    private BigQueryClient $bigQuery; // BigQuery 客戶端

    // 初始化 BigQuery 客戶端，使用環境變數配置
    public function __construct()
    {
        $this->bigQuery = new BigQueryClient([
            'projectId' => env('BQ_PROJECT_ID'),
            'keyFile' => json_decode(file_get_contents(env('BQ_SERVICE_ACCOUNT_FILE')), true)
        ]);
    }

    // 獲取所有文章（分頁）
    public function findAll(): LengthAwarePaginator
    {
        $query = 'SELECT * FROM `' . env('BQ_DATASET_ID') . '.articles`';
        return $this->runPaginatedQuery($query);
    }

    // 根據 ID 查詢單篇文章
    public function findById(ArticleId $id): ?Article
    {
        $query = 'SELECT * FROM `' . env('BQ_DATASET_ID') . '.articles` WHERE id = @articleId';
        $queryJobConfig = $this->bigQuery->query($query)
            ->parameters(['articleId' => $id->toString()]);
        $rows = $this->bigQuery->runQuery($queryJobConfig);

        foreach ($rows as $row) {
            return $this->toDomain($row);
        }

        return null;
    }

    // 保存文章到 BigQuery
    public function save(Article $article): void
    {
        $table = $this->bigQuery->dataset(env('BQ_DATASET_ID'))->table('articles');
        $table->insertRows([
            [
                'insertId' => $article->id->toString(),
                'data' => $article->toArray()
            ]
        ]);
    }

    // 搜尋文章（標題或內容）
    public function searchByTitleOrContent(string $query): LengthAwarePaginator
    {
        $likeQuery = '%' . $query . '%';
        $sql = "SELECT * FROM `" . env('BQ_DATASET_ID') . ".articles`
                WHERE title LIKE @query OR content LIKE @query";
        
        $queryJobConfig = $this->bigQuery->query($sql)
            ->parameters(['query' => $likeQuery]);

        return $this->runPaginatedQuery($queryJobConfig);
    }

    // 執行分頁查詢
    private function runPaginatedQuery($queryJobConfig): LengthAwarePaginator
    {
        $rows = $this->bigQuery->runQuery($queryJobConfig);
        $items = collect($rows->rows())->map(fn($row) => $this->toDomain($row));
        $total = $this->countTotalResults($queryJobConfig);

        return new BasePaginator($items, $total, 15);
    }

    // 計算總結果數
    private function countTotalResults($queryJobConfig): int
    {
        $query = $queryJobConfig instanceof \Google\Cloud\BigQuery\QueryJobConfiguration ? $queryJobConfig->getQuery() : $queryJobConfig;
        $countQuery = "SELECT count(*) FROM ($query)";
        $countJobConfig = $this->bigQuery->query($countQuery);
        $rows = $this->bigQuery->runQuery($countJobConfig);
        foreach ($rows as $row) {
            return $row['f0_'];
        }
        return 0;
    }

    // 將 BigQuery 資料轉為領域物件
    private function toDomain(array $data): Article
    {
        return Article::fromArray($data);
    }
}
```

**亮點**：

  - 實現 **BigQuery** 作為資料倉儲的儲存層，支援大規模數據分析。
  - 提供分頁查詢與搜尋功能，適應企業級高數據量場景。
  - 使用環境變數（`BQ_PROJECT_ID`、`BQ_SERVICE_ACCOUNT_FILE`）配置，確保安全性與靈活性。

-----

### 4\. API 控制器：`src/app/Http/Controllers/ArticleController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Application\IArticleService;
use App\Domain\Article;
use App\Http\Resources\ArticleResource;
use App\ValueObjects\CreateArticleDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ArticleController extends Controller
{
    private IArticleService $articleService; // 注入應用服務

    public function __construct(IArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    // 獲取所有文章
    public function index(): AnonymousResourceCollection
    {
        $articles = $this->articleService->getAllArticles();
        return ArticleResource::collection($articles);
    }

    // 創建新文章
    public function store(Request $request): JsonResponse
    {
        $dto = new CreateArticleDto($request); // 使用 DTO 驗證與封裝輸入
        $article = $this->articleService->createArticle($dto);
        return response()->json(new ArticleResource($article), 201);
    }

    // 獲取單篇文章
    public function show(string $id): ArticleResource
    {
        $article = $this->articleService->getArticleById($id);
        return new ArticleResource($article);
    }

    // 搜尋文章
    public function search(Request $request): AnonymousResourceCollection
    {
        $articles = $this->articleService->searchArticles($request->get('query'));
        return ArticleResource::collection($articles);
    }

    // 與 AI 服務互動
    public function askAi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
        ]);
        
        $aiServiceUrl = env('AI_SERVICE_URL') . '/query';

        try {
            $response = Http::post($aiServiceUrl, ['prompt' => $validated['prompt']]);
            $response->throw();
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => '無法從 AI 服務獲取回應'], 500);
        }
    }
}
```

**亮點**：

  - 透過 **Laravel Sanctum** 實現安全的 API 認證（`auth:sanctum` 中間件）。
  - 使用 **DTO**（`CreateArticleDto`）進行輸入驗證，確保資料一致性。
  - 與外部 AI 服務整合，提供智慧化查詢功能（`/articles/ask-ai`）。

-----

### 5\. Artisan 命令：`src/app/Console/Commands/SetupQueueCommand.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SetupQueueCommand extends Command
{
    protected $signature = 'queue:setup'; // 命令簽名
    protected $description = '設置 RabbitMQ 佇列';

    // 執行命令
    public function handle()
    {
        $this->info('正在連接到 RabbitMQ...');
        try {
            // 建立 RabbitMQ 連線
            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST'),
                env('RABBITMQ_PORT'),
                env('RABBITMQ_USER'),
                env('RABBITMQ_PASSWORD'),
                env('RABBITMQ_VHOST')
            );
            $channel = $connection->channel();
            // 宣告持久化佇列
            $channel->queue_declare('article_events', false, true, false, false);
            $this->info('佇列 "article_events" 創建成功。');
            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            $this->error('連接到 RabbitMQ 或創建佇列失敗：' . $e->getMessage());
        }
    }
}
```

**亮點**：

  - 提供 Artisan 命令自動化配置 RabbitMQ 佇列，簡化環境初始化。
  - 使用環境變數配置連線參數，確保靈活性與安全性。
  - 支援事件驅動架構，`article_events` 佇列用於非同步處理文章事件。

-----

## 五、進階功能與擴展

1.  **資料織網（Data Fabric）**：

      - 參考 `docs/data-fabric.md`，透過 BigQuery 實現大規模資料分析。
      - 配置範例：

    <!-- end list -->

    ```bash
    bq --location=US mk --dataset enterprise_media_platform_dataset
    bq mk --table enterprise_media_platform_dataset.articles ./schema.json
    ```

2.  **Mermaid 架構圖**：

      - 位於 `docs/mermaid-diagrams.mmd`，展示系統架構與 CI/CD 流程。
      - 可透過 Mermaid 在 GitHub Pages 渲染為互動式圖表。

3.  **OpenAPI 文檔**：

      - `docs/openapi.yaml` 定義了 API 端點（如 `/health`、`/login`），便於前端與第三方整合。

4.  **Kubernetes 部署**：

      - `k8s/` 包含完整的部署配置，支援 Postgres、Redis、Elasticsearch、RabbitMQ 與應用服務。
      - 使用 Secret 管理敏感數據（如 BigQuery 服務帳號）。

-----

## 六、問題與限制

  - **BigQuery 更新限制**：目前 `BigQueryArticleRepository` 的 `save` 方法僅支援插入，更新需透過暫存表或外部 ETL 流程實現。
  - **事件監聽器**：`ArticleCreatedEvent` 的監聽器尚未實現，需根據業務需求自行擴展。
  - **AI 服務依賴**：`/articles/ask-ai` 端點依賴外部 AI 服務，需確保 `AI_SERVICE_URL` 配置正確。

-----

## 七、未來改進方向

1.  實現 **CQRS**（Command Query Responsibility Segregation），進一步分離讀寫操作。
2.  增加 **GraphQL** 端點，支援更靈活的查詢需求。
3.  擴展 **Sentry** 配置，加入自訂追蹤與性能監控標籤。
4.  支援 **多語言**（i18n）與 **全文搜尋**（Elasticsearch 增強）。
5.  整合 **ArgoCD** 或 **Flux**，實現 GitOps 部署流程。

-----

## 八、聯繫與貢獻

  - **問題回報**：請提交至 GitHub Issues，遵循 `ISSUE_TEMPLATE`。
  - **功能請求**：提交 PR 並參考 `PULL_REQUEST_TEMPLATE`。
  - **文檔發布**：Mermaid 圖表與 API 文檔可透過 GitHub Actions 自動發布至 GitHub Pages。

-----

**版權聲明**：本專案採用 MIT 許可，歡迎複製、修改與分享。
