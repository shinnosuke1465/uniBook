"use server";

import { getToken } from "@/services/auth/getToken";

export interface LikeParams {
  textbookId: string;
}

export const createLike = async (params: LikeParams): Promise<void> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/textbooks/${params.textbookId}/likes`;

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    cache: "no-store",
  });

  if (!res.ok) {
    const errorText = await res.text();
    console.error("いいね作成APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`いいね作成に失敗しました: ${res.status}`);
  }

  console.log("いいね作成成功");
};

export const deleteLike = async (params: LikeParams): Promise<void> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/textbooks/${params.textbookId}/likes`;

  const res = await fetch(endpoint, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    cache: "no-store",
  });

  if (!res.ok) {
    const errorText = await res.text();
    console.error("いいね削除APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`いいね削除に失敗しました: ${res.status}`);
  }

  console.log("いいね削除成功");
};