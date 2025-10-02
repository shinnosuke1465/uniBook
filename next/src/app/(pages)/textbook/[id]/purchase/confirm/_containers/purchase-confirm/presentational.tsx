"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { verifyPaymentIntent } from "@/services/deal/verifyPaymentIntent";

interface PurchaseConfirmPresentationProps {
  textbookId: string;
  paymentIntentId: string;
}

export function PurchaseConfirmPresentation({
  textbookId,
  paymentIntentId,
}: PurchaseConfirmPresentationProps) {
  const router = useRouter();
  const [isFailed, setIsFailed] = useState<boolean>(false);

  useEffect(() => {
    const confirmPayment = async () => {
      try {
        console.log("決済確認開始:", { textbookId, paymentIntentId });

        await verifyPaymentIntent({
          textbookId,
          paymentIntentId,
        });

        console.log("決済確認完了");

        // データベースの更新を待機してからリダイレクト
        await new Promise(resolve => setTimeout(resolve, 2000));

        console.log("リダイレクト実行");

        // 決済成功後、購入した商品詳細ページへリダイレクト
        const redirectPath = `/mypage/purchased_textbooks/${textbookId}`;
        router.push(redirectPath);
      } catch (error) {
        console.error("決済確認エラー:", error);
        setIsFailed(true);
      }
    };
    confirmPayment();
  }, [textbookId, paymentIntentId, router]);

  if (isFailed) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <div className="font-bold text-red-500">
          決済処理に失敗しました。お問い合わせください。
        </div>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-16 text-center">
      <div>決済処理中...</div>
    </div>
  );
}
