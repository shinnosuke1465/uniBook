"use client";

import type { DealRoomDetail, DealEvent } from "../../../../_lib/fetchDealRoomDetail";
import { DealEventList } from "@/app/(pages)/mypage/_components/DealEventList";
import { DealActionSection } from "./action-section";
import { useState } from "react";

interface DealRoomDetailPresentationProps {
  dealRoom: DealRoomDetail;
  currentUserId: string;
}

export function DealRoomDetailPresentation({
  currentUserId,
  dealRoom,
}: DealRoomDetailPresentationProps) {
  const [dealEvents, setDealEvents] = useState<DealEvent[]>(dealRoom.deal.deal_events);

  const handleEventAdded = (newEvent: DealEvent) => {
    setDealEvents([...dealEvents, newEvent]);
  };

  return (
    <div className="space-y-6">
      {/* ヘッダー */}
      <div className="border-b pb-4">
        <h1 className="text-3xl font-bold">取引詳細</h1>
      </div>

      {/* 2カラムレイアウト */}
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {/* 左カラム */}
        <div className="space-y-6">
          {/* 商品情報 */}
          <div className="rounded-lg border border-gray-200 bg-white p-6">
            <h2 className="mb-4 text-xl font-semibold">商品情報</h2>
            <div className="flex items-start space-x-4">
              {/* 商品画像 */}
              <div className="h-32 w-32 flex-shrink-0 overflow-hidden rounded-md bg-gray-100">
                {dealRoom.deal.textbook.image_url ? (
                  <img
                    src={dealRoom.deal.textbook.image_url}
                    alt={dealRoom.deal.textbook.name}
                    className="h-full w-full object-cover"
                  />
                ) : (
                  <div className="flex h-full items-center justify-center text-gray-400">
                    No Image
                  </div>
                )}
              </div>

              {/* 商品詳細 */}
              <div className="flex-1">
                <h3 className="mb-2 text-xl font-bold">
                  {dealRoom.deal.textbook.name}
                </h3>
                <p className="mb-2 text-gray-600">
                  {dealRoom.deal.textbook.description}
                </p>
                <p className="text-2xl font-bold text-blue-600">
                  ¥{dealRoom.deal.textbook.price.toLocaleString()}
                </p>
              </div>
            </div>
          </div>

          {/* 取引相手情報 */}
          <div className="rounded-lg border border-gray-200 bg-white p-4">
            <h3 className="mb-3 text-sm font-semibold text-gray-600">
              取引相手
            </h3>
            <div className="space-y-3">
              {/* 出品者 */}
              <div className="flex items-center space-x-2">
                {dealRoom.deal.seller_info.profile_image_url ? (
                  <img
                    src={dealRoom.deal.seller_info.profile_image_url}
                    alt={dealRoom.deal.seller_info.name}
                    className="h-8 w-8 rounded-full"
                  />
                ) : (
                  <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300">
                    <span className="text-sm">👤</span>
                  </div>
                )}
                <div>
                  <p className="text-xs text-gray-500">出品者</p>
                  <p className="text-sm font-medium">
                    {dealRoom.deal.seller_info.name}
                  </p>
                </div>
              </div>

              {/* 購入者 */}
              <div className="flex items-center space-x-2">
                {dealRoom.deal.buyer_info.profile_image_url ? (
                  <img
                    src={dealRoom.deal.buyer_info.profile_image_url}
                    alt={dealRoom.deal.buyer_info.name}
                    className="h-8 w-8 rounded-full"
                  />
                ) : (
                  <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300">
                    <span className="text-sm">👤</span>
                  </div>
                )}
                <div>
                  <p className="text-xs text-gray-500">購入者</p>
                  <p className="text-sm font-medium">
                    {dealRoom.deal.buyer_info.name}
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* 取引履歴 */}
          <DealEventList events={dealEvents} />
        </div>

        {/* 右カラム - メッセージ */}
        <div className="space-y-6">
          {/* アクションセクション */}
          <div className="rounded-lg border border-gray-200 bg-white p-6">
            <DealActionSection
              dealRoomId={dealRoom.id}
              status={dealRoom.deal.status}
              currentUserId={currentUserId}
              sellerId={dealRoom.deal.seller_info.id}
              buyerId={dealRoom.deal.buyer_info.id}
              textbookId={dealRoom.deal.textbook.id}
              buyerShippingInfo={{
                postal_code: dealRoom.deal.buyer_info.postal_code,
                address: dealRoom.deal.buyer_info.address,
                name: dealRoom.deal.buyer_info.name,
              }}
              onEventAdded={handleEventAdded}
            />
          </div>

          {/* メッセージ */}
          <div className="rounded-lg border border-gray-200 bg-white p-6">
            <h2 className="mb-4 text-xl font-semibold">メッセージ</h2>
            <div className="space-y-4">
              {dealRoom.messages.length === 0 ? (
                <p className="text-center text-gray-500">
                  メッセージはまだありません
                </p>
              ) : (
                dealRoom.messages.map((message) => (
                  <div
                    key={message.id}
                    className="rounded-lg border border-gray-100 bg-gray-50 p-4"
                  >
                    <div className="mb-2 flex items-center space-x-2">
                      {message.user.profile_image_url ? (
                        <img
                          src={message.user.profile_image_url}
                          alt={message.user.name}
                          className="h-8 w-8 rounded-full"
                        />
                      ) : (
                        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300">
                          <span className="text-xs">👤</span>
                        </div>
                      )}
                      <div className="flex-1">
                        <p className="font-semibold">{message.user.name}</p>
                        <p className="text-xs text-gray-500">
                          {new Date(message.created_at).toLocaleString("ja-JP")}
                        </p>
                      </div>
                    </div>
                    <p className="whitespace-pre-wrap text-gray-700">
                      {message.message}
                    </p>
                  </div>
                ))
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
