# WordPress移行・機能実装計画書 (Next.js to WordPress)

## 1. 目的

「yoru-otoko-navi」(Next.js版) のコア機能を「yoruo-navi」(WordPress版) に移植し、店舗登録、求人、ユーザー応募の一連の流れをWordPress上で完結させる。

### 主な機能範囲

1.  **店舗登録・承認フロー**: 店舗がアカウントを作成し、管理者が承認する。
2.  **店舗用求人管理**: ログインした店舗が、自分の求人を投稿・編集・管理する。
3.  **ユーザー応募フロー**: 求職者がアカウントを作成し、求人に対して応募を行う。

---

## 2. アーキテクチャとデータ構造

### 2.1 ユーザーとロール (権限)

WordPressの標準ユーザー機能に加え、カスタムロールを導入する。

| ユーザータイプ | Next.jsでの役割 | WordPressロール名 | 説明                         |
| :------------- | :-------------- | :---------------- | :--------------------------- |
| **店舗**       | `employer`      | `store_owner`     | 自身の店舗情報と求人を管理   |
| **求職者**     | `jobseeker`     | `job_seeker`      | 求人を検索し、応募履歴を管理 |
| **管理者**     | `admin`         | `administrator`   | サイト全体の管理、店舗の承認 |

### 2.2 カスタム投稿タイプ (CPT)

すでに実装済みの `job` に加え、応募情報を管理する `application` を追加する。

| エンティティ | 投稿タイプ名  | 現状   | メタデータ / タクソノミー                                          |
| :----------- | :------------ | :----- | :----------------------------------------------------------------- |
| **求人**     | `job`         | **済** | `job_category`, `job_area`, `_salary_min`, `_salary_max` 等        |
| **応募**     | `application` | 未     | `_job_id`, `_applicant_id`, `_status`, `_message`, `_contact_info` |

---

## 3. 実装フェーズ

### フェーズ 1: コアセットアップ (ロールと投稿タイプ)

**ファイル:** `inc/core-setup.php`

- カスタムロール `store_owner` と `job_seeker` の登録。
- 権限(Capabilities)の設定: `store_owner` は求人の投稿・編集権限を持つ。
- `application` 投稿タイプの登録 (管理画面のみ、またはマイページ用)。

### フェーズ 2: ユーザー登録・ログイン機能

**ファイル:** `page-templates/page-auth.php` (または個別のlogin/signup)

- **新規登録フォーム**:
  - メール、パスワード、表示名、役割選択（店舗 または 求職者）。
  - 店舗選択時: `_account_status` を `pending` (保留) に設定。
- **ログインフォーム**:
  - 役割に応じたリダイレクト (店舗 -> ダッシュボード、求職者 -> ホーム/マイページ)。

### フェーズ 3: 店舗専用ワークフロー

**ファイル:** `page-templates/page-store-dashboard.php`, `page-templates/page-job-post.php`

1.  **店舗情報登録 (Onboarding)**
    - 初回ログイン時に店舗名、業態、エリア、連絡先を登録。
    - `update_user_meta` を使用して保存。
2.  **店舗ダッシュボード**
    - 自分が投稿した求人の一覧を表示 (公開・下書き・審査中)。
3.  **求人投稿・編集フォーム**
    - Next.js版のフィールドを完全移植:
      - タイトル、職種、雇用形態、エリア
      - 給与(タイプ、最小、最大)
      - 詳細(資格、アクセス、待遇、保険、時間、休日、PR)
      - 画像アップロード
    - 保存時、店舗が未承認の場合は `pending` ステータスで保存。

### フェーズ 4: 管理者向け承認インターフェース

**ファイル:** `inc/admin-customizations.php`

- ユーザー一覧に「アカウントステータス」列を追加。
- 管理者がワンクリックで店舗を承認 (`approved`) できる機能。

### フェーズ 5: 求人応募フロー

**ファイル:** `single-job.php` (更新), `inc/application-handler.php`

1.  **求人詳細ページ**:
    - 「この求人に応募する」ボタン。
    - 未ログイン時はログイン/登録へ誘導。
2.  **応募フォーム (モーダル搭載)**:
    - 名前、連絡手段 (LINE/電話/メール)、連絡先、開始可能日、メッセージ。
3.  **応募処理**:
    - `application` 投稿を作成。
    - 店舗オーナーにメール通知。

---

## 4. 詳細フィールド定義 (参考: yoru-otoko-navi)

### 店舗情報 (User Meta)

- `_store_name` (店舗名)
- `_store_business_type` (業態)
- `_store_area_pref` (都道府県)
- `_store_area_city` (市区町村)
- `_store_contact_email` (連絡用メール)
- `_store_contact_phone` (電話番号)
- `_account_status` (`pending`, `approved`, `rejected`)

### 応募情報 (Application Meta)

- `_job_id` (対象求人ID)
- `_applicant_id` (応募ユーザーID)
- `_contact_type` (`line`, `phone`, `email`)
- `_contact_value` (IDや番号)
- `_start_date` (勤務開始日)
- `_message` (メッセージ)

---

## 5. 次のステップ

1.  **Phase 1 の実行**: `inc/core-setup.php` を作成し、ロールと `application` 投稿タイプを定義する。
2.  **Phase 2 の実行**: ログイン・新規登録ページを作成する。
3.  **デザインの適用**: Next.js版のUIを踏襲した、プレミアムな店舗管理画面を実装する。

この計画に基づき、順次実装を進めます。修正や追加の要望があればお知らせください。
