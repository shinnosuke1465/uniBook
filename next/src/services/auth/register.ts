"use server";

import { cookies } from "next/headers";

//ユーザー登録処理
export const register = async (data: FormData) => {
    try {
        const imageId = data.get("image_id");
        const res = await fetch(`${process.env.API_BASE_URL}/api/register`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                name: data.get("name"),
                password: data.get("password"),
                post_code: data.get("post_code"),
                address: data.get("address"),
                mail_address: data.get("mail_address"),
                image_id: imageId === "" ? null : imageId,
                faculty_id: data.get("faculty_id"),
                university_id: data.get("university_id")
            }),
            cache: "no-store",
        });

        if (!res.ok) {
            let message = "ユーザ新規登録に失敗しました";
            try {
                const body = await res.json();
                if (body?.message) message = body.message;
            } catch {
                /* JSONでなければ既定メッセージ */
            }
            throw new Error(message);
        }

        // API から token を取得
        const body = await res.json();
        const token: string | undefined = body.token;

        if (!token) {
            throw new Error("トークンが取得できませんでした");
        }

        // サーバー側で Cookie に保存
        const cookieStore = await cookies();
        cookieStore.set({ name: "token", value: token, httpOnly: true, });

        return { token };
    } catch (e) {
        console.error(e);
        throw new Error("ユーザ新規登録に失敗しました");
    }
};
