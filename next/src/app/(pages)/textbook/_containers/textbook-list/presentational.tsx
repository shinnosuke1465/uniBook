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
    <div className="space-y-6">
      {/* 検索バー */}
      <div className="flex items-center gap-4">
        <div className="relative flex-1">
          <input
            type="text"
            value={keyword}
            onChange={(e) => setKeyword(e.target.value)}
            placeholder="教科書名、説明、大学名、学部名で検索..."
            className="w-full rounded-2xl border border-blue-200 bg-white py-3.5 pl-12 pr-4 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all"
          />
          <svg
            className="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-blue-400"
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
          className="rounded-2xl border border-blue-200 bg-white px-5 py-3.5 text-sm font-semibold text-blue-600 hover:bg-blue-50 shadow-sm transition-all"
        >
          詳細検索
        </button>
        <button
          onClick={() => setIsCategoryModalOpen(true)}
          className="rounded-2xl border border-blue-200 bg-white px-5 py-3.5 text-sm font-semibold text-blue-600 hover:bg-blue-50 shadow-sm transition-all"
        >
          カテゴリー選択
        </button>
      </div>

      {/* 適用中のフィルター - 詳細検索 */}
      {(selectedUniversityId || selectedFacultyId) && (
        <div className="flex items-center gap-2 flex-wrap">
          <span className="text-sm font-semibold text-slate-600">詳細検索:</span>
          {selectedUniversity && (
            <span className="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-blue-400 to-blue-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">
              {selectedUniversity}
            </span>
          )}
          {selectedFaculty && (
            <span className="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-blue-400 to-blue-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">
              {selectedFaculty}
            </span>
          )}
          <button
            onClick={handleClearFilters}
            className="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors"
          >
            クリア
          </button>
        </div>
      )}

      {/* 適用中のフィルター - カテゴリー */}
      {hasCategoryFilters && (
        <div className="flex items-center gap-2 flex-wrap">
          <span className="text-sm font-semibold text-slate-600">カテゴリー:</span>
          {categoryFilters.minPrice !== null && (
            <span className="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">
              ¥{categoryFilters.minPrice.toLocaleString()}〜
            </span>
          )}
          {categoryFilters.maxPrice !== null && (
            <span className="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">
              〜¥{categoryFilters.maxPrice.toLocaleString()}
            </span>
          )}
          {categoryFilters.conditionTypes.map((type) => (
            <span
              key={type}
              className="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-purple-400 to-purple-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm"
            >
              {conditionLabels[type as keyof typeof conditionLabels]}
            </span>
          ))}
          {categoryFilters.saleStatus !== "all" && (
            <span className="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-orange-400 to-orange-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">
              {saleStatusLabels[categoryFilters.saleStatus]}
            </span>
          )}
          <button
            onClick={handleClearCategoryFilters}
            className="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors"
          >
            クリア
          </button>
        </div>
      )}

      {/* 検索結果件数 */}
      <div className="text-sm font-semibold text-slate-600">
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
      className="group block overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1"
    >
      {/* 画像エリア */}
      <div className="relative aspect-[4/3] bg-gradient-to-br from-blue-50 to-indigo-50 overflow-hidden">
        {textbook.image_urls.length > 0 ? (
          <ImageFrame
            path={textbook.image_urls[0]}
            alt={textbook.name}
            width={400}
            height={300}
            className="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
          />
        ) : process.env.NODE_ENV === "production" ? (
          <ImageFrame
            path={S3_DEFAULT_TEXTBOOK_IMAGE_URL}
            alt="デフォルト画像"
            width={400}
            height={300}
            className="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
          />
        ) : (
          <ImageFrame
            path={LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL}
            alt="デフォルト画像"
            width={400}
            height={300}
            className="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
          />
        )}

        {/* SOLD OUTオーバーレイ */}
        {isSoldOut && (<div className="absolute inset-0 flex items-center justify-center backdrop-blur-sm" style={{ backgroundColor: "rgba(0, 0, 0, 0.4)" }}>
            <div className="rounded-2xl bg-gradient-to-r from-red-500 to-red-600 px-8 py-3 shadow-xl">
              <span className="text-2xl font-bold text-white">SOLD OUT</span>
            </div>
          </div>
        )}
      </div>

      {/* コンテンツエリア */}
      <div className={`p-5 ${isSoldOut ? "opacity-60" : ""}`}>
        <h3 className="mb-3 line-clamp-2 text-lg font-bold text-slate-800 group-hover:text-blue-600 transition-colors">
          {textbook.name}
        </h3>

        <div className="mb-3 space-y-1.5 text-sm text-slate-600">
          <p className="font-medium">{textbook.university_name}</p>
          <p>{textbook.faculty_name}</p>
        </div>

        <div className="mb-4 flex items-center justify-between">
          <span
            className={`text-3xl font-bold ${isSoldOut ? "text-gray-400 line-through" : "bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent"}`}
          >
            ¥{textbook.price.toLocaleString()}
          </span>
          <span
            className={`rounded-full px-3 py-1.5 text-xs font-semibold shadow-sm ${
              textbook.condition_type === "new"
                ? "bg-gradient-to-r from-emerald-400 to-emerald-500 text-white"
                : textbook.condition_type === "near_new"
                  ? "bg-gradient-to-r from-blue-400 to-blue-500 text-white"
                  : textbook.condition_type === "no_damage"
                    ? "bg-gradient-to-r from-amber-400 to-amber-500 text-white"
                    : "bg-gradient-to-r from-gray-400 to-gray-500 text-white"
            }`}
          >
            {conditionLabels[textbook.condition_type]}
          </span>
        </div>

        {/* 取引状態 */}
        {textbook.deal && (
          <div className="mb-3">
            {isSoldOut ? (
              <span className="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-red-400 to-red-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm">
                売り切れ
              </span>
            ) : (
              <span className="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-orange-400 to-orange-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm">
                販売中
              </span>
            )}
          </div>
        )}

        {/* 説明文 */}
        <p className="mb-4 line-clamp-2 text-sm text-slate-600 leading-relaxed">
          {textbook.description}
        </p>

        {/* いいね・コメント */}
        <div className="flex items-center justify-between border-t border-blue-100 pt-3">
          <div className="flex items-center space-x-4 text-sm font-medium text-slate-500">
            <span className="flex items-center gap-1.5 hover:text-blue-500 transition-colors">
              <svg className="w-4 h-4" fill={textbook.is_liked ? "currentColor" : "none"} stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              いいね
            </span>
            <span className="flex items-center gap-1.5 hover:text-blue-500 transition-colors">
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              {textbook.comments.length}
            </span>
          </div>
        </div>
      </div>
    </Link>
  );
}