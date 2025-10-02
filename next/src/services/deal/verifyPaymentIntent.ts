"use server";

import { getToken } from "@/services/auth/getToken";

export interface VerifyPaymentIntentParams {
  textbookId: string;
  paymentIntentId: string;
}

export const verifyPaymentIntent = async (
  params: VerifyPaymentIntentParams
): Promise<void> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/textbooks/${params.textbookId}/deal/payment_intent/verify`;


  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      payment_intent_id: params.paymentIntentId,
    }),
    cache: "no-store",
  });

  if (!res.ok) {
    const errorData = await res.json().catch(() => ({}));
    console.error("決済確認APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorData,
    });
    throw new Error(
      errorData.message || `決済確認に失敗しました: ${res.status}`
    );
  }

  console.log("決済確認成功");
};
