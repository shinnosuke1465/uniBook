"use server";

import { revalidatePath } from "next/cache";

export interface CreateFacultyParams {
  name: string;
  university_id: string;
}

export const createFaculty = async (
  params: CreateFacultyParams
): Promise<void> => {
  const endpoint = `${process.env.API_BASE_URL}/api/faculties`;

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      name: params.name,
      university_id: params.university_id,
    }),
    cache: "no-store",
  });

  if (!res.ok) {
    const errorText = await res.text();
    console.error("学部作成APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`学部作成に失敗しました: ${res.status}`);
  }

  console.log("学部作成成功");

  // キャッシュを再検証してデータを更新
  revalidatePath("/register");
};