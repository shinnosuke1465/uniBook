import { fetchTextbooks } from "../../../fetchTextbooks";
import { RelatedTextbooksPresentation } from "./presentational";

interface RelatedTextbooksContainerProps {
  currentTextbookId: string;
  universityId: string;
}

export async function RelatedTextbooksContainer({
  currentTextbookId,
  universityId,
}: RelatedTextbooksContainerProps) {
  const allTextbooks = await fetchTextbooks();

  console.log("All textbooks count:", allTextbooks.length);
  console.log("Current textbook ID:", currentTextbookId);
  console.log("University ID:", universityId);

  // 同じ大学の教科書で、現在の教科書以外、かつ販売中のものをフィルタリング
  const relatedTextbooks = allTextbooks
    .filter(
      (textbook) =>
        textbook.university_id === universityId &&
        textbook.id !== currentTextbookId &&
        textbook.deal?.is_purchasable === true
    )
    .slice(0, 4); // 最大4件まで表示

  console.log("Related textbooks count:", relatedTextbooks.length);

  if (relatedTextbooks.length === 0) {
    return null;
  }

  return <RelatedTextbooksPresentation textbooks={relatedTextbooks} />;
}
