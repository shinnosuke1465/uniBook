import { Suspense } from "react";
import { fetchTextbookDetail } from "../../../fetchTextbookDetail";
import { TextbookDetailPresentation } from "./presentational";
import { PaymentContainer } from "../payment";

interface TextbookDetailContainerProps {
  textbookId: string;
}

export async function TextbookDetailContainer({
  textbookId,
}: TextbookDetailContainerProps) {
  const textbook = await fetchTextbookDetail(textbookId);

  return (
    <TextbookDetailPresentation textbook={textbook}>
      <Suspense
        fallback={
          <div className="flex items-center justify-center py-8">
            <div className="h-8 w-8 animate-spin rounded-full border-b-2 border-blue-600"></div>
            <span className="ml-3 text-gray-600">支払い情報を読み込み中...</span>
          </div>
        }
      >
        <PaymentContainer textbookId={textbookId} price={textbook.price} />
      </Suspense>
    </TextbookDetailPresentation>
  );
}