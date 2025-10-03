"use server";

import { getToken } from "@/services/auth/getToken";

export interface ReportReceiptParams {
  textbookId: string;
}

export const reportReceipt = async (
  params: ReportReceiptParams
): Promise<void> => {
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
    const errorText = await res.text();
    console.error("受取報告APIエラー:", {
      status: res.status,
      statusText: res.statusText,
      errorBody: errorText,
    });
    throw new Error(`受取報告に失敗しました: ${res.status}`);
  }

  // 204 No Content は正常なレスポンス
  console.log("受取報告成功");
};
