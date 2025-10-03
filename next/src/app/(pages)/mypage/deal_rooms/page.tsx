import { Suspense } from "react";
import { DealRoomsContainer } from "./_containers/deal-rooms";

export default function DealRoomsPage() {
  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="mb-8 text-3xl font-bold">取引一覧</h1>

      <Suspense
        fallback={
          <div className="flex min-h-[400px] items-center justify-center">
            <div className="text-gray-500">取引一覧を読み込み中...</div>
          </div>
        }
      >
        <DealRoomsContainer />
      </Suspense>
    </div>
  );
}
