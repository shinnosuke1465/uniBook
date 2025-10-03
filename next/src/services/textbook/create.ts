"use server";

import { getToken } from "@/services/auth/getToken";
import { revalidatePath } from "next/cache";

export interface CreateTextbookParams {
  name: string;
  price: number;
  description: string;
  image_ids: string[];
  university_id: string;
  faculty_id: string;
  condition_type: "new" | "near_new" | "no_damage" | "slight_damage" | "damage" | "poor_condition";
}

export const createTextbook = async (
  params: CreateTextbookParams
): Promise<void> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/textbooks`;

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      name: params.name,
      price: params.price,
      description: params.description,
      image_ids: params.image_ids,
      university_id: params.university_id,
      faculty_id: params.faculty_id,
      condition_type: params.condition_type,
    }),
    cache: "no-store",
  });

  if (!res.ok) {
    const errorText = await res.text();
    console.error("教科書作成APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`教科書作成に失敗しました: ${res.status}`);
  }

  console.log("教科書作成成功");

  // 教科書一覧のキャッシュを再検証
  revalidatePath("/textbook");
};