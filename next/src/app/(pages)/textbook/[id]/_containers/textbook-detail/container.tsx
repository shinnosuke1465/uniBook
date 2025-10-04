import { Suspense } from "react";
import { fetchTextbookDetail } from "../../../fetchTextbookDetail";
import { TextbookDetailPresentation } from "./presentational";
import { PaymentContainer } from "../payment";
import { RelatedTextbooksContainer } from "../related-textbooks/container";

interface TextbookDetailContainerProps {
  textbookId: string;
}

export async function TextbookDetailContainer({
  textbookId,
}: TextbookDetailContainerProps) {
  const textbook = await fetchTextbookDetail(textbookId);

  return (
    <TextbookDetailPresentation
      textbook={textbook}
      relatedTextbooks={
        <Suspense
          fallback={
            <div className="mt-12">
              <h2 className="mb-6 text-2xl font-bold">関連する教科書</h2>
              <div className="flex items-center justify-center py-8">
                <div className="h-8 w-8 animate-spin rounded-full border-b-2 border-blue-600"></div>
                <span className="ml-3 text-gray-600">読み込み中...</span>
              </div>
            </div>
          }
        >
          <RelatedTextbooksContainer
            currentTextbookId={textbook.id}
            universityId={textbook.university_id}
          />
        </Suspense>
      }
    >
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