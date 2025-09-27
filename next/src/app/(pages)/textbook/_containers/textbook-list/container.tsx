import { fetchTextbooks } from "@/app/(pages)/textbook/fetchTextbooks";
import { TextbookListPresentation } from "./presentational";

export async function TextbookListContainer() {
  const textbooks = await fetchTextbooks();

  return <TextbookListPresentation textbooks={textbooks} />;
}