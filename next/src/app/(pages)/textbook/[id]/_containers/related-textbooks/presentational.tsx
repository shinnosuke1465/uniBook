import Link from "next/link";
import type { Textbook } from "@/app/types/textbook";
import { ImageFrame } from "@/components/image/image-frame";
import {
	LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL,
	S3_DEFAULT_TEXTBOOK_IMAGE_URL,
} from "@/constants";

interface RelatedTextbooksPresentationProps {
  textbooks: Textbook[];
}

export function RelatedTextbooksPresentation({
  textbooks,
}: RelatedTextbooksPresentationProps) {
  return (
    <div className="mt-12">
      <h2 className="mb-6 text-2xl font-bold">関連する教科書</h2>
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
        {textbooks.map((textbook) => (
          <Link
            key={textbook.id}
            href={`/textbook/${textbook.id}`}
            className="block overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
          >
            <div className="aspect-square overflow-hidden bg-gray-100">
              {textbook.image_urls.length > 0 ? (
                <ImageFrame
                  path={textbook.image_urls[0]}
                  alt={textbook.name}
                  className="h-full w-full object-cover"
                />
              ) : process.env.NODE_ENV === "production" ? (
                <ImageFrame
                  path={S3_DEFAULT_TEXTBOOK_IMAGE_URL}
                  alt="デフォルト画像"
                  className="h-full w-full object-cover"
                />
              ) : (
                <ImageFrame
                  path={LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL}
                  alt="デフォルト画像"
                  className="h-full w-full object-cover"
                />
              )}
            </div>
            <div className="p-3">
              <h3 className="mb-1 line-clamp-2 text-sm font-semibold">
                {textbook.name}
              </h3>
              <p className="mb-2 text-xs text-gray-600">
                {textbook.faculty_name}
              </p>
              <p className="text-lg font-bold text-blue-600">
                ¥{textbook.price.toLocaleString()}
              </p>
            </div>
          </Link>
        ))}
      </div>
    </div>
  );
}
