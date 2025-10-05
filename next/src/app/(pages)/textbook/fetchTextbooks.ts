"use server";

import { getToken } from "@/services/auth/getToken";
import type { Textbook, TextbooksResponse } from "@/app/types/textbook";

export const fetchTextbooks = async (): Promise<Textbook[]> => {
  const url = `${process.env.API_BASE_URL}/api/textbooks`;

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

    const data: TextbooksResponse = await response.json();

    return data.textbooks;
  } catch (error) {
    console.error("Error fetching textbook:", error);
    throw error;
  }
};