"use client";

import { useState } from "react";
import { uploadImage } from "@/services/textbook/image";
import { createTextbook } from "@/services/textbook/create";
import { useRouter } from "next/navigation";
import { useAuthContext } from "@/contexts/AuthContext";

interface ImagePreview {
  id: string;
  file: File;
  preview: string;
}

export function TextbookCreatePresentation() {
  const router = useRouter();
  const { authUser } = useAuthContext();
  const [name, setName] = useState("");
  const [price, setPrice] = useState("");
  const [description, setDescription] = useState("");
  const [conditionType, setConditionType] = useState<
    "new" | "near_new" | "no_damage" | "slight_damage" | "damage" | "poor_condition"
  >("no_damage");
  const [images, setImages] = useState<ImagePreview[]>([]);
  const [uploadedImageIds, setUploadedImageIds] = useState<string[]>([]);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const conditionLabels = {
    new: "新品",
    near_new: "ほぼ新品",
    no_damage: "傷や汚れなし",
    slight_damage: "やや傷や汚れあり",
    damage: "傷や汚れあり",
    poor_condition: "全体的に状態が悪い",
  };

  const handleImageChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
    if (!e.target.files || e.target.files.length === 0) return;

    const files = Array.from(e.target.files);

    // 画像をアップロード
    for (const file of files) {
      try {
        const formData = new FormData();
        formData.append("image", file);

        const response = await uploadImage(formData);

        // プレビュー用の画像を追加（アップロード成功後）
        const newImage = {
          id: response.id,
          file,
          preview: URL.createObjectURL(file),
        };

        setImages((prev) => [...prev, newImage]);
        setUploadedImageIds((prev) => [...prev, response.id]);
      } catch (error) {
        console.error("画像アップロードエラー:", error);
        alert(`画像のアップロードに失敗しました: ${file.name}`);
      }
    }
  };

  const handleRemoveImage = (id: string) => {
    setImages(images.filter((img) => img.id !== id));
    setUploadedImageIds(uploadedImageIds.filter((imgId) => imgId !== id));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (
      !name ||
      !price ||
      !description ||
      !authUser ||
      uploadedImageIds.length === 0
    ) {
      alert("すべての項目を入力してください");
      return;
    }

    setIsSubmitting(true);

    try {
      await createTextbook({
        name,
        price: Number(price),
        description,
        image_ids: uploadedImageIds,
        university_id: authUser.university_id,
        faculty_id: authUser.faculty_id,
        condition_type: conditionType,
      });

      alert("教科書を出品しました");
      router.push("/textbook");
    } catch (error) {
      console.error("教科書作成エラー:", error);
      alert("教科書の作成に失敗しました");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="mb-8 text-3xl font-bold">教科書を出品</h1>

      <form onSubmit={handleSubmit} className="mx-auto max-w-2xl space-y-6">
        {/* 画像アップロード */}
        <div>
          <label className="mb-2 block text-sm font-semibold text-gray-700">
            商品画像
          </label>
          <input
            type="file"
            accept="image/*"
            multiple
            onChange={handleImageChange}
            className="w-full rounded-lg border border-gray-300 p-2"
          />
          {images.length > 0 && (
            <div className="mt-4 grid grid-cols-4 gap-2">
              {images.map((image) => (
                <div key={image.id} className="relative">
                  <img
                    src={image.preview}
                    alt="プレビュー"
                    className="aspect-square rounded-lg object-cover"
                  />
                  <button
                    type="button"
                    onClick={() => handleRemoveImage(image.id)}
                    className="absolute right-1 top-1 rounded-full bg-red-500 px-2 py-1 text-xs text-white hover:bg-red-600"
                  >
                    削除
                  </button>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* 商品名 */}
        <div>
          <label
            htmlFor="name"
            className="mb-2 block text-sm font-semibold text-gray-700"
          >
            商品名
          </label>
          <input
            type="text"
            id="name"
            value={name}
            onChange={(e) => setName(e.target.value)}
            className="w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
            placeholder="例: 線形代数学の教科書"
          />
        </div>

        {/* 価格 */}
        <div>
          <label
            htmlFor="price"
            className="mb-2 block text-sm font-semibold text-gray-700"
          >
            価格 (円)
          </label>
          <input
            type="number"
            id="price"
            value={price}
            onChange={(e) => setPrice(e.target.value)}
            className="w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
            placeholder="1000"
            min="0"
          />
        </div>

        {/* 商品説明 */}
        <div>
          <label
            htmlFor="description"
            className="mb-2 block text-sm font-semibold text-gray-700"
          >
            商品説明
          </label>
          <textarea
            id="description"
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            className="w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
            rows={5}
            placeholder="商品の状態や特徴を詳しく記載してください"
          />
        </div>

        {/* 商品の状態 */}
        <div>
          <label
            htmlFor="condition"
            className="mb-2 block text-sm font-semibold text-gray-700"
          >
            商品の状態
          </label>
          <select
            id="condition"
            value={conditionType}
            onChange={(e) =>
              setConditionType(
                e.target.value as "new" | "near_new" | "no_damage" | "slight_damage" | "damage" | "poor_condition"
              )
            }
            className="w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
          >
            {Object.entries(conditionLabels).map(([value, label]) => (
              <option key={value} value={value}>
                {label}
              </option>
            ))}
          </select>
        </div>

        {/* 大学・学部情報 */}
        <div className="rounded-lg bg-gray-50 p-4">
          <h2 className="mb-2 text-sm font-semibold text-gray-600">
            出品者の所属
          </h2>
          <p className="text-lg">{authUser?.university_name || "-"}</p>
          <p className="text-gray-600">{authUser?.faculty_name || "-"}</p>
        </div>

        {/* 送信ボタン */}
        <div className="flex justify-end space-x-4">
          <button
            type="button"
            onClick={() => router.back()}
            className="rounded-lg border-2 border-gray-300 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-50"
          >
            キャンセル
          </button>
          <button
            type="submit"
            disabled={isSubmitting}
            className="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300"
          >
            {isSubmitting ? "出品中..." : "出品する"}
          </button>
        </div>
      </form>
    </div>
  );
}