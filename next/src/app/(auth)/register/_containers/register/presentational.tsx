"use client";

import { useState } from "react";
import { register } from "@/services/auth/register";
import { useRouter } from "next/navigation";
import { useAuthContext } from "@/contexts/AuthContext";
import type { University } from "../../_lib/fetchUniversities";
import type { Faculty } from "../../_lib/fetchFaculties";

interface RegisterPresentationProps {
  universities: University[];
  onFetchFaculties: (universityId: string) => Promise<Faculty[]>;
}

export function RegisterPresentation({
  universities,
  onFetchFaculties,
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

  // 大学選択変更時の処理
  const handleUniversityChange = async (
    e: React.ChangeEvent<HTMLSelectElement>
  ) => {
    const selectedUniversityId = e.target.value;
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

  const tryRegister = async (data: FormData) => {
    const pas1 = data.get("password");
    const pas2 = data.get("confirm_password");
    if (pas1 === pas2) {
      try {
        const token = await register(data);
        console.log(token);

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

        <form action={tryRegister} className="mt-8 space-y-6">
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
                {universities.map((university) => (
                  <option key={university.id} value={university.id}>
                    {university.name}
                  </option>
                ))}
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
                onChange={(e) => setFacultyId(e.target.value)}
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
    </div>
  );
}
