<img width="1536" height="500" alt="uniBookImage (1)" src="https://github.com/user-attachments/assets/584bef3e-5fb6-48ed-a8fe-b350c39af2c0" />

<br><br>
ドメイン駆動設計（DDD）を取り入れ、以前作成した教科書販売アプリをベースに機能を追加し、新たに作り直しました。

<br>

**▼ サービス URL**

[https://unibook.click/textbook/](https://unibook.click/textbook/)

<br>

## 以前作成した教科書販売アプリ
アプリの大まかな概要はgithub参照 (半年ほど前に作成)

[https://shinnosuke1465/textbook-sales/](https://github.com/shinnosuke1465/textbook-sales)

<br>

## 作成期間
- **以前のアプリ**：約2ヶ月
- **今回のアプリ**：約1ヶ月半 （設計：8月11日〜8月31日／実装・デプロイ：9月14日〜10月5日）
  - 当初から 10月5日を完成目標日としてスケジュールを逆算し、計画的に進行しました。
  - サマーインターンと時期が重なり、限られた時間の中でタイトなスケジュールと高い課題設定となりましたが、最終的には目標通りすべての実装をやり切ることができました。

<br>

## 新たに作り直した背景と目的
- 現在のインターン先で扱っている以下の技術を、自分のアプリ開発に落とし込んでアウトプットとして形にしたかったため。
  - DDD (ドメイン駆動開発)
  - laravelでapiを作成
- 独学で学んだ以下の内容についても、実践的な形でアウトプットしたいと考えたため。
  - ドメインモデリング
    - インターン先ではドメインモデリングに関わる機会がなかったため、独学で身につけた知識と技術を実践的なプロジェクトで活かしたかった。
  - DB設計・API設計
  - React / Next.js
  - AWS（Terraform を含む）
- サマーインターンで学んだ「WOL（Working Out Loud）」の考え方を実践し、学びや成長の過程を可視化しながら開発を進めたいと考えたため。
- 過去に作成した教科書販売アプリで、拡張性の観点から未着手となっていた機能やタスクが残っており、それらを改めて整理・実装して完成度を高めたいと考えたため。

<br>

## 前回からの変更点
**機能面**

- 取引履歴の閲覧機能（商品の現在の取引状態を確認可能）
- 配送報告・受け取り報告機能
- 商品への「いいね」機能（いいねした商品の一覧を閲覧可能）
- 商品へのコメント機能
- 関連商品表示機能
- 複数画像登録機能

 **技術面・開発手法の変更**

- Next.js の導入 (App Router・サーバーコンポーネント活用)
- バックエンドの API 化
- DDD（ドメイン駆動設計）の採用
- テストコード実装 (バックエンド)
- CI/CD パイプラインの構築
- AWS 環境へのデプロイ (Terraform活用)
- PhpStorm の導入
- wol (Working Out Loud) の導入

<br>

## 作成したアプリ紹介

**検索機能（教科書・大学・カテゴリ・関連商品）**

| 検索 | 大学・学部検索 | カテゴリ選択 |
|------|------------------|------------------|
| ![Image](https://github.com/user-attachments/assets/4ee3c365-e1ef-4ded-a73d-85694c0477c2) | ![Image](https://github.com/user-attachments/assets/39c124ec-c4cd-48c1-aa01-bb657de38c74) | ![Image](https://github.com/user-attachments/assets/02afe0f4-f1ab-4d02-9ecc-64c487c99224) |
| キーワードを入力して、教科書を探すことができます。 | 大学名・学部名から条件を絞り込み、目的の教科書を表示できます。 | カテゴリを選択すると、そのカテゴリに紐づいた教科書を表示できます。 |
<br>

**関連商品機能**
| 関連商品一覧 |
|---------------|
| <img width="333" height="245" alt="スクリーンショット 2025-10-06 16 44 57" src="https://github.com/user-attachments/assets/5d1f48d4-e3b5-40ee-b934-5f87401d36d5" />
| 各教科書詳細ページで、同じ大学の教科書一覧表示 |
<br>

**リアクション機能（いいね・コメント）**

| いいね | コメント |
|--------|----------|
| ![Image](https://github.com/user-attachments/assets/c539e565-1274-48cd-a987-cb71684b6cc3) | ![Image](https://github.com/user-attachments/assets/cd054388-ab29-4773-81f8-13ce12844666) |
| 投稿に「いいね」をつけることができます（※ログインユーザー限定）。 | コメントの閲覧・投稿が可能です（※ログインユーザー限定）。 |
<br>

**購入機能**

| 購入画面 | 購入 |
|--------|----------|
|<img width="1291" height="800" alt="スクリーンショット 2025-10-06 19 54 05" src="https://github.com/user-attachments/assets/39445643-2597-4213-8fd6-43e3b5ce6ba3" /> | ![Image](https://github.com/user-attachments/assets/31325b1e-5eda-422f-8ccd-5aab88165da9) |
| クレカ情報を入力することで購入できます（※ログインユーザー限定）。 | 教科書の詳細ページから購入処理へ進めます。購入後は取引チャット機能を通じて出品者とやり取りが可能です。 |
<br>

**取引機能（購入後のやりとり）**

| メッセージ | 取引履歴 | 配送・受け取り報告 |
|------------|----------|------------------|
| ![Image](https://github.com/user-attachments/assets/89092b1f-12a9-4ff7-aa6f-7e11dd6d47b2) |<img width="2036" alt="スクリーンショット 2025-10-06 17 20 49" src="https://github.com/user-attachments/assets/0fbc43cd-277b-44c9-9912-ae240ca95a67" /> | ![Image](https://github.com/user-attachments/assets/3b0134c1-b63b-4c12-a227-87eb37a8e5f1) |
| 取引ルーム内で出品者と購入者が直接やり取りできます。 | これまでに行ったすべての取引履歴を一覧で確認できます。 | 配送完了や受け取り完了の報告により、取引ステータスを最新の状態に更新できます。双方の報告が完了すると取引が完了状態になります。 |
<br>

**ユーザー機能 (会員登録・ログイン・マイページ)**

| 会員登録 | ログイン | マイページ |
|------------|----------|------------------|
| ![Image](https://github.com/user-attachments/assets/71594cff-2a7a-47d6-8b52-f97d26547cce) | ![Image](https://github.com/user-attachments/assets/14c5b3b0-01a6-4221-b5e3-5b625d0d5f34) | ![Image](https://github.com/user-attachments/assets/8e618594-53f0-424b-aa82-9c6c1197cd51) |
| ユーザー情報を入力してアカウントを新規作成できます。登録後は出品や購入などすべての機能が利用可能になります。 | 登録済みのメールアドレスとパスワードでログインし、各種機能にアクセスできます。 | プロフィール、出品した商品、購入した商品、いいねした商品、取引一覧など、自分に関する情報をまとめて確認・管理できます。 |

<br><br>

## sudoモデリング
**システム関連図**

<img width="827" height="367" alt="スクリーンショット 2025-10-06 21 08 40" src="https://github.com/user-attachments/assets/01af516f-14a1-4b93-9dbc-a8ddd8b17c7e" />

<br><br>

**ユースケース図**

https://lucid.app/lucidchart/891bd186-289f-4ce7-89e8-565201fac3b1/edit?view_items=aGPKdstoPjD0&invitationId=inv_8bbfa3c7-9c50-4332-ad96-bba571b9b395

<img width="427" height="600" alt="ユースケース図 (1) (2)" src="https://github.com/user-attachments/assets/4796c09f-1ece-4561-9216-94da0724f3aa" />

<br><br>

**画面ごとのユーザーストーリ (sudoモデリングとは関係ないが実践。)**

https://docs.google.com/spreadsheets/d/1bwGpMHaAQnwNA1lDeR2nOouwf93TGsFCP8hUzIkH5n8/edit?usp=sharing

<img width="568" height="1199" alt="画面ごとのユーザーストーリ - シート1 (1)-1 (1)" src="https://github.com/user-attachments/assets/175d9f07-f671-448d-8b6b-99e8f4a8e533" />

<br><br>

**ドメインモデル図** 

https://lucid.app/lucidchart/3493b2e4-5c0d-4404-b02d-f61cb4068765/edit?viewport_loc=-2365%2C-622%2C4501%2C2197%2C0_0&invitationId=inv_abb1f54c-e4bf-4209-a8c6-05babb3c0c98

<img width="2048" height="946" alt="ドメインモデル図 (1)" src="https://github.com/user-attachments/assets/35ee4910-76d3-41d7-a8a4-2d4f4babb555" />

<br><br>

**ER図**

https://lucid.app/lucidchart/3ea1488c-bf0b-4b79-a36b-01122a27e9cf/edit?viewport_loc=-1268%2C-1082%2C4768%2C2680%2C0_0&invitationId=inv_bc505b65-f2be-479a-b06a-31d7e47197f7

<img width="2048" height="1430" alt="Database ER diagram (crow's foot) (1)" src="https://github.com/user-attachments/assets/e03e922a-5a91-450c-ae90-c70188c0743e" />

<br><br>

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

## システム構成図
### local
<img width="1500" height="600" alt="kouseizu1" src="https://github.com/user-attachments/assets/868f929d-4e3a-40d5-b0e7-73848147522a" />

<br>

### 本番環境
<img width="1890" height="732" alt="kouseizu2" src="https://github.com/user-attachments/assets/2d4a759e-0c54-4e8b-bca1-94b6dff66ce4" />

<br><br>


