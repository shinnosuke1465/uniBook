import { Suspense } from "react";
import { TextbookListContainer } from "./_containers/textbook-list";

export default function TextbookPage() {
  return (
    <div className="container mx-auto px-4 py-8">
      <Suspense
        fallback={
          <div className="flex min-h-[400px] items-center justify-center">
            <div className="text-gray-500">教科書取得中</div>
          </div>
        }
      >
        <TextbookListContainer />
      </Suspense>
    </div>
  );
}