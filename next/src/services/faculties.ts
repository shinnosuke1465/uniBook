"use server";

import { cookies } from "next/headers";
export interface Faculty {
  id: string;
  name: string;
  university_id: string;
}

export const getFaculties = async (universityId: string): Promise<Faculty[]> => {
  if (!universityId) {
    return [];
  }
    const cookieStore = await cookies();
    const token: string | undefined = cookieStore.get("token")?.value;

    if (!token) {
        throw new Error("トークンが存在しません");
    }

  const response = await fetch(`${process.env.API_BASE_URL}/api/faculties`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
    },
      body: JSON.stringify({
          university_id: universityId,
      }),
  });

  if (!response.ok) {
    throw new Error('学部一覧の取得に失敗しました');
  }

  return response.json();
};