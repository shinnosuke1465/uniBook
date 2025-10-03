import Link from "next/link";
import type { DealRoom } from "../../../_lib/fetchDealRooms";
import { ImageFrame } from "@/components/image/image-frame";
import {
	LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL,
	S3_DEFAULT_TEXTBOOK_IMAGE_URL,
} from "@/constants";

interface DealRoomsPresentationProps {
  dealRooms: DealRoom[];
}

export function DealRoomsPresentation({
  dealRooms,
}: DealRoomsPresentationProps) {
  if (dealRooms.length === 0) {
    // @ts-ignore
      return (
      <div className="flex min-h-[400px] items-center justify-center">
        <p className="text-gray-500">取引はまだありません</p>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      {dealRooms.map((room) => (
        <DealRoomCard key={room.id} room={room} />
      ))}
    </div>
  );
}

interface DealRoomCardProps {
  room: DealRoom;
}

function DealRoomCard({ room }: DealRoomCardProps) {
  const formattedDate = new Date(room.created_at).toLocaleDateString("ja-JP", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });

  return (
    <Link
      href={`/mypage/deal_rooms/${room.id}`}
      className="block overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
    >
      <div className="flex items-center p-4">
        {/* 商品画像 */}
        <div className="mr-4 h-20 w-20 flex-shrink-0 overflow-hidden rounded-md bg-gray-100">
          {room.deal.textbook.image_urls.length > 0 ? (
            <ImageFrame
              path={room.deal.textbook.image_urls[0]}
              alt={room.deal.textbook.name}
              width={80}
              height={80}
              className="h-full w-full object-cover"
            />
          ) : process.env.NODE_ENV === "production" ? (
            <ImageFrame
              path={S3_DEFAULT_TEXTBOOK_IMAGE_URL}
              alt="デフォルト画像"
              width={80}
              height={80}
              className="h-full w-full object-cover"
            />
          ) : (
            <ImageFrame
              path={LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL}
              alt="デフォルト画像"
              width={80}
              height={80}
              className="h-full w-full object-cover"
            />
          )}
        </div>

        {/* 取引情報 */}
        <div className="flex-1">
          <h3 className="mb-1 text-lg font-semibold">
            {room.deal.textbook.name}
          </h3>

          <div className="mb-2 flex items-center space-x-2">
            {room.deal.seller_info.profile_image_url ? (
              <img
                src={room.deal.seller_info.profile_image_url}
                alt={room.deal.seller_info.nickname}
                className="h-6 w-6 rounded-full"
              />
            ) : (
              <div className="flex h-6 w-6 items-center justify-center rounded-full bg-gray-300">
                <span className="text-xs">👤</span>
              </div>
            )}
            <span className="text-sm text-gray-600">
              {room.deal.seller_info.nickname}
            </span>
          </div>

          <p className="text-xs text-gray-500">作成日: {formattedDate}</p>
        </div>

        {/* 矢印アイコン */}
        <div className="ml-4 text-gray-400">
          <svg
            className="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M9 5l7 7-7 7"
            />
          </svg>
        </div>
      </div>
    </Link>
  );
}
