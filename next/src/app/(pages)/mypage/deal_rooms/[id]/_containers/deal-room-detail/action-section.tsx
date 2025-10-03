"use client";

import { reportDelivery } from "@/services/deal/reportDelivery";
import { reportReceipt } from "@/services/deal/reportReceipt";
import { useState, type FormEvent } from "react";
import { toast } from "react-hot-toast";
import type { DealEvent } from "../../../../_lib/fetchDealRoomDetail";

interface DealActionSectionProps {
  dealRoomId: string;
  status: string;
  currentUserId: string;
  sellerId: string;
  buyerId: string;
  textbookId: string;
  buyerShippingInfo: {
    postal_code: string;
    address: string;
    name: string;
  };
  onEventAdded: (event: DealEvent) => void;
}

export function DealActionSection({
  buyerShippingInfo,
  buyerId,
  currentUserId,
  dealRoomId,
  onEventAdded,
  sellerId,
  status: initialStatus,
  textbookId,
}: DealActionSectionProps) {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [currentStatus, setCurrentStatus] = useState(initialStatus);

  const isSeller = currentUserId === sellerId;
  const isBuyer = currentUserId === buyerId;

  const statusLabels: Record<string, string> = {
    Listing: "出品中",
    Purchased: "購入済み",
    Shipping: "配送中",
    Completed: "完了",
  };

  const onSubmitReportDelivery = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsSubmitting(true);

    try {
      await reportDelivery({ textbookId });
      setCurrentStatus("Shipping");

      // 新しいイベントを追加
      const newEvent: DealEvent = {
        id: crypto.randomUUID(),
        actor_type: "seller",
        event_type: "ReportDelivery",
        created_at: new Date().toISOString(),
      };
      onEventAdded(newEvent);

      toast.success("商品の発送報告をしました。");
    } catch (error) {
      toast.error("商品の発送報告に失敗しました。");
      console.error("配送報告エラー:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  const onSubmitReportReceipt = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsSubmitting(true);

    try {
      await reportReceipt({ textbookId });
      setCurrentStatus("Completed");

      // 新しいイベントを追加
      const newEvent: DealEvent = {
        id: crypto.randomUUID(),
        actor_type: "buyer",
        event_type: "ReportReceipt",
        created_at: new Date().toISOString(),
      };
      onEventAdded(newEvent);

      toast.success("商品の受取報告をしました。");
    } catch (error) {
      toast.error("商品の受取報告に失敗しました。");
      console.error("受取報告エラー:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="space-y-4">
      {/* ステータス表示 */}
      <div className="rounded-lg bg-gray-50 p-4">
        <p className="text-sm text-gray-600">
          ステータス:{" "}
          <span className="font-semibold">
            {statusLabels[currentStatus] || currentStatus}
          </span>
        </p>
      </div>

      {/* 出品者向けアクション */}
      {isSeller && currentStatus === "Purchased" && (
        <div>
          <p>購入者から決済がありました。</p>
          <p className="text-red-500">
            以下の住所に商品を発送してください。
          </p>
          <div className="mb-5 mt-4 rounded-lg bg-slate-100/80 p-4 font-bold">
            〒{buyerShippingInfo.postal_code}
            <br />
            {buyerShippingInfo.address}
            <br />
            {buyerShippingInfo.name}
          </div>
          <form onSubmit={onSubmitReportDelivery}>
            <button
              type="submit"
              disabled={isSubmitting}
              className="w-full rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700 disabled:bg-gray-400"
            >
              {isSubmitting ? "送信中..." : "配送報告"}
            </button>
          </form>
        </div>
      )}

      {isSeller && currentStatus === "Shipping" && (
        <div>
          <p className="text-red-500">
            購入者に商品発送報告をしました。購入者からの商品受取報告をお待ちください。
          </p>
          <div className="mt-4 rounded-lg bg-slate-100/80 p-4 font-bold">
            〒{buyerShippingInfo.postal_code}
            <br />
            {buyerShippingInfo.address}
            <br />
            {buyerShippingInfo.name}
          </div>
        </div>
      )}

      {isSeller && currentStatus === "Completed" && (
        <div>
          購入者からの受取報告があり、取引完了しました。ありがとうございました。
        </div>
      )}

      {/* 購入者向けアクション */}
      {isBuyer && currentStatus === "Purchased" && (
        <div>決済完了しました。出品者が配送準備中です。</div>
      )}

      {isBuyer && currentStatus === "Shipping" && (
        <div>
          <div className="mb-5">
            出品者が配送を行いました。商品が届いたら受取報告をしてください。
          </div>
          <form onSubmit={onSubmitReportReceipt}>
            <div className="block pt-2">
              <button
                type="submit"
                disabled={isSubmitting}
                className="w-full rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700 disabled:bg-gray-400"
              >
                {isSubmitting ? "送信中..." : "受取報告"}
              </button>
            </div>
          </form>
        </div>
      )}

      {isBuyer && currentStatus === "Completed" && (
        <div>取引完了しました。ありがとうございました。</div>
      )}
    </div>
  );
}
