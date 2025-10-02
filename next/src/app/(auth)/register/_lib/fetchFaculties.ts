"use server";

export interface Faculty {
  id: string;
  name: string;
  university_id: string;
}

export const fetchFaculties = async (
  universityId: string
): Promise<Faculty[]> => {
  if (!universityId) {
    return [];
  }

  const response = await fetch(
    `${process.env.API_BASE_URL}/api/faculties?university_id=${universityId}`,
    {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    }
  );

  if (!response.ok) {
    throw new Error("学部一覧の取得に失敗しました");
  }

  const data = await response.json();
  return data.faculties || [];
};
