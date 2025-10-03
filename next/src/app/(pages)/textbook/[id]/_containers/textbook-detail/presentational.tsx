"use client";

import { useState } from "react";
import type { Textbook, Comment } from "@/app/types/textbook";
import { useAuthContext } from "@/contexts/AuthContext";
import { sendComment } from "@/services/textbook/comment";
import { createLike, deleteLike } from "@/services/textbook/like";
import Link from "next/link";

interface TextbookDetailPresentationProps {
  textbook: Textbook;
  children?: React.ReactNode;
}

export function TextbookDetailPresentation({
  textbook,
  children,
}: TextbookDetailPresentationProps) {
  const [showPayment, setShowPayment] = useState(false);
  const [showCommentForm, setShowCommentForm] = useState(false);
  const [comments, setComments] = useState<Comment[]>(textbook.comments);
  const [commentInput, setCommentInput] = useState("");
  const [isSending, setIsSending] = useState(false);
  const [isLiked, setIsLiked] = useState(textbook.is_liked);
  const [isLikeProcessing, setIsLikeProcessing] = useState(false);
  const { authUser } = useAuthContext();

  const conditionLabels = {
    new: "新品",
    like_new: "ほぼ新品",
    good: "良い",
    fair: "可",
    poor: "難あり",
  };

  // 自分が出品した商品かどうか
  const isOwnProduct = authUser?.id === textbook.deal?.seller_info.id;
  // 購入可能かどうか
  const canPurchase = textbook.deal?.is_purchasable && !isOwnProduct;

  const handleSendComment = async () => {
    if (!commentInput.trim() || isSending) return;

    setIsSending(true);

    // Optimistic UI update
    const optimisticComment: Comment = {
      id: `temp-${Date.now()}`,
      text: commentInput,
      created_at: new Date().toISOString(),
      user: {
        id: authUser?.id || "",
        name: authUser?.name || "あなた",
        profile_image_url: authUser?.profile_image_url || null,
      },
    };

    setComments([...comments, optimisticComment]);
    setCommentInput("");
    setShowCommentForm(false);

    try {
      await sendComment({
        textbookId: textbook.id,
        text: commentInput,
      });
    } catch (error) {
      console.error("コメント送信エラー:", error);
      // エラー時は楽観的更新を元に戻す
      setComments(comments);
      setCommentInput(optimisticComment.text);
      setShowCommentForm(true);
      alert("コメントの送信に失敗しました");
    } finally {
      setIsSending(false);
    }
  };

  const handleToggleLike = async () => {
    if (isLikeProcessing) return;

    setIsLikeProcessing(true);

    // Optimistic UI update
    const previousLikeState = isLiked;
    setIsLiked(!isLiked);

    try {
      if (previousLikeState) {
        // いいね済み → いいね削除
        await deleteLike({ textbookId: textbook.id });
      } else {
        // 未いいね → いいね作成
        await createLike({ textbookId: textbook.id });
      }
    } catch (error) {
      console.error("いいね処理エラー:", error);
      // エラー時は楽観的更新を元に戻す
      setIsLiked(previousLikeState);
      alert("いいね処理に失敗しました");
    } finally {
      setIsLikeProcessing(false);
    }
  };

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
        {/* 画像セクション */}
        <div className="space-y-4">
          <div className="aspect-square overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
            {textbook.image_urls.length > 0 ? (
              <img
                src={textbook.image_urls[0]}
                alt={textbook.name}
                className="h-full w-full object-cover"
              />
            ) : (
              <div className="flex h-full items-center justify-center text-gray-400">
                <span className="text-2xl">No Image</span>
              </div>
            )}
          </div>
          {/* サムネイル画像（複数画像がある場合） */}
          {textbook.image_urls.length > 1 && (
            <div className="grid grid-cols-4 gap-2">
              {textbook.image_urls.map((imageUrl, index) => (
                <div
                  key={index}
                  className="aspect-square overflow-hidden rounded border border-gray-200 bg-gray-100"
                >
                  <img
                    src={imageUrl}
                    alt={`${textbook.name} - ${index + 1}`}
                    className="h-full w-full object-cover"
                  />
                </div>
              ))}
            </div>
          )}
        </div>

        {/* 詳細情報セクション */}
        <div className="space-y-6">
          {/* タイトルと価格 */}
          <div>
            <h1 className="mb-2 text-3xl font-bold">{textbook.name}</h1>
            <div className="flex items-center space-x-4">
              <span className="text-4xl font-bold text-blue-600">
                ¥{textbook.price.toLocaleString()}
              </span>
              <span
                className={`rounded-full px-4 py-2 text-sm font-medium ${
                  textbook.condition_type === "new"
                    ? "bg-green-100 text-green-800"
                    : textbook.condition_type === "like_new"
                      ? "bg-blue-100 text-blue-800"
                      : textbook.condition_type === "good"
                        ? "bg-yellow-100 text-yellow-800"
                        : "bg-gray-100 text-gray-800"
                }`}
              >
                {conditionLabels[textbook.condition_type]}
              </span>
            </div>
          </div>

          {/* 大学・学部情報 */}
          <div className="rounded-lg bg-gray-50 p-4">
            <h2 className="mb-2 text-sm font-semibold text-gray-600">
              出品者の所属
            </h2>
            <p className="text-lg">{textbook.university_name}</p>
            <p className="text-gray-600">{textbook.faculty_name}</p>
          </div>

          {/* 説明文 */}
          <div>
            <h2 className="mb-2 text-lg font-semibold">商品説明</h2>
            <p className="whitespace-pre-wrap text-gray-700">
              {textbook.description}
            </p>
          </div>

          {/* 出品者情報 */}
          {textbook.deal && (
            <div className="rounded-lg border border-blue-200 bg-blue-50 p-4">
              <h2 className="mb-3 text-sm font-semibold text-gray-600">
                出品者情報
              </h2>
              <div className="flex items-center space-x-3">
                {textbook.deal.seller_info.profile_image_url ? (
                  <img
                    src={textbook.deal.seller_info.profile_image_url}
                    alt={textbook.deal.seller_info.nickname}
                    className="h-12 w-12 rounded-full"
                  />
                ) : (
                  <div className="flex h-12 w-12 items-center justify-center rounded-full bg-gray-300">
                    <span className="text-lg">👤</span>
                  </div>
                )}
                <div>
                  <p className="font-semibold">{textbook.deal.seller_info.nickname}</p>
                  {!textbook.deal.is_purchasable && (
                    <span className="text-xs text-red-600">現在取引中</span>
                  )}
                </div>
              </div>
            </div>
          )}

          {/* アクションボタン */}
          <div className="space-y-3">
            {canPurchase && (
              <button
                onClick={() => setShowPayment(true)}
                className="w-full rounded-lg bg-blue-600 px-6 py-3 text-lg font-semibold text-white transition hover:bg-blue-700"
              >
                購入する
              </button>
            )}
            {isOwnProduct && (
              <button
                disabled
                className="w-full cursor-not-allowed rounded-lg bg-gray-300 px-6 py-3 text-lg font-semibold text-gray-500"
              >
                自分の商品です
              </button>
            )}
            {textbook.deal && !textbook.deal.is_purchasable && !isOwnProduct && (
              <button
                disabled
                className="w-full cursor-not-allowed rounded-lg bg-gray-300 px-6 py-3 text-lg font-semibold text-gray-500"
              >
                現在取引中です
              </button>
            )}
            {!textbook.deal && (
              <div className="text-center text-gray-500">
                商品情報を読み込んでいます...
              </div>
            )}
            {textbook.deal && (
              <button
                onClick={() => setShowCommentForm(!showCommentForm)}
                className="w-full rounded-lg border-2 border-gray-300 px-6 py-3 text-lg font-semibold text-gray-700 transition hover:bg-gray-50"
              >
                {showCommentForm ? "キャンセル" : "コメントする"}
              </button>
            )}
          </div>

          {/* いいね・コメント数 */}
          <div className="flex items-center space-x-6 border-t pt-4">
            <button
              onClick={handleToggleLike}
              disabled={isLikeProcessing}
              className="flex items-center space-x-2 text-gray-600 transition hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50"
            >
              <span className="text-2xl">
                {isLiked ? "❤️" : "🤍"}
              </span>
              <span>いいね</span>
            </button>
            <div className="flex items-center space-x-2 text-gray-600">
              <span className="text-2xl">💬</span>
              <span>{comments.length} コメント</span>
            </div>
          </div>
        </div>
      </div>

      {/* コメント入力フォーム */}
      {showCommentForm && (
        <div className="mt-12">
          <h2 className="mb-4 text-2xl font-bold">コメント入力</h2>
          <div className="rounded-lg border border-gray-200 bg-white p-6">
            <textarea
              value={commentInput}
              onChange={(e) => setCommentInput(e.target.value)}
              placeholder="コメントを入力..."
              className="w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
              rows={4}
              disabled={isSending}
            />
            <div className="mt-3 flex justify-end">
              <button
                onClick={handleSendComment}
                disabled={!commentInput.trim() || isSending}
                className="rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300"
              >
                {isSending ? "送信中..." : "送信"}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* コメントセクション */}
      {comments.length > 0 && (
        <div className="mt-12">
          <h2 className="mb-6 text-2xl font-bold">コメント</h2>
          <div className="space-y-4">
            {comments.map((comment) => (
              <div
                key={comment.id}
                className="rounded-lg border border-gray-200 bg-white p-4"
              >
                <div className="mb-2 flex items-center justify-between">
                  <div className="flex items-center space-x-2">
                    {comment.user.profile_image_url ? (
                      <img
                        src={comment.user.profile_image_url}
                        alt={comment.user.name}
                        className="h-8 w-8 rounded-full"
                      />
                    ) : (
                      <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300">
                        <span className="text-xs">👤</span>
                      </div>
                    )}
                    <span className="font-semibold">{comment.user.name}</span>
                  </div>
                  <span className="text-sm text-gray-500">
                    {new Date(comment.created_at).toLocaleDateString("ja-JP")}
                  </span>
                </div>
                <p className="text-gray-700">{comment.text}</p>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* 支払い画面セクション */}
      {showPayment && (
        <div className="mt-12">
          <h2 className="mb-6 text-2xl font-bold">支払い情報入力</h2>
          {children}
        </div>
      )}
    </div>
  );
}