"use server";

import { getToken } from "@/services/auth/getToken";
import type { DealRoomDetail } from "@/app/(pages)/mypage/_lib/fetchDealRoomDetail";

export interface ReportReceiptParams {
  textbookId: string;
}

export const reportReceipt = async (
  params: ReportReceiptParams
): Promise<DealRoomDetail> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/textbooks/${params.textbookId}/deal/report_receipt`;

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
    console.error("受取報告APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorData,
    });
    throw new Error(
      errorData.message || `受取報告に失敗しました: ${res.status}`
    );
  }

  const data = await res.json();
  return data.deal_room;
};
