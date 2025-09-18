"use server";

export interface University {
  id: string;
  name: string;
}

export const getUniversities = async (): Promise<University[]> => {
  const url = `${process.env.API_BASE_URL}/api/universities`;
  console.log("API URL:", url);

  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  });

  console.log("Response status:", response.status);

  if (!response.ok) {
    const errorText = await response.text();
    console.error("API Error:", errorText);
    throw new Error(`大学一覧の取得に失敗しました: ${response.status} ${errorText}`);
  }

  const data = await response.json();
  console.log("Response data:", data);
  return data.universities || [];
};