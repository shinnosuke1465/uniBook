<img width="1536" height="300" alt="ChatGPT Image 2025年10月6日 14_47_53 (1)" src="https://github.com/user-attachments/assets/ded9659a-54f4-47bb-aed6-04d18e442d97" />

<br>

ドメイン駆動設計（DDD）を取り入れ、以前作成した教科書販売アプリをベースに機能を追加し、新たに作り直しました。

<br>

**▼ サービス URL**

[https://unibook.click/textbook/](https://unibook.click/textbook/)

<br>

## 以前作成した教科書販売アプリ
アプリの大まかな概要はgithub参照 (半年ほど前に作成)

[https://unibook.click/textbook/](https://github.com/shinnosuke1465/textbook-sales)

<br>

## 前回からの変更点
**機能面**

- 取引履歴の閲覧（商品の現在の取引状態を確認可能）
- 配送報告機・能受け取り報告
- 商品への「いいね」機能（いいねした商品の一覧を閲覧可能）
- 商品へのコメント機能
- 関連商品の表示機能
- 複数画像登録

 **技術面・開発手法の変更**

- Next.js の導入（ベストプラクティスに沿った構成）
- バックエンドの API 化
- DDD（ドメイン駆動設計）の採用
- テストコード実装 (バックエンドのみ)
- CI CD
- AWS でデプロイ (Terraform使用)
- PhpStorm 使用
- wol (Working Out Loud) 実践

<br>

## 作成したアプリ紹介

### 検索機能（教科書・大学・カテゴリ・関連商品）

| 検索 | 大学・学部検索 | カテゴリ選択 |
|------|------------------|------------------|
| ![検索](https://i.gyazo.com/search-example.png) | ![大学・学部検索](https://i.gyazo.com/university-example.png) | ![カテゴリ選択](https://i.gyazo.com/filter-example.png) |
| キーワードを入力して、教科書を探すことができます。 | 大学名・学部名から条件を絞り込み、目的の教科書を表示できます。 | カテゴリを選択すると、そのカテゴリに紐づいた教科書を表示できます。 |
<br>

## 関連商品機能

| 関連商品一覧 |
|---------------|
| ![関連商品](https://i.gyazo.com/related-example.png) |
| 各教科書の詳細ページで、同じ大学の教科書一覧を表示します。 |
<br>

### リアクション機能（いいね・コメント）

| いいね | コメント |
|--------|----------|
| ![いいね](https://i.gyazo.com/like-example.png) | ![コメント](https://i.gyazo.com/comment-example.png) |
| 投稿に「いいね」をつけることができます（※ログインユーザー限定）。 | コメントの閲覧・投稿が可能です（※ログインユーザー限定）。 |
<br>

### 購入機能

| 購入 |
|------|
| ![購入](https://i.gyazo.com/purchase-example.png) |
| 教科書の詳細ページから購入処理へ進めます。購入後は取引チャット機能を通じて出品者とやり取りが可能です。 |
<br>

### 取引機能（購入後のやりとり）

| メッセージ | 取引履歴 | 配送・受け取り報告 |
|------------|----------|------------------|
| ![メッセージ](https://i.gyazo.com/message-example.png) | ![取引履歴](https://i.gyazo.com/history-example.png) | ![配送・受け取り報告](https://i.gyazo.com/delivery-example.png) |
| 取引ルーム内で出品者と購入者が直接やり取りできます。 | これまでに行ったすべての取引履歴を一覧で確認できます。 | 配送完了や受け取り完了の報告により、取引ステータスを最新の状態に更新できます。双方の報告が完了すると取引が完了状態になります。 |
<br>

### ユーザー機能 (会員登録・ログイン・マイページ)

| 会員登録 | ログイン | マイページ |
|------------|----------|------------------|
| ![会員登録](https://i.gyazo.com/register-example.png) | ![ログイン](https://i.gyazo.com/login-example.png) | ![マイページ](https://i.gyazo.com/mypage-example.png) |
| ユーザー情報を入力してアカウントを新規作成できます。登録後は出品や購入などすべての機能が利用可能になります。 | 登録済みのメールアドレスとパスワードでログインし、各種機能にアクセスできます。 | プロフィール、出品した商品、購入した商品、いいねした商品、取引一覧など、自分に関する情報をまとめて確認・管理できます。 |

<br>

## 使用技術

**バックエンド**

- PHP: 8.4
- Laravel: 12.x
- php-fpm: 8.4
- MySQL: 8.0

**フロントエンド**

- Next.js: 15.5.2
- Node.js: 24.
- React: 19.

**インフラ**

- Docker Desktop: 4.23.0
- nginx: 1.28
- Terraform: 1.12.2
- Terraform AWS Provider: 5.100.0
- AWS
  - ECS
  - Fargate
  - ECR
  - VPC
  - ALB
  - Route53
  - ACM
  - RDS
  - S3
  - IAM


**CI/CD**

- GitHub Actions

**外部サービス**
- Stripe

<br>

### システム構成図
**local**


