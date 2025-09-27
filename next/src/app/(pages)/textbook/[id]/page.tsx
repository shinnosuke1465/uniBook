import { Suspense } from "react";
import { TextbookDetailContainer } from "./_containers/textbook-detail";

interface TextbookDetailPageProps {
  params: Promise<{
    id: string;
  }>;
}

export default async function TextbookDetailPage({
  params,
}: TextbookDetailPageProps) {
  const { id } = await params;

  return (
    <Suspense
      fallback={
        <div className="container mx-auto px-4 py-8">
          <div className="flex min-h-[600px] items-center justify-center">
            <div className="text-gray-500">教科書詳細を読み込み中...</div>
          </div>
        </div>
      }
    >
      <TextbookDetailContainer textbookId={id} />
    </Suspense>
  );
}