"use server";

import { getToken } from "@/services/auth/getToken";

export interface ReportDeliveryParams {
  textbookId: string;
}

export const reportDelivery = async (
  params: ReportDeliveryParams
): Promise<void> => {
  const token = await getToken();

  if (!token) {
    throw new Error("認証が必要です");
  }

  const endpoint = `${process.env.API_BASE_URL}/api/textbooks/${params.textbookId}/deal/report_delivery`;

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
    console.error("配送報告APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`配送報告に失敗しました: ${res.status}`);
  }

  // 204 No Content は正常なレスポンス
  console.log("配送報告成功");
};
