import { fetchTextbookDetail } from "../../../fetchTextbookDetail";
import { TextbookDetailPresentation } from "./presentational";

interface TextbookDetailContainerProps {
  textbookId: string;
}

export async function TextbookDetailContainer({
  textbookId,
}: TextbookDetailContainerProps) {
  const textbook = await fetchTextbookDetail(textbookId);

  return <TextbookDetailPresentation textbook={textbook} />;
}