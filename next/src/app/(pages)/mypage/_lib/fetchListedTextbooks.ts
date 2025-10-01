"use server";

import { getToken } from "@/services/auth/getToken";
import type { ListedTextbook } from "@/types/response/responseType";

export async function fetchListedTextbooks(): Promise<ListedTextbook[]> {
	const token = await getToken();

	if (!token) {
		throw new Error("トークンが存在しません");
	}

	try {
		const res = await fetch(
			`${process.env.API_BASE_URL}/api/me/listed_textbooks`,
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
			throw new Error("出品した教科書の取得に失敗しました");
		}

		const data = await res.json();
		return data.products;
	} catch (e) {
		console.error(e);
		throw new Error("出品した教科書の取得に失敗しました");
	}
}