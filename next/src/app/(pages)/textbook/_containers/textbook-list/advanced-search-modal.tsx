"use client";

import { useState, useEffect } from "react";
import { getUniversities, type University } from "@/services/info/getUniversities";
import { getFaculties, type Faculty } from "@/services/info/getFaculties";

interface AdvancedSearchModalProps {
  isOpen: boolean;
  onClose: () => void;
  onApply: (universityId: string | null, facultyId: string | null) => void;
}

export function AdvancedSearchModal({
  isOpen,
  onClose,
  onApply,
}: AdvancedSearchModalProps) {
  const [universities, setUniversities] = useState<University[]>([]);
  const [faculties, setFaculties] = useState<Faculty[]>([]);
  const [selectedUniversityId, setSelectedUniversityId] = useState<string>("");
  const [selectedFacultyId, setSelectedFacultyId] = useState<string>("");
  const [universityKeyword, setUniversityKeyword] = useState<string>("");
  const [facultyKeyword, setFacultyKeyword] = useState<string>("");
  const [isLoadingUniversities, setIsLoadingUniversities] = useState(false);
  const [isLoadingFaculties, setIsLoadingFaculties] = useState(false);

  // モーダルが開いたら大学一覧を取得
  useEffect(() => {
    if (isOpen) {
      loadUniversities();
    }
  }, [isOpen]);

  // 大学が選択されたら学部一覧を取得
  useEffect(() => {
    if (selectedUniversityId) {
      loadFaculties(selectedUniversityId);
    } else {
      setFaculties([]);
      setSelectedFacultyId("");
    }
  }, [selectedUniversityId]);

  const loadUniversities = async () => {
    setIsLoadingUniversities(true);
    try {
      const data = await getUniversities();
      setUniversities(data);
    } catch (error) {
      console.error("大学一覧の取得に失敗:", error);
      alert("大学一覧の取得に失敗しました");
    } finally {
      setIsLoadingUniversities(false);
    }
  };

  const loadFaculties = async (universityId: string) => {
    setIsLoadingFaculties(true);
    try {
      const data = await getFaculties(universityId);
      setFaculties(data);
    } catch (error) {
      console.error("学部一覧の取得に失敗:", error);
      alert("学部一覧の取得に失敗しました");
    } finally {
      setIsLoadingFaculties(false);
    }
  };

  const handleApply = () => {
    onApply(
      selectedUniversityId || null,
      selectedFacultyId || null
    );
    onClose();
  };

  const handleReset = () => {
    setSelectedUniversityId("");
    setSelectedFacultyId("");
    setUniversityKeyword("");
    setFacultyKeyword("");
    setFaculties([]);
  };

  // 大学一覧をキーワードでフィルタリング
  const filteredUniversities = universityKeyword.trim()
    ? universities.filter((university) =>
        university.name.toLowerCase().includes(universityKeyword.toLowerCase())
      )
    : universities;

  // 学部一覧をキーワードでフィルタリング
  const filteredFaculties = facultyKeyword.trim()
    ? faculties.filter((faculty) =>
        faculty.name.toLowerCase().includes(facultyKeyword.toLowerCase())
      )
    : faculties;

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
          <h2 className="mb-4 text-xl font-bold">詳細検索</h2>

          <div className="space-y-4">
            {/* 大学選択 */}
            <div>
              <label className="mb-2 block text-sm font-semibold text-gray-700">
                大学
              </label>
              {isLoadingUniversities ? (
                <div className="text-sm text-gray-500">読み込み中...</div>
              ) : (
                <>
                  <input
                    type="text"
                    value={universityKeyword}
                    onChange={(e) => setUniversityKeyword(e.target.value)}
                    placeholder="大学名で絞り込み..."
                    className="mb-2 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-blue-500 focus:outline-none"
                  />
                  <select
                    value={selectedUniversityId}
                    onChange={(e) => setSelectedUniversityId(e.target.value)}
                    className="w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
                  >
                    <option value="">大学を選択してください</option>
                    {filteredUniversities.map((university) => (
                      <option key={university.id} value={university.id}>
                        {university.name}
                      </option>
                    ))}
                  </select>
                </>
              )}
            </div>

            {/* 学部選択 */}
            {selectedUniversityId && (
              <div>
                <label className="mb-2 block text-sm font-semibold text-gray-700">
                  学部
                </label>
                {isLoadingFaculties ? (
                  <div className="text-sm text-gray-500">読み込み中...</div>
                ) : (
                  <>
                    <input
                      type="text"
                      value={facultyKeyword}
                      onChange={(e) => setFacultyKeyword(e.target.value)}
                      placeholder="学部名で絞り込み..."
                      className="mb-2 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-blue-500 focus:outline-none"
                    />
                    <select
                      value={selectedFacultyId}
                      onChange={(e) => setSelectedFacultyId(e.target.value)}
                      className="w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:outline-none"
                    >
                      <option value="">学部を選択してください</option>
                      {filteredFaculties.map((faculty) => (
                        <option key={faculty.id} value={faculty.id}>
                          {faculty.name}
                        </option>
                      ))}
                    </select>
                  </>
                )}
              </div>
            )}
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