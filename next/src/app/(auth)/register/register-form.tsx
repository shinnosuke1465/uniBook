"use client"

import { useState } from "react"
import { register } from "@/services/auth/register";
import { useRouter } from "next/navigation";

interface University {
  id: string;
  name: string;
}

interface Faculty {
  id: string;
  name: string;
  university_id: string;
}

interface RegisterFormProps {
  initialUniversities: University[];
  onUniversityChange: (universityId: string) => Promise<Faculty[]>;
}

export default function RegisterForm({ initialUniversities, onUniversityChange }: RegisterFormProps) {
    const router = useRouter();
    const [error, setError] = useState<string | null>(null);

    // 入力状態管理
    const [userName, setUserName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [postCode, setPostCode] = useState('');
    const [address, setAddress] = useState('');
    const [facultyId, setFacultyId] = useState('');
    const [universityId, setUniversityId] = useState('');

    // 学部データ管理
    const [faculties, setFaculties] = useState<Faculty[]>([]);
    const [loadingFaculties, setLoadingFaculties] = useState(false);

    // 大学選択変更時の処理
    const handleUniversityChange = async (e: React.ChangeEvent<HTMLSelectElement>) => {
        const selectedUniversityId = e.target.value;
        setUniversityId(selectedUniversityId);
        setFacultyId(''); // 大学変更時は学部選択をリセット

        if (!selectedUniversityId) {
            setFaculties([]);
            return;
        }

        setLoadingFaculties(true);
        try {
            const facultiesData = await onUniversityChange(selectedUniversityId);
            setFaculties(facultiesData);
        } catch (err) {
            setError('学部一覧の取得に失敗しました');
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

                // 会員登録成功後、教科書一覧ページへリダイレクト
                router.push('/textbook');
            } catch (e) {
                setError((e as Error).message);
            }
        } else {
            setError("パスワード入力が異なります");
        }
    };

    return (
        <form action={tryRegister} className="space-y-4">
            {/* ユーザー名 */}
            <div>
                <label htmlFor="user_name">User name</label>
                <input
                    name="name"
                    id="user_name"
                    type="text"
                    className="block mt-1 bg-gray-100 text-gray-700"
                    onChange={(e) => setUserName(e.target.value)}
                    value={userName}
                    required
                />
            </div>

            {/* メールアドレス */}
            <div>
                <label htmlFor="email">Email</label>
                <input
                    name="mail_address"
                    id="email"
                    type="email"
                    className="block mt-1 bg-gray-100 text-gray-700"
                    onChange={(e) => setEmail(e.target.value)}
                    value={email}
                    required
                />
            </div>

            {/* パスワード */}
            <div>
                <label htmlFor="password">Password</label>
                <input
                    name="password"
                    id="password"
                    type="password"
                    className="block mt-1 bg-gray-100 text-gray-700"
                    onChange={(e) => setPassword(e.target.value)}
                    value={password}
                    required
                    autoComplete="current-password"
                />
            </div>

            {/* パスワード確認 */}
            <div>
                <label htmlFor="confirm_password">Confirm password</label>
                <input
                    name="confirm_password"
                    id="confirm_password"
                    type="password"
                    className="block mt-1 bg-gray-100 text-gray-700"
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    value={confirmPassword}
                    required
                    autoComplete="current-password"
                />
            </div>

            {/* 郵便番号 */}
            <div>
                <label htmlFor="post_code">Post code</label>
                <input
                    name="post_code"
                    id="post_code"
                    type="text"
                    className="block mt-1 bg-gray-100 text-gray-700"
                    onChange={(e) => setPostCode(e.target.value)}
                    value={postCode}
                    required
                />
            </div>

            {/* 住所 */}
            <div>
                <label htmlFor="address">Address</label>
                <input
                    name="address"
                    id="address"
                    type="text"
                    className="block mt-1 bg-gray-100 text-gray-700"
                    onChange={(e) => setAddress(e.target.value)}
                    value={address}
                    required
                />
            </div>

            {/* 大学選択 */}
            <div>
                <label htmlFor="university_id">University</label>
                <select
                    name="university_id"
                    id="university_id"
                    className="block mt-1 bg-gray-100 text-gray-700"
                    onChange={handleUniversityChange}
                    value={universityId}
                    required
                >
                    <option value="">大学を選択してください</option>
                    {initialUniversities.map((university) => (
                        <option key={university.id} value={university.id}>
                            {university.name}
                        </option>
                    ))}
                </select>
            </div>

            {/* 学部選択 */}
            <div>
                <label htmlFor="faculty_id">Faculty</label>
                <select
                    name="faculty_id"
                    id="faculty_id"
                    className={`block mt-1 bg-gray-100 text-gray-700 ${
                        !universityId ? 'opacity-50 cursor-not-allowed' : ''
                    }`}
                    onChange={(e) => setFacultyId(e.target.value)}
                    value={facultyId}
                    required
                    disabled={!universityId || loadingFaculties}
                >
                    <option value="">
                        {!universityId
                            ? '先に大学を選択してください'
                            : loadingFaculties
                                ? '読み込み中...'
                                : '学部を選択してください'
                        }
                    </option>
                    {faculties.map((faculty) => (
                        <option key={faculty.id} value={faculty.id}>
                            {faculty.name}
                        </option>
                    ))}
                </select>
            </div>

            {/* エラーメッセージ */}
            <div>
                {error && <p className="text-red-500">{error}</p>}
            </div>

            {/* 登録ボタン */}
            <div>
                <button type="submit" className="px-4 py-2 bg-blue-500 text-white rounded">
                    登録
                </button>
            </div>
        </form>
    );
}