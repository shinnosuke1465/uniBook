"use server";

import { getToken } from "@/services/auth/getToken";

export interface PaymentIntentResponse {
  payment_intent_id?: string;
  client_secret: string;
}

export const createPaymentIntent = async (
  textbookId: string
): Promise<PaymentIntentResponse> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/textbooks/${textbookId}/deal/payment_intent`;

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    cache: "no-store",
  });

  if (!res.ok) {
    const errorData = await res.json().catch(() => ({}));
    throw new Error(
      errorData.message || `PaymentIntent作成に失敗しました: ${res.status}`
    );
  }

  const body = await res.json();

  console.log("PaymentIntent API Response:", body);

  if (!body.client_secret) {
    throw new Error("client_secretが見つかりません");
  }

  return {
    client_secret: body.client_secret,
  };
};