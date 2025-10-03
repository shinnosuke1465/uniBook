"use server";

import { getToken } from "@/services/auth/getToken";

export interface DealRoom {
  id: string;
  deal: {
    id: string;
    seller_info: {
      id: string;
      nickname: string;
      profile_image_url: string | null;
    };
    textbook: {
      name: string;
      image_url: string | null;
    };
  };
  created_at: string;
}

interface DealRoomsResponse {
  deal_rooms: DealRoom[];
}

export async function fetchDealRooms(): Promise<DealRoom[]> {
  const token = await getToken();

  if (!token) {
    throw new Error("トークンが存在しません");
  }

  try {
    const res = await fetch(`${process.env.API_BASE_URL}/api/me/dealrooms`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      cache: "no-store",
    });

    if (!res.ok) {
      const errorData = await res.json().catch(() => ({}));
      console.error("取引ルーム一覧取得APIエラー:", {
        status: res.status,
        statusText: res.statusText,
        errorData,
      });
      throw new Error(`取引ルーム一覧の取得に失敗しました: ${res.status}`);
    }

    const data: DealRoomsResponse = await res.json();
    return data.deal_rooms;
  } catch (e) {
    console.error("取引ルーム一覧取得エラー:", e);
    throw e;
  }
}
