"use server";

import { getToken } from "@/services/auth/getToken";
import type { Textbook } from "@/app/types/textbook";

export const fetchTextbookDetail = async (id: string): Promise<Textbook> => {
  const url = `${process.env.API_BASE_URL}/api/textbooks/${id}`;
  console.log("Fetching textbook detail from:", url);

  const token = await getToken();

  try {
    const headers: HeadersInit = {
      "Content-Type": "application/json",
    };

    if (token) {
      headers["Authorization"] = `Bearer ${token}`;
    }

    const response = await fetch(url, {
      method: "GET",
      headers,
      cache: "no-cache",
    });

    console.log("Response status:", response.status);

    if (!response.ok) {
      if (response.status === 404) {
        throw new Error("Textbook not found");
      }
      const errorText = await response.text();
      console.error("API Error:", errorText);
      throw new Error(`Failed to fetch textbook detail: ${response.status}`);
    }

    const data: Textbook = await response.json();
    console.log(`Retrieved textbook: ${data.name}`);

    return data;
  } catch (error) {
    console.error("Error fetching textbook detail:", error);
    throw error;
  }
};