"use server";

import { getToken } from "@/services/auth/getToken";
import type { LikedTextbook } from "@/types/response/responseType";

export async function fetchLikedTextbooks(): Promise<LikedTextbook[]> {
	const token = await getToken();

	if (!token) {
		throw new Error("トークンが存在しません");
	}

	try {
		const res = await fetch(
			`${process.env.API_BASE_URL}/api/me/likes`,
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
			throw new Error("いいねした教科書の取得に失敗しました");
		}

		const data = await res.json();
		return data.textbooks;
	} catch (e) {
		console.error(e);
		throw new Error("いいねした教科書の取得に失敗しました");
	}
}