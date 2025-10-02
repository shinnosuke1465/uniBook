"use server";

export interface University {
  id: string;
  name: string;
}

export const fetchUniversities = async (): Promise<University[]> => {
  const url = `${process.env.API_BASE_URL}/api/universities`;

  const response = await fetch(url, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (!response.ok) {
    const errorText = await response.text();
    console.error("大学一覧取得APIエラー:", errorText);
    throw new Error(`大学一覧の取得に失敗しました: ${response.status}`);
  }

  const data = await response.json();
  return data.universities || [];
};
