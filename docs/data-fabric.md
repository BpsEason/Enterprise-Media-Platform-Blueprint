# 資料織網 (Data Fabric) 設置指南

本專案利用 BigQuery 作為企業級資料倉儲，以下是設定步驟。

## 1. 建立 BigQuery 資料集

使用 `bq` 命令列工具建立一個資料集。
\`\`\`bash
# 確保你已安裝並認證 Google Cloud SDK
bq --location=US mk --dataset enterprise_media_platform_dataset
\`\`\`

## 2. 建立 `articles` 表格

在資料集中建立一個 `articles` 表格，用於儲存文章資料。

\`\`\`json
# schema.json
[
  {"name": "id", "type": "INTEGER", "mode": "REQUIRED"},
  {"name": "title", "type": "STRING", "mode": "REQUIRED"},
  {"name": "content", "type": "STRING", "mode": "REQUIRED"},
  {"name": "status", "type": "STRING", "mode": "REQUIRED"},
  {"name": "created_at", "type": "TIMESTAMP", "mode": "REQUIRED"},
  {"name": "updated_at", "type": "TIMESTAMP", "mode": "REQUIRED"}
]
\`\`\`

然後執行以下命令建立表格：
\`\`\`bash
bq mk --table enterprise_media_platform_dataset.articles ./schema.json
\`\`\`

## 3. 服務帳號權限

確保你的服務帳號（通常是 Kubernetes Secret 中使用的那個）具有以下權限：
- `BigQuery Data Editor` 或 `BigQuery Data Owner`：允許向表格中寫入資料。

## 4. 程式碼整合

- 在 `.env` 中設定 `BQ_DATASET_ID` 和 `BQ_SERVICE_ACCOUNT_FILE`。
- `BigQueryArticleRepository.php` 已經實現了 BigQuery 的資料存取邏輯。
- `ArticleService.php` 在建立文章時，會觸發事件，由 `BigQueryEventListener`（需自行實現）將資料同步到 BigQuery。
