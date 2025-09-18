"use server";

import { cookies } from "next/headers";
export interface University {
  id: string;
  name: string;
}

export const getUniversities = async (): Promise<University[]> => {
    const cookieStore = await cookies();
    const token: string | undefined = cookieStore.get("token")?.value;

    if (!token) {
        throw new Error("トークンが存在しません");
    }

    const response = await fetch(`${process.env.API_BASE_URL}/api/universities`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({}),
  });

  if (!response.ok) {
    throw new Error('大学一覧の取得に失敗しました');
  }

  return response.json();
};