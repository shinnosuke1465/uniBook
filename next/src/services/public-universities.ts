"use server";
export interface University {
  id: string;
  name: string;
}

// 認証不要の大学一覧取得（登録画面用）
export const getPublicUniversities = async (): Promise<University[]> => {
  const response = await fetch(`${process.env.API_BASE_URL}/api/universities`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  });

  if (!response.ok) {
    throw new Error('大学一覧の取得に失敗しました');
  }

  return response.json();
};