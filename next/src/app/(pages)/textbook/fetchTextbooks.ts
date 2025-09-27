"use server";

import { getToken } from "@/services/auth/getToken";
import type { Textbook, TextbooksResponse } from "@/app/types/textbook";

export const fetchTextbooks = async (): Promise<Textbook[]> => {
  const url = `${process.env.API_BASE_URL}/api/textbooks`;
  console.log("Fetching textbook from:", url);

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

    const data: TextbooksResponse = await response.json();
    console.log(`Retrieved ${data.textbooks.length} textbooks`);

    return data.textbooks;
  } catch (error) {
    console.error("Error fetching textbook:", error);
    throw error;
  }
};