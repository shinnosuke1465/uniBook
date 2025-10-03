"use server";

import { getToken } from "@/services/auth/getToken";

export interface UploadImageResponse {
  id: string;
  path: string;
  type: string;
}

export const uploadImage = async (
  formData: FormData
): Promise<UploadImageResponse> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/images`;

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
    },
    body: formData,
    cache: "no-store",
  });

  if (!res.ok) {
    const errorText = await res.text();
    console.error("画像アップロードAPIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`画像アップロードに失敗しました: ${res.status}`);
  }

  const data = await res.json();
  console.log("画像アップロード成功:", data);

  return data;
};