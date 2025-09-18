"use server";
export interface Faculty {
  id: string;
  name: string;
  university_id: string;
}

// 認証不要の学部一覧取得（登録画面用）
export const getPublicFaculties = async (universityId: string): Promise<Faculty[]> => {
  if (!universityId) {
    return [];
  }

  const response = await fetch(`${process.env.API_BASE_URL}/api/faculties`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
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