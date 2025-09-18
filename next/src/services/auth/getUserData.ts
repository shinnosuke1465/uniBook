"use server";

import { cookies } from "next/headers";


// /api/users/me にアクセスして自身のユーザ情報を取得
export const getUserData = async () => {
    try {
        const cookieStore = await cookies();
        const token: string | undefined = cookieStore.get("token")?.value;

        if (!token) {
            throw new Error("トークンが存在しません");
        }

        const res = await fetch(`${process.env.API_BASE_URL}/api/users/me`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
            },
            cache: "no-store",
        });

        if (!res.ok) {
            throw new Error("アクセス許可がありません");
        }

        return await res.json();
    } catch (e) {
        console.error(e);
        throw new Error("アクセス許可がありません");
    }
};