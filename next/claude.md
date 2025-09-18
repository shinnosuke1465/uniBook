# CLAUDE.md

このファイルは、このリポジトリでコードを扱う際のClaude Code (claude.ai/code) への指針を提供します。

## アーキテクチャ

App Routerを使用したミニマルなNext.jsアプリケーション：

### 技術スタック
- **フレームワーク**: Next.js（最新版）with App Router
- **スタイリング**: Tailwind CSS v4
- **リンティング・フォーマット**: Biome
- **パッケージマネージャー**: pnpm

### プロジェクト構造
- `app/` - Next.js App Routerのページとコンポーネント
- `docs/akfm-knowledge/` - ドキュメントとナレッジベース（Biomeチェック対象外）
- ルートレベルの設定ファイル

### 主要機能
- データ取得最適化のためのDataLoader
- Server-onlyコンポーネントサポート
- スタイリングのためのTailwind CSS
- 日本語サポート（layoutでlang="ja"）

### コードスタイル
- Biome設定による強制：
    - 文字列にダブルクォート
    - 常にセミコロン
    - 末尾カンマ
    - 2スペースインデント
    - オブジェクトキーのソート

## ドキュメント・ナレッジベース

`docs/akfm-knowledge/`ディレクトリには、React・Next.jsに関する包括的なベストプラクティスドキュメントが含まれています。

### 主要ドキュメント構成

#### 1. Next.js基本原理ガイド (`nextjs-basic-principle/`)
Next.js App Routerの包括的なガイド（36章構成）：

**Part 1: データ取得 (11章)**
- **参照タイミング**: データ取得パターンを実装する際
- **主要ファイル**:
    - `part_1_server_components.md`
    - `part_1_colocation.md`
    - `part_1_request_memoization.md`
    - `part_1_concurrent_fetch.md`
    - `part_1_data_loader.md`
    - `part_1_fine_grained_api_design.md`
    - `part_1_interactive_fetch.md`

**Part 2: コンポーネント設計 (5章)**
- **参照タイミング**: コンポーネント設計・リファクタリング時
- **主要ファイル**:
    - `part_2_client_components_usecase.md`
    - `part_2_composition_pattern.md`
    - `part_2_container_presentational_pattern.md`
    - `part_2_container_1st_design.md`

**Part 3: キャッシュ戦略 (6章)**
- **参照タイミング**: パフォーマンス最適化・キャッシュ制御時
- **主要ファイル**:
    - `part_3_static_rendering_full_route_cache.md`
    - `part_3_dynamic_rendering_data_cache.md`
    - `part_3_router_cache.md`
    - `part_3_data_mutation.md`
    - `part_3_dynamicio.md`

**Part 4: レンダリング戦略 (4章)**
- **参照タイミング**: レンダリング最適化・Streaming実装時
- **主要ファイル**:
    - `part_4_pure_server_components.md`
    - `part_4_suspense_and_streaming.md`
    - `part_4_partial_pre_rendering.md`

**Part 5: その他の実践 (4章)**
- **参照タイミング**: 認証・エラーハンドリング実装時
- **主要ファイル**:
    - `part_5_request_ref.md`
    - `part_5_auth.md`
    - `part_5_error_handling.md`

### 参照ガイドライン

**参照タイミング**:
- 実装時には関連するドキュメントを必ず参照する
- ドキュメントを参照したら、「📖{ドキュメント名}を読み込みました」と出力すること

**機能実装時の参照優先順位**:
1. **データ取得実装** → Part 1のドキュメント群を参照
2. **コンポーネント設計** → Part 2のパターンを適用
3. **パフォーマンス最適化** → Part 3のキャッシュ戦略を活用
4. **レンダリング最適化** → Part 4のStreaming・PPR戦略を参照
5. **認証・エラーハンドリング** → Part 5の実践パターンを適用

**重要な設計原則**:
- **Server-First**: Server Componentsを優先し、必要時にClient Componentsを使用
- **データ取得の配置**: データを使用するコンポーネントの近くでデータ取得を実行
- **コンポジション**: 適切なコンポーネント分離とコンポジションパターンの活用
- **プログレッシブ強化**: JavaScript無効時でも機能する設計を心がける
