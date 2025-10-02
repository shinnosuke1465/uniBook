import { Suspense } from "react";
import { DealRoomDetailContainer } from "./_containers/deal-room-detail";

interface DealRoomDetailPageProps {
  params: Promise<{
    id: string;
  }>;
}

export default async function DealRoomDetailPage({
  params,
}: DealRoomDetailPageProps) {
  const { id } = await params;

  return (
    <div className="container mx-auto px-4 py-8">
      <Suspense
        fallback={
          <div className="flex min-h-[600px] items-center justify-center">
            <div className="text-gray-500">取引情報を読み込み中...</div>
          </div>
        }
      >
        <DealRoomDetailContainer dealRoomId={id} />
      </Suspense>
    </div>
  );
}
