import React from "react";

export default async function Page() {
    const res = await fetch(`${process.env.API_BASE_URL}/api/test`, {
        cache: "no-store", // SSR時にキャッシュさせたくない場合
    });

    if (!res.ok) {
        throw new Error("APIリクエストに失敗しました");
    }

    const data: { message: string } = await res.json();

    return (
        <>
            <p>{data.message}</p>
        </>
    );
}
