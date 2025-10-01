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
			throw new Error("購入した教科書の詳細取得に失敗しました");
		}

		return await res.json();
	} catch (e) {
		console.error(e);
		throw new Error("購入した教科書の詳細取得に失敗しました");
	}
}