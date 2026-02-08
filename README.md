# 夜男ナビ (Yoruo Navi) WordPress Theme

Next.js (TypeScript) プロジェクトから移植されたカスタムテーマです。

## 特徴

- **デザイン**: 元のNext.jsプロジェクトのモダンでクリーンなデザインを100%再現。
- **スタック**: WordPress (PHP) + Tailwind CSS + Vanilla JS。
- **データ構造**:
  - カスタム投稿タイプ: `求人 (job)`
  - タクソノミー: `職種 (job_category)`, `エリア (job_area)`, `特徴・タグ (job_tag)`
  - カスタムフィールド: `area`, `city`, `salary`, `employer_name`, `qualifications`, `benefits` など

## インストールと設定

1. WordPress管理画面でテーマ `夜男ナビ` を有効化してください。
2. カスタム投稿タイプ `求人 (job)` が左メニューに表示されます。
3. 求人を追加する際、以下のカスタムフィールドを設定してください（テンプレートで使用しています）:
   - `area`: 東京都、神奈川県など
   - `city`: 新宿区、横浜市など
   - `salary`: 月給30万円〜 など（表示用）
   - `employer_name`: ストア名
   - `qualifications`, `access_info`, `salary_details`, `working_hours`, `holidays`, `benefits`: 各募集要項の詳細

## 技術的な注意点

- **Tailwind CSS**: 開発の簡便性とデザイン再現のため、現在は `functions.php` 内で `https://cdn.tailwindcss.com` を読み込んでいます。本番環境では、Tailwind CLIを使用してコンパイルしたローカルCSSファイルに置き換えることを推奨します。
- **アイコン**: 元のルーシードアイコン（Lucide）に近い Font Awesome 6 を使用しています。
- **JSロジック**: スライダーや簡易的なマップインタラクションは `js/main.js` および `js/japan-map.js` に実装されています。

## 改修のヒント

- **詳細なエリア検索**: `archive-job.php` では現在、都道府県単位の絞り込みを実装しています。市区町村単位の絞り込みが必要な場合は、`job_area` タクソノミーを階層構造（都道府県 > 市区町村）にして、WP_Queryの `tax_query` を拡張してください。
