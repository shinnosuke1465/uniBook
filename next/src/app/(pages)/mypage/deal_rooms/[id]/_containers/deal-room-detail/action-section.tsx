"use client";

import { reportDelivery } from "@/services/deal/reportDelivery";
import { reportReceipt } from "@/services/deal/reportReceipt";
import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";
import { toast } from "react-hot-toast";

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
}

export function DealActionSection({
  buyerShippingInfo,
  buyerId,
  currentUserId,
  dealRoomId,
  sellerId,
  status: initialStatus,
  textbookId,
}: DealActionSectionProps) {
  const router = useRouter();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [currentStatus, setCurrentStatus] = useState(initialStatus);

  const isSeller = currentUserId === sellerId;
  const isBuyer = currentUserId === buyerId;

  const onSubmitReportDelivery = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsSubmitting(true);

    try {
      const updatedDealRoom = await reportDelivery({ textbookId });
      toast.success("商品の発送報告をしました。");
      setCurrentStatus(updatedDealRoom.deal.status);
      // 状態更新後に少し待ってからリフレッシュ
      setTimeout(() => {
        router.refresh();
      }, 100);
    } catch (error) {
      toast.error("商品の発送報告に失敗しました。");
    } finally {
      setIsSubmitting(false);
    }
  };

  const onSubmitReportReceipt = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsSubmitting(true);

    try {
      const updatedDealRoom = await reportReceipt({ textbookId });
      toast.success("商品の受取報告をしました。");
      setCurrentStatus(updatedDealRoom.deal.status);
      // 状態更新後に少し待ってからリフレッシュ
      setTimeout(() => {
        router.refresh();
      }, 100);
    } catch (error) {
      toast.error("商品の受取報告に失敗しました。");
    } finally {
      setIsSubmitting(false);
    }
  };

  if (isSeller) {
    switch (currentStatus) {
      case "Purchased":
        return (
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
        );
      case "Shipping":
        return (
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
        );
      case "Completed":
        return (
          <div>
            購入者からの受取報告があり、取引完了しました。ありがとうございました。
          </div>
        );
      default:
        return null;
    }
  }

  if (isBuyer) {
    switch (currentStatus) {
      case "Purchased":
        return <div>決済完了しました。出品者が配送準備中です。</div>;
      case "Shipping":
        return (
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
        );
      case "Completed":
        return <div>取引完了しました。ありがとうございました。</div>;
      default:
        return null;
    }
  }

  return null;
}
