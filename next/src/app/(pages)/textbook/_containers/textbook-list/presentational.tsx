"use client";

import Link from "next/link";
import { useState, useMemo } from "react";
import type { Textbook } from "@/app/types/textbook";
import { ImageFrame } from "@/components/image/image-frame";
import {
  LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL,
  S3_DEFAULT_TEXTBOOK_IMAGE_URL,
} from "@/constants";
import { AdvancedSearchModal } from "./advanced-search-modal";
import {
  CategoryFilterModal,
  type CategoryFilters,
} from "./category-filter-modal";

interface TextbookListPresentationProps {
  textbooks: Textbook[];
}

export function TextbookListPresentation({
  textbooks,
}: TextbookListPresentationProps) {
  const [keyword, setKeyword] = useState("");
  const [selectedUniversityId, setSelectedUniversityId] = useState<string | null>(null);
  const [selectedFacultyId, setSelectedFacultyId] = useState<string | null>(null);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isCategoryModalOpen, setIsCategoryModalOpen] = useState(false);
  const [categoryFilters, setCategoryFilters] = useState<CategoryFilters>({
    minPrice: null,
    maxPrice: null,
    conditionTypes: [],
    saleStatus: "all",
  });

  const filteredTextbooks = useMemo(() => {
    let result = textbooks;

    // キーワード検索
    if (keyword.trim()) {
      const searchTerm = keyword.toLowerCase();
      result = result.filter((textbook) => {
        return (
          textbook.name.toLowerCase().includes(searchTerm) ||
          textbook.description.toLowerCase().includes(searchTerm) ||
          textbook.university_name.toLowerCase().includes(searchTerm) ||
          textbook.faculty_name.toLowerCase().includes(searchTerm)
        );
      });
    }

    // 大学フィルター
    if (selectedUniversityId) {
      result = result.filter(
        (textbook) => textbook.university_id === selectedUniversityId
      );
    }

    // 学部フィルター
    if (selectedFacultyId) {
      result = result.filter(
        (textbook) => textbook.faculty_id === selectedFacultyId
      );
    }

    // 価格フィルター
    if (categoryFilters.minPrice !== null) {
      result = result.filter(
        (textbook) => textbook.price >= categoryFilters.minPrice!
      );
    }
    if (categoryFilters.maxPrice !== null) {
      result = result.filter(
        (textbook) => textbook.price <= categoryFilters.maxPrice!
      );
    }

    // 商品状態フィルター
    if (categoryFilters.conditionTypes.length > 0) {
      result = result.filter((textbook) =>
        categoryFilters.conditionTypes.includes(textbook.condition_type)
      );
    }

    // 販売状態フィルター
    if (categoryFilters.saleStatus === "selling") {
      result = result.filter(
        (textbook) => textbook.deal && textbook.deal.is_purchasable
      );
    } else if (categoryFilters.saleStatus === "sold") {
      result = result.filter(
        (textbook) => textbook.deal && !textbook.deal.is_purchasable
      );
    }

    return result;
  }, [textbooks, keyword, selectedUniversityId, selectedFacultyId, categoryFilters]);

  const handleApplyAdvancedSearch = (
    universityId: string | null,
    facultyId: string | null
  ) => {
    setSelectedUniversityId(universityId);
    setSelectedFacultyId(facultyId);
  };

  const handleClearFilters = () => {
    setKeyword("");
    setSelectedUniversityId(null);
    setSelectedFacultyId(null);
  };

  const handleClearCategoryFilters = () => {
    setCategoryFilters({
      minPrice: null,
      maxPrice: null,
      conditionTypes: [],
      saleStatus: "all",
    });
  };

  const selectedUniversity = textbooks.find(
    (t) => t.university_id === selectedUniversityId
  )?.university_name;
  const selectedFaculty = textbooks.find(
    (t) => t.faculty_id === selectedFacultyId
  )?.faculty_name;

  const conditionLabels = {
    new: "新品",
    near_new: "ほぼ新品",
    no_damage: "傷や汚れなし",
    slight_damage: "やや傷や汚れあり",
    damage: "傷や汚れあり",
    poor_condition: "全体的に状態が悪い",
  };

  const saleStatusLabels = {
    all: "すべて",
    selling: "販売中",
    sold: "売却済み",
  };

  const hasCategoryFilters =
    categoryFilters.minPrice !== null ||
    categoryFilters.maxPrice !== null ||
    categoryFilters.conditionTypes.length > 0 ||
    categoryFilters.saleStatus !== "all";

  return (
    <div>
      {/* 検索バー */}
      <div className="flex items-center gap-4" style={{ marginBottom: "1.5rem" }}>
        <div className="relative flex-1">
          <input
            type="text"
            value={keyword}
            onChange={(e) => setKeyword(e.target.value)}
            placeholder="教科書名、説明、大学名、学部名で検索..."
            className="w-full rounded-lg border border-gray-300 py-3 pl-10 pr-4 focus:border-blue-500 focus:outline-none"
          />
          <svg
            className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
        </div>
        <button
          onClick={() => setIsModalOpen(true)}
          className="rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          詳細検索
        </button>
        <button
          onClick={() => setIsCategoryModalOpen(true)}
          className="rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          カテゴリー選択
        </button>
      </div>

      {/* 適用中のフィルター - 詳細検索 */}
      {(selectedUniversityId || selectedFacultyId) && (
        <div className="flex items-center gap-2">
          <span className="text-sm text-gray-600">詳細検索:</span>
          {selectedUniversity && (
            <span className="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800">
              {selectedUniversity}
            </span>
          )}
          {selectedFaculty && (
            <span className="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800">
              {selectedFaculty}
            </span>
          )}
          <button
            onClick={handleClearFilters}
            className="text-sm text-gray-600 hover:text-gray-900 underline"
          >
            クリア
          </button>
        </div>
      )}

      {/* 適用中のフィルター - カテゴリー */}
      {hasCategoryFilters && (
        <div className="flex items-center gap-2 flex-wrap" style={{ marginBottom: "0.5rem" }}>
          <span className="text-sm text-gray-600">カテゴリー:</span>
          {categoryFilters.minPrice !== null && (
            <span className="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm text-green-800">
              ¥{categoryFilters.minPrice.toLocaleString()}〜
            </span>
          )}
          {categoryFilters.maxPrice !== null && (
            <span className="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm text-green-800">
              〜¥{categoryFilters.maxPrice.toLocaleString()}
            </span>
          )}
          {categoryFilters.conditionTypes.map((type) => (
            <span
              key={type}
              className="inline-flex items-center gap-1 rounded-full bg-purple-100 px-3 py-1 text-sm text-purple-800"
            >
              {conditionLabels[type as keyof typeof conditionLabels]}
            </span>
          ))}
          {categoryFilters.saleStatus !== "all" && (
            <span className="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-sm text-orange-800">
              {saleStatusLabels[categoryFilters.saleStatus]}
            </span>
          )}
          <button
            onClick={handleClearCategoryFilters}
            className="text-sm text-gray-600 hover:text-gray-900 underline"
          >
            クリア
          </button>
        </div>
      )}

      {/* 検索結果件数 */}
      <div className="text-sm text-gray-600" style={{ marginBottom: "1.5rem" }}>
        {filteredTextbooks.length}件の教科書が見つかりました
      </div>

      {/* 詳細検索モーダル */}
      <AdvancedSearchModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        onApply={handleApplyAdvancedSearch}
      />

      {/* カテゴリー選択モーダル */}
      <CategoryFilterModal
        isOpen={isCategoryModalOpen}
        onClose={() => setIsCategoryModalOpen(false)}
        onApply={setCategoryFilters}
        currentFilters={categoryFilters}
      />

      {/* 教科書一覧 */}
      {filteredTextbooks.length === 0 ? (
        <div className="flex min-h-[400px] items-center justify-center">
          <p className="text-gray-500">教科書が見つかりませんでした</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          {filteredTextbooks.map((textbook) => (
            <TextbookCard key={textbook.id} textbook={textbook} />
          ))}
        </div>
      )}
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
        {isSoldOut && (<div className="absolute inset-0 flex items-center justify-center" style={{ backgroundColor: "rgba(0, 0, 0, 0.3)" }}>
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