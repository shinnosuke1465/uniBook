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
		<header className="bg-white/80 backdrop-blur-md shadow-sm border-b border-blue-100 sticky top-0 z-50">
			<div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div className="flex justify-between items-center h-16">
					{/* ロゴ */}
					<Link href="/textbook" className="text-2xl font-bold bg-gradient-to-r from-blue-500 to-blue-400 bg-clip-text text-transparent hover:from-blue-600 hover:to-blue-500 transition-all">
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
									className="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-400 hover:from-blue-600 hover:to-blue-500 rounded-full shadow-md hover:shadow-lg transition-all duration-200"
								>
									出品する
								</Link>

								{/* いいねアイコン */}
								<Link
									href="/mypage/liked_textbooks"
									className="p-2.5 text-blue-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all duration-200"
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
									className="p-2.5 text-blue-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all duration-200"
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
										className="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-500 hover:from-blue-500 hover:to-blue-600 shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400"
									>
										<svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
										</svg>
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
											<div className="absolute right-0 mt-3 w-56 bg-white/95 backdrop-blur-md rounded-xl shadow-xl py-2 z-20 border border-blue-100">
												<Link
													href="/mypage/profile"
													className="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
													onClick={() => setIsDropdownOpen(false)}
												>
													プロフィール
												</Link>
												<Link
													href="/mypage/listed_textbooks"
													className="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
													onClick={() => setIsDropdownOpen(false)}
												>
													出品した商品一覧
												</Link>
												<Link
													href="/mypage/purchased_textbooks"
													className="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
													onClick={() => setIsDropdownOpen(false)}
												>
													購入した商品一覧
												</Link>
												<div className="my-1 border-t border-blue-100"></div>
												<button
													onClick={async () => {
														setIsDropdownOpen(false);
														await handleLogout();
													}}
													className="block w-full text-left px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors"
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
									className="px-5 py-2.5 text-sm font-semibold text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-full transition-all duration-200"
								>
									会員登録
								</Link>

								{/* ログインボタン */}
								<Link
									href="/login"
									className="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-400 hover:from-blue-600 hover:to-blue-500 rounded-full shadow-md hover:shadow-lg transition-all duration-200"
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
