"use server";

import { getToken } from "@/services/auth/getToken";

export interface SendDealMessageParams {
  dealRoomId: string;
  message: string;
}

export const sendDealMessage = async (
  params: SendDealMessageParams
): Promise<void> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/me/dealrooms/${params.dealRoomId}/messages`;

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      message: params.message,
    }),
    cache: "no-store",
  });

  if (!res.ok) {
    const errorText = await res.text();
    console.error("メッセージ送信APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`メッセージ送信に失敗しました: ${res.status}`);
  }

  console.log("メッセージ送信成功");
};