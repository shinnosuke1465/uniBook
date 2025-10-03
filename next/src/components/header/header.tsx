"use client";

import Link from "next/link";
import { useState } from "react";
import { logout } from "@/services/auth/logout";
import { useRouter } from "next/navigation";
import { useAuthContext } from "@/contexts/AuthContext";

export function Header() {
	const [isDropdownOpen, setIsDropdownOpen] = useState(false);
	const router = useRouter();
	const { authUser, isLoaded, refreshUser } = useAuthContext();

	const handleLogout = async () => {
		try {
			await logout();
			await refreshUser();
			router.push("/login");
		} catch (error) {
			console.error("ログアウトエラー:", error);
			alert("ログアウトに失敗しました");
		}
	};

	return (
		<header className="bg-white shadow-sm border-b border-gray-200">
			<div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div className="flex justify-between items-center h-16">
					{/* ロゴ */}
					<Link href="/textbook" className="text-xl font-bold text-gray-900">
						uniBook
					</Link>

					{/* 右側のナビゲーション */}
					<div className="flex items-center gap-4">
						{!isLoaded ? (
							<div className="w-24 h-8 bg-gray-200 animate-pulse rounded" />
						) : authUser ? (
							<>
								{/* 出品ボタン */}
								<Link
									href="/textbook/create"
									className="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
								>
									出品する
								</Link>

								{/* いいねアイコン */}
								<Link
									href="/mypage/liked_textbooks"
									className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors"
									title="いいね一覧"
								>
									<svg
										className="w-6 h-6"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
									>
										<path
											strokeLinecap="round"
											strokeLinejoin="round"
											strokeWidth={2}
											d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
										/>
									</svg>
								</Link>

								{/* 取引アイコン */}
								<Link
									href="/mypage/deal_rooms"
									className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors"
									title="取引一覧"
								>
									<svg
										className="w-6 h-6"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
									>
										<path
											strokeLinecap="round"
											strokeLinejoin="round"
											strokeWidth={2}
											d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
										/>
									</svg>
								</Link>

								{/* アバタードロップダウン */}
								<div className="relative">
									<button
										type="button"
										onClick={() => setIsDropdownOpen(!isDropdownOpen)}
										className="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
									>
										<span className="text-sm font-medium text-gray-700">U</span>
									</button>

									{/* ドロップダウンメニュー */}
									{isDropdownOpen && (
										<>
											{/* オーバーレイ */}
											<div
												className="fixed inset-0 z-10"
												onClick={() => setIsDropdownOpen(false)}
											/>
											{/* メニュー */}
											<div className="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 border border-gray-200">
												<Link
													href="/mypage/profile"
													className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
													onClick={() => setIsDropdownOpen(false)}
												>
													プロフィール
												</Link>
												<Link
													href="/mypage/listed_textbooks"
													className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
													onClick={() => setIsDropdownOpen(false)}
												>
													出品した商品一覧
												</Link>
												<Link
													href="/mypage/purchased_textbooks"
													className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
													onClick={() => setIsDropdownOpen(false)}
												>
													購入した商品一覧
												</Link>
												<button
													onClick={async () => {
														setIsDropdownOpen(false);
														await handleLogout();
													}}
													className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
												>
													ログアウト
												</button>
											</div>
										</>
									)}
								</div>
							</>
						) : (
							<>
								{/* 会員登録ボタン */}
								<Link
									href="/register"
									className="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
								>
									会員登録
								</Link>

								{/* ログインボタン */}
								<Link
									href="/login"
									className="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
								>
									ログイン
								</Link>
							</>
						)}
					</div>
				</div>
			</div>
		</header>
	);
}
