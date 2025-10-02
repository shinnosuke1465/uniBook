"use server";

import { getToken } from "@/services/auth/getToken";
import type { PurchasedTextbook } from "@/types/response/responseType";

export async function fetchPurchasedTextbookDetail(
	textbookId: string,
): Promise<PurchasedTextbook> {
	const token = await getToken();

	if (!token) {
		throw new Error("トークンが存在しません");
	}

	try {
		const res = await fetch(
			`${process.env.API_BASE_URL}/api/me/purchased_textbooks/${textbookId}`,
			{
				method: "GET",
				headers: {
					"Content-Type": "application/json",
					Authorization: `Bearer ${token}`,
				},
				cache: "no-store",
			},
		);

		if (!res.ok) {
			const errorData = await res.json().catch(() => ({}));
			console.error("購入した教科書詳細取得APIエラー:", {
				status: res.status,
				statusText: res.statusText,
				errorData,
			});
			throw new Error(`購入した教科書の詳細取得に失敗しました: ${res.status}`);
		}

		return await res.json();
	} catch (e) {
		console.error("購入した教科書詳細取得エラー:", e);
		throw e;
	}
}