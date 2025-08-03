<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src') // 設定要檢查的目錄，這裡假設您的程式碼在 src 目錄下
    ->exclude('vendor');   // 排除不需要檢查的目錄，例如 vendor

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true, // 遵循 PSR-12 標準
        'array_syntax' => ['syntax' => 'short'], // 使用簡短的陣列語法 []
        'ordered_imports' => ['sort_algorithm' => 'alpha'], // 排序 use 語句
        'no_unused_imports' => true, // 移除未使用的 use 語句
        'declare_strict_types' => true, // 在檔案頂部新增 declare(strict_types=1);
    ])
    ->setFinder($finder);

