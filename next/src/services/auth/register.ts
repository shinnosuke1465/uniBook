"use server";

import { cookies } from "next/headers";

//ユーザー登録処理
export const register = async (data: FormData) => {
    try {
        const imageId = data.get("image_id");
        const requestBody = {
            name: data.get("name"),
            password: data.get("password"),
            post_code: data.get("post_code"),
            address: data.get("address"),
            mail_address: data.get("mail_address"),
            image_id: imageId === "" ? null : imageId,
            faculty_id: data.get("faculty_id"),
            university_id: data.get("university_id")
        };

        console.log("Register API URL:", `${process.env.API_BASE_URL}/api/users`);
        console.log("Request body:", requestBody);

        const res = await fetch(`${process.env.API_BASE_URL}/api/users`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify(requestBody),
            cache: "no-store",
        });

        console.log("Response status:", res.status);

        if (!res.ok) {
            let message = "ユーザ新規登録に失敗しました";
            try {
                const body = await res.json();
                console.error("API Error response:", body);
                if (body?.message) message = body.message;
            } catch {
                const errorText = await res.text();
                console.error("API Error (non-JSON):", errorText);
            }
            throw new Error(message);
        }

        // API から token を取得
        const body = await res.json();
        console.log("Success response:", body);
        const token: string | undefined = body.token;

        if (!token) {
            console.error("Token not found in response:", body);
            throw new Error("トークンが取得できませんでした");
        }

        // サーバー側で Cookie に保存
        const cookieStore = await cookies();
        cookieStore.set({ name: "token", value: token, httpOnly: true, });

        return { token };
    } catch (e) {
        console.error("Register error:", e);
        if (e instanceof Error) {
            throw e; // 元のエラーメッセージを保持
        }
        throw new Error("ユーザ新規登録に失敗しました");
    }
};
