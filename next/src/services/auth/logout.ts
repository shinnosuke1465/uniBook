"use server";

import { cookies } from "next/headers";

// /api/logout にアクセスして保持しているトークンを削除
export const logout = async () => {
    try {
        const cookieStore = await cookies();
        const token: string | undefined = cookieStore.get("token")?.value;

        if (!token) {
            throw new Error("トークンが存在しません");
        }

        const res = await fetch(`${process.env.API_BASE_URL}/api/logout`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({}),
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            console.error("ログアウトAPIエラー:", {
                status: res.status,
                statusText: res.statusText,
                errorData,
            });
            throw new Error(`ログアウトに失敗しました: ${res.status}`);
        }

        // Cookie削除
        cookieStore.delete("token");
    } catch (e) {
        console.error("ログアウト処理エラー:", e);
        throw new Error("ログアウトに失敗しました");
    }
};
