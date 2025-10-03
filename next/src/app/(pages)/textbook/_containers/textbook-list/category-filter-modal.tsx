"use client";

import { useState } from "react";

interface CategoryFilterModalProps {
  isOpen: boolean;
  onClose: () => void;
  onApply: (filters: CategoryFilters) => void;
  currentFilters: CategoryFilters;
}

export interface CategoryFilters {
  minPrice: number | null;
  maxPrice: number | null;
  conditionTypes: string[];
  saleStatus: "all" | "selling" | "sold";
}

export function CategoryFilterModal({
  isOpen,
  onClose,
  onApply,
  currentFilters,
}: CategoryFilterModalProps) {
  const [minPrice, setMinPrice] = useState<string>(
    currentFilters.minPrice?.toString() || ""
  );
  const [maxPrice, setMaxPrice] = useState<string>(
    currentFilters.maxPrice?.toString() || ""
  );
  const [selectedConditions, setSelectedConditions] = useState<string[]>(
    currentFilters.conditionTypes
  );
  const [saleStatus, setSaleStatus] = useState<"all" | "selling" | "sold">(
    currentFilters.saleStatus
  );

  const conditionOptions = [
    { value: "new", label: "新品" },
    { value: "near_new", label: "ほぼ新品" },
    { value: "no_damage", label: "傷や汚れなし" },
    { value: "slight_damage", label: "やや傷や汚れあり" },
    { value: "damage", label: "傷や汚れあり" },
    { value: "poor_condition", label: "全体的に状態が悪い" },
  ];

  const handleConditionToggle = (value: string) => {
    setSelectedConditions((prev) =>
      prev.includes(value)
        ? prev.filter((v) => v !== value)
        : [...prev, value]
    );
  };

  const handleApply = () => {
    onApply({
      minPrice: minPrice ? Number(minPrice) : null,
      maxPrice: maxPrice ? Number(maxPrice) : null,
      conditionTypes: selectedConditions,
      saleStatus,
    });
    onClose();
  };

  const handleReset = () => {
    setMinPrice("");
    setMaxPrice("");
    setSelectedConditions([]);
    setSaleStatus("all");
  };

  if (!isOpen) return null;

  return (
    <>
      {/* オーバーレイ */}
      <div
        className="fixed inset-0 z-40 bg-black bg-opacity-50"
        onClick={onClose}
      />

      {/* モーダル */}
      <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div className="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
          <h2 className="mb-4 text-xl font-bold">カテゴリー選択</h2>

          <div className="space-y-4">
            {/* 価格範囲 */}
            <div>
              <label className="mb-2 block text-sm font-semibold text-gray-700">
                価格範囲
              </label>
              <div className="flex items-center gap-2">
                <input
                  type="number"
                  value={minPrice}
                  onChange={(e) => setMinPrice(e.target.value)}
                  placeholder="最低価格"
                  className="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none"
                />
                <span className="text-gray-500">〜</span>
                <input
                  type="number"
                  value={maxPrice}
                  onChange={(e) => setMaxPrice(e.target.value)}
                  placeholder="最高価格"
                  className="w-full rounded-lg border border-gray-300 p-2 focus:border-blue-500 focus:outline-none"
                />
              </div>
            </div>

            {/* 商品の状態 */}
            <div>
              <label className="mb-2 block text-sm font-semibold text-gray-700">
                商品の状態
              </label>
              <div className="space-y-2">
                {conditionOptions.map((option) => (
                  <label
                    key={option.value}
                    className="flex items-center gap-2 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      checked={selectedConditions.includes(option.value)}
                      onChange={() => handleConditionToggle(option.value)}
                      className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span className="text-sm text-gray-700">
                      {option.label}
                    </span>
                  </label>
                ))}
              </div>
            </div>

            {/* 販売状態 */}
            <div>
              <label className="mb-2 block text-sm font-semibold text-gray-700">
                販売状態
              </label>
              <div className="space-y-2">
                <label className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    name="saleStatus"
                    checked={saleStatus === "all"}
                    onChange={() => setSaleStatus("all")}
                    className="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span className="text-sm text-gray-700">すべて</span>
                </label>
                <label className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    name="saleStatus"
                    checked={saleStatus === "selling"}
                    onChange={() => setSaleStatus("selling")}
                    className="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span className="text-sm text-gray-700">販売中</span>
                </label>
                <label className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    name="saleStatus"
                    checked={saleStatus === "sold"}
                    onChange={() => setSaleStatus("sold")}
                    className="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span className="text-sm text-gray-700">売却済み</span>
                </label>
              </div>
            </div>
          </div>

          {/* ボタン */}
          <div className="mt-6 flex justify-end space-x-3">
            <button
              onClick={handleReset}
              className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              リセット
            </button>
            <button
              onClick={onClose}
              className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              キャンセル
            </button>
            <button
              onClick={handleApply}
              className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
              適用
            </button>
          </div>
        </div>
      </div>
    </>
  );
}