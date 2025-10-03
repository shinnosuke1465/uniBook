"use server";

import { revalidatePath } from "next/cache";

export interface CreateUniversityParams {
  name: string;
}

export const createUniversity = async (
  params: CreateUniversityParams
): Promise<void> => {
  const endpoint = `${process.env.API_BASE_URL}/api/universities`;

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      name: params.name,
    }),
    cache: "no-store",
  });

  if (!res.ok) {
    const errorText = await res.text();
    console.error("大学作成APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`大学作成に失敗しました: ${res.status}`);
  }

  console.log("大学作成成功");

  // キャッシュを再検証してデータを更新
  revalidatePath("/register");
};