"use server";

import { getToken } from "@/services/auth/getToken";

export interface DealEvent {
  id: string;
  actor_type: string;
  event_type: string;
  created_at: string;
}

export interface Message {
  id: number;
  message: string;
  created_at: string;
  user: {
    id: string;
    name: string;
    profile_image_url: string | null;
  };
}

export interface DealRoomDetail {
  id: string;
  deal: {
    id: string;
    status: string;
    textbook: {
      id: string;
      name: string;
      description: string;
      price: number;
      image_url: string | null;
      image_urls: string[];
    };
    seller_info: {
      id: string;
      name: string;
      profile_image_url: string | null;
    };
    buyer_info: {
      id: string;
      name: string;
      postal_code: string;
      address: string;
      profile_image_url: string | null;
    };
    deal_events: DealEvent[];
  };
  messages: Message[];
}

interface DealRoomDetailResponse {
  deal_room: DealRoomDetail;
}

export async function fetchDealRoomDetail(
  dealRoomId: string
): Promise<DealRoomDetail> {
  const token = await getToken();

  if (!token) {
    throw new Error("トークンが存在しません");
  }

  try {
    const res = await fetch(
      `${process.env.API_BASE_URL}/api/me/dealrooms/${dealRoomId}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        cache: "no-store",
      }
    );

    if (!res.ok) {
      const errorData = await res.json().catch(() => ({}));
      console.error("取引ルーム詳細取得APIエラー:", {
        status: res.status,
        statusText: res.statusText,
        errorData,
      });
      throw new Error(`取引ルーム詳細の取得に失敗しました: ${res.status}`);
    }

    const data: DealRoomDetailResponse = await res.json();
    return data.deal_room;
  } catch (e) {
    console.error("取引ルーム詳細取得エラー:", e);
    throw e;
  }
}
