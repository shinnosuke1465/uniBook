import Link from "next/link";
import type { Textbook } from "@/app/types/textbook";
import { ImageFrame } from "@/components/image/image-frame";
import {
  LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL,
  S3_DEFAULT_TEXTBOOK_IMAGE_URL,
} from "@/constants";

interface TextbookListPresentationProps {
  textbooks: Textbook[];
}

export function TextbookListPresentation({
  textbooks,
}: TextbookListPresentationProps) {
  if (textbooks.length === 0) {
    return (
      <div className="flex min-h-[400px] items-center justify-center">
        <p className="text-gray-500">教科書が見つかりませんでした</p>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      {textbooks.map((textbook) => (
        <TextbookCard key={textbook.id} textbook={textbook} />
      ))}
    </div>
  );
}

interface TextbookCardProps {
  textbook: Textbook;
}

function TextbookCard({ textbook }: TextbookCardProps) {
  const conditionLabels = {
    new: "新品",
    near_new: "ほぼ新品",
    no_damage: "傷や汚れなし",
    slight_damage: "やや傷や汚れあり",
    damage: "傷や汚れあり",
    poor_condition: "全体的に状態が悪い",
  };

  const isSoldOut = textbook.deal && !textbook.deal.is_purchasable;

  return (
    <Link
      href={`/textbook/${textbook.id}`}
      className="block overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
    >
      {/* 画像エリア */}
      <div className="relative aspect-[4/3] bg-gray-100">
        {textbook.image_urls.length > 0 ? (
          <ImageFrame
            path={textbook.image_urls[0]}
            alt={textbook.name}
            width={400}
            height={300}
            className="h-full w-full object-cover"
          />
        ) : process.env.NODE_ENV === "production" ? (
          <ImageFrame
            path={S3_DEFAULT_TEXTBOOK_IMAGE_URL}
            alt="デフォルト画像"
            width={400}
            height={300}
            className="h-full w-full object-cover"
          />
        ) : (
          <ImageFrame
            path={LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL}
            alt="デフォルト画像"
            width={400}
            height={300}
            className="h-full w-full object-cover"
          />
        )}

        {/* SOLD OUTオーバーレイ */}
        {isSoldOut && (
          <div className="absolute inset-0 flex items-center justify-center bg-black bg-opacity-90">
            <div className="rounded-md bg-red-600 px-6 py-2">
              <span className="text-xl font-bold text-white">SOLD OUT</span>
            </div>
          </div>
        )}
      </div>

      {/* コンテンツエリア */}
      <div className={`p-4 ${isSoldOut ? "opacity-60" : ""}`}>
        <h3 className="mb-2 line-clamp-2 text-lg font-semibold">
          {textbook.name}
        </h3>

        <div className="mb-3 space-y-1 text-sm text-gray-600">
          <p>{textbook.university_name}</p>
          <p>{textbook.faculty_name}</p>
        </div>

        <div className="mb-3 flex items-center justify-between">
          <span
            className={`text-2xl font-bold ${isSoldOut ? "text-gray-400 line-through" : "text-blue-600"}`}
          >
            ¥{textbook.price.toLocaleString()}
          </span>
          <span
            className={`rounded-full px-3 py-1 text-xs font-medium ${
              textbook.condition_type === "new"
                ? "bg-green-100 text-green-800"
                : textbook.condition_type === "near_new"
                  ? "bg-blue-100 text-blue-800"
                  : textbook.condition_type === "no_damage"
                    ? "bg-yellow-100 text-yellow-800"
                    : "bg-gray-100 text-gray-800"
            }`}
          >
            {conditionLabels[textbook.condition_type]}
          </span>
        </div>

        {/* 取引状態 */}
        {textbook.deal && (
          <div className="mb-2">
            {isSoldOut ? (
              <span className="inline-block rounded bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                売り切れ
              </span>
            ) : (
              <span className="inline-block rounded bg-orange-100 px-2 py-1 text-xs font-medium text-orange-800">
                販売中
              </span>
            )}
          </div>
        )}

        {/* 説明文 */}
        <p className="mb-3 line-clamp-2 text-sm text-gray-600">
          {textbook.description}
        </p>

        {/* いいね・コメント */}
        <div className="flex items-center justify-between border-t pt-3">
          <div className="flex items-center space-x-4 text-sm text-gray-500">
            <span className="flex items-center">
              {textbook.is_liked ? "❤️" : "🤍"} いいね
            </span>
            <span className="flex items-center">
              💬 {textbook.comments.length}
            </span>
          </div>
        </div>
      </div>
    </Link>
  );
}