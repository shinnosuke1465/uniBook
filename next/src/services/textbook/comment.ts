"use server";

import { getToken } from "@/services/auth/getToken";

export interface SendCommentParams {
  textbookId: string;
  text: string;
}

export const sendComment = async (
  params: SendCommentParams
): Promise<void> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/textbooks/${params.textbookId}/comments`;

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      text: params.text,
    }),
    cache: "no-store",
  });

  if (!res.ok) {
    const errorText = await res.text();
    console.error("コメント送信APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`コメント送信に失敗しました: ${res.status}`);
  }

  console.log("コメント送信成功");
};