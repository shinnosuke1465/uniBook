"use server";

import { cookies } from "next/headers";

// ログイン処理。
// email,passwordを入力フォームから受け取り、認証が成功したらAPIから返却されたtokenをサーバー側のcookieにセット。
// これ以降、クライアントは毎回このtokenをリクエストに付与して送信。
export const login = async (data: FormData) => {
    const endpoint = `${process.env.API_BASE_URL}/api/login`;
    const res = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            mail_address: data.get("mail_address"),
            password: data.get("password"),
        }),
        cache: "no-store",
    });

    if (!res.ok) throw new Error("EmailまたはPasswordに誤りがあります");

    const body = await res.json();
    const token = body.token;

    // サーバー側で Cookie に保存
    const cookieStore = await cookies();
    cookieStore.set({ name: "token", value: token, httpOnly: true, });

    return { token, endpoint };
};