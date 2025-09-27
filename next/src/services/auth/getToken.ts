"use server";

import { cookies } from "next/headers";

export const getToken = async (): Promise<string | null> => {
  const cookieStore = await cookies();
  const token = cookieStore.get("token");

  return token?.value || null;
}