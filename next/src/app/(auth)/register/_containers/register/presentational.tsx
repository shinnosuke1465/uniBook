"use client";

import { useState } from "react";
import { register } from "@/services/auth/register";
import { useRouter } from "next/navigation";
import { useAuthContext } from "@/contexts/AuthContext";
import type { University } from "../../_lib/fetchUniversities";
import type { Faculty } from "../../_lib/fetchFaculties";
import { createUniversity } from "@/services/info/university";
import { createFaculty } from "@/services/info/faculty";

interface RegisterPresentationProps {
  universities: University[];
  onFetchFaculties: (universityId: string) => Promise<Faculty[]>;
  onRefreshUniversities: () => Promise<University[]>;
}

export function RegisterPresentation({
  universities,
  onFetchFaculties,
  onRefreshUniversities,
}: RegisterPresentationProps) {
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);
  const { refreshUser } = useAuthContext();

  // 入力状態管理
  const [userName, setUserName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [postCode, setPostCode] = useState("");
  const [address, setAddress] = useState("");
  const [facultyId, setFacultyId] = useState("");
  const [universityId, setUniversityId] = useState("");

  // 学部データ管理
  const [faculties, setFaculties] = useState<Faculty[]>([]);
  const [loadingFaculties, setLoadingFaculties] = useState(false);

  // モーダル管理
  const [showUniversityModal, setShowUniversityModal] = useState(false);
  const [showFacultyModal, setShowFacultyModal] = useState(false);
  const [newUniversityName, setNewUniversityName] = useState("");
  const [newFacultyName, setNewFacultyName] = useState("");
  const [isCreating, setIsCreating] = useState(false);

  // 大学リスト管理（作成後に追加するため）
  const [universitiesList, setUniversitiesList] = useState<University[]>(universities);

  // 大学選択変更時の処理
  const handleUniversityChange = async (
    e: React.ChangeEvent<HTMLSelectElement>
  ) => {
    const selectedUniversityId = e.target.value;

    // 「該当なし」が選択された場合
    if (selectedUniversityId === "create_new") {
      setShowUniversityModal(true);
      return;
    }

    setUniversityId(selectedUniversityId);
    setFacultyId(""); // 大学変更時は学部選択をリセット

    if (!selectedUniversityId) {
      setFaculties([]);
      return;
    }

    setLoadingFaculties(true);
    try {
      const facultiesData = await onFetchFaculties(selectedUniversityId);
      setFaculties(facultiesData);
    } catch (err) {
      setError("学部一覧の取得に失敗しました");
      setFaculties([]);
    } finally {
      setLoadingFaculties(false);
    }
  };

  // 学部選択変更時の処理
  const handleFacultyChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const selectedFacultyId = e.target.value;

    // 「該当なし」が選択された場合
    if (selectedFacultyId === "create_new") {
      setShowFacultyModal(true);
      return;
    }

    setFacultyId(selectedFacultyId);
  };

  // 大学のみを作成
  const handleCreateUniversity = async () => {
    if (!newUniversityName.trim()) {
      setError("大学名を入力してください");
      return;
    }

    setIsCreating(true);
    setError(null);

    try {
      console.log("大学作成開始:", newUniversityName);
      await createUniversity({ name: newUniversityName });
      console.log("大学作成完了");

      // 大学一覧を再取得
      const refreshedUniversities = await onRefreshUniversities();
      setUniversitiesList(refreshedUniversities);

      // モーダルを閉じる
      setShowUniversityModal(false);
      setNewUniversityName("");

      console.log("大学作成処理完了");
    } catch (err) {
      console.error("大学作成エラー:", err);
      setError(`大学の作成に失敗しました: ${(err as Error).message}`);
    } finally {
      setIsCreating(false);
    }
  };

  // 学部のみを作成
  const handleCreateFaculty = async () => {
    if (!newFacultyName.trim()) {
      setError("学部名を入力してください");
      return;
    }

    if (!universityId) {
      setError("先に大学を選択してください");
      return;
    }

    setIsCreating(true);
    setError(null);

    try {
      console.log("学部作成開始:", newFacultyName, "大学ID:", universityId);
      await createFaculty({
        name: newFacultyName,
        university_id: universityId,
      });
      console.log("学部作成完了");

      // 学部一覧を再取得
      const refreshedFaculties = await onFetchFaculties(universityId);
      setFaculties(refreshedFaculties);

      // モーダルを閉じる
      setShowFacultyModal(false);
      setNewFacultyName("");

      console.log("学部作成処理完了");
    } catch (err) {
      console.error("学部作成エラー:", err);
      setError(`学部の作成に失敗しました: ${(err as Error).message}`);
    } finally {
      setIsCreating(false);
    }
  };

  const tryRegister = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    const pas1 = formData.get("password");
    const pas2 = formData.get("confirm_password");
    if (pas1 === pas2) {
      try {
        await register(formData);

        // 会員登録成功後、認証状態を更新してから教科書一覧ページへリダイレクト
        await refreshUser();
        router.push("/textbook");
      } catch (e) {
        setError((e as Error).message);
      }
    } else {
      setError("パスワード入力が異なります");
    }
  };

  return (
    <div className="container mx-auto flex min-h-screen items-center justify-center px-4 py-12">
      <div className="w-full max-w-md space-y-8">
        <div className="text-center">
          <h2 className="text-3xl font-bold">新規登録</h2>
          <p className="mt-2 text-sm text-gray-600">
            アカウントを作成してください
          </p>
        </div>

        <form onSubmit={tryRegister} className="mt-8 space-y-6">
          <div className="space-y-4">
            {/* ユーザー名 */}
            <div>
              <label
                htmlFor="user_name"
                className="block text-sm font-medium text-gray-700"
              >
                ユーザー名
              </label>
              <input
                name="name"
                id="user_name"
                type="text"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                onChange={(e) => setUserName(e.target.value)}
                value={userName}
                required
                autoComplete="off"
              />
            </div>

            {/* メールアドレス */}
            <div>
              <label
                htmlFor="email"
                className="block text-sm font-medium text-gray-700"
              >
                メールアドレス
              </label>
              <input
                name="mail_address"
                id="email"
                type="email"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                onChange={(e) => setEmail(e.target.value)}
                value={email}
                required
                autoComplete="off"
              />
            </div>

            {/* パスワード */}
            <div>
              <label
                htmlFor="password"
                className="block text-sm font-medium text-gray-700"
              >
                パスワード
              </label>
              <input
                name="password"
                id="password"
                type="password"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                onChange={(e) => setPassword(e.target.value)}
                value={password}
                required
                autoComplete="new-password"
              />
            </div>

            {/* パスワード確認 */}
            <div>
              <label
                htmlFor="confirm_password"
                className="block text-sm font-medium text-gray-700"
              >
                パスワード（確認）
              </label>
              <input
                name="confirm_password"
                id="confirm_password"
                type="password"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                onChange={(e) => setConfirmPassword(e.target.value)}
                value={confirmPassword}
                required
                autoComplete="new-password"
              />
            </div>

            {/* 郵便番号 */}
            <div>
              <label
                htmlFor="post_code"
                className="block text-sm font-medium text-gray-700"
              >
                郵便番号
              </label>
              <input
                name="post_code"
                id="post_code"
                type="text"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                onChange={(e) => setPostCode(e.target.value)}
                value={postCode}
                required
                autoComplete="off"
              />
            </div>

            {/* 住所 */}
            <div>
              <label
                htmlFor="address"
                className="block text-sm font-medium text-gray-700"
              >
                住所
              </label>
              <input
                name="address"
                id="address"
                type="text"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                onChange={(e) => setAddress(e.target.value)}
                value={address}
                required
                autoComplete="off"
              />
            </div>

            {/* 大学選択 */}
            <div>
              <label
                htmlFor="university_id"
                className="block text-sm font-medium text-gray-700"
              >
                大学
              </label>
              <select
                name="university_id"
                id="university_id"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                onChange={handleUniversityChange}
                value={universityId}
                required
              >
                <option value="">大学を選択してください</option>
                {universitiesList.map((university) => (
                  <option key={university.id} value={university.id}>
                    {university.name}
                  </option>
                ))}
                <option value="create_new" className="font-semibold text-blue-600">
                  + 大学が見つからない場合
                </option>
              </select>
            </div>

            {/* 学部選択 */}
            <div>
              <label
                htmlFor="faculty_id"
                className="block text-sm font-medium text-gray-700"
              >
                学部
              </label>
              <select
                name="faculty_id"
                id="faculty_id"
                className={`mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 ${
                  !universityId ? "cursor-not-allowed opacity-50" : ""
                }`}
                onChange={handleFacultyChange}
                value={facultyId}
                required
                disabled={!universityId || loadingFaculties}
              >
                <option value="">
                  {!universityId
                    ? "先に大学を選択してください"
                    : loadingFaculties
                      ? "読み込み中..."
                      : "学部を選択してください"}
                </option>
                {faculties.map((faculty) => (
                  <option key={faculty.id} value={faculty.id}>
                    {faculty.name}
                  </option>
                ))}
                {universityId && !loadingFaculties && (
                  <option value="create_new" className="font-semibold text-blue-600">
                    + 学部が見つからない場合
                  </option>
                )}
              </select>
            </div>
          </div>

          {/* エラーメッセージ */}
          {error && (
            <div className="rounded-md bg-red-50 p-4">
              <p className="text-sm text-red-600">{error}</p>
            </div>
          )}

          {/* 登録ボタン */}
          <button
            type="submit"
            className="w-full rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            登録
          </button>
        </form>
      </div>

      {/* 大学作成モーダル */}
      {showUniversityModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center" style={{ backgroundColor: "rgba(0, 0, 0, 0.3)" }}>
          <div className="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
            <h3 className="mb-4 text-xl font-bold">大学を登録</h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">
                  大学名
                </label>
                <input
                  type="text"
                  value={newUniversityName}
                  onChange={(e) => setNewUniversityName(e.target.value)}
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                  placeholder="例: 東京大学"
                />
              </div>
            </div>
            <div className="mt-6 flex space-x-3">
              <button
                onClick={() => {
                  setShowUniversityModal(false);
                  setNewUniversityName("");
                }}
                className="flex-1 rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                disabled={isCreating}
              >
                キャンセル
              </button>
              <button
                onClick={handleCreateUniversity}
                className="flex-1 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300"
                disabled={isCreating}
              >
                {isCreating ? "作成中..." : "登録"}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* 学部のみ作成モーダル */}
      {showFacultyModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center" style={{ backgroundColor: "rgba(0, 0, 0, 0.3)" }}>
          <div className="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
            <h3 className="mb-4 text-xl font-bold">学部を登録</h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">
                  学部名
                </label>
                <input
                  type="text"
                  value={newFacultyName}
                  onChange={(e) => setNewFacultyName(e.target.value)}
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                  placeholder="例: 工学部"
                />
              </div>
            </div>
            <div className="mt-6 flex space-x-3">
              <button
                onClick={() => {
                  setShowFacultyModal(false);
                  setNewFacultyName("");
                }}
                className="flex-1 rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                disabled={isCreating}
              >
                キャンセル
              </button>
              <button
                onClick={handleCreateFaculty}
                className="flex-1 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300"
                disabled={isCreating}
              >
                {isCreating ? "作成中..." : "登録"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
