import type { ListedTextbook } from "@/types/response/responseType";
import Link from "next/link";

type ListedTextbookDetailPresenterProps = {
	textbook: ListedTextbook;
};

export function ListedTextbookDetailPresenter({
	textbook,
}: ListedTextbookDetailPresenterProps) {
	return (
		<div className="space-y-6">
			{/* パンくずリスト */}
			<nav className="flex items-center gap-2 text-sm text-gray-600">
				<Link href="/mypage/listed_textbooks" className="hover:text-gray-900">
					出品した教科書
				</Link>
				<span>/</span>
				<span className="text-gray-900">{textbook.name}</span>
			</nav>

			{/* メイン情報 */}
			<div className="bg-white rounded-lg shadow p-6">
				<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
					{/* 画像 */}
					<div>
						{textbook.image_url ? (
							<img
								src={textbook.image_url}
								alt={textbook.name}
								className="w-full rounded-lg"
							/>
						) : (
							<div className="w-full aspect-square bg-gray-200 rounded-lg flex items-center justify-center">
								<span className="text-gray-400">No Image</span>
							</div>
						)}
						{textbook.image_urls.length > 1 && (
							<div className="grid grid-cols-4 gap-2 mt-2">
								{textbook.image_urls.slice(1).map((url, index) => (
									<img
										key={index}
										src={url}
										alt={`${textbook.name} ${index + 2}`}
										className="w-full aspect-square object-cover rounded"
									/>
								))}
							</div>
						)}
					</div>

					{/* 詳細情報 */}
					<div className="space-y-4">
						<div>
							<h1 className="text-2xl font-bold text-gray-900">
								{textbook.name}
							</h1>
							<div className="mt-2">
								<span
									className={`inline-block px-3 py-1 text-sm font-medium rounded ${
										textbook.deal.status === "listing"
											? "bg-green-100 text-green-800"
											: "bg-gray-100 text-gray-800"
									}`}
								>
									{textbook.deal.status === "listing" ? "出品中" : "売却済み"}
								</span>
							</div>
						</div>

						<div>
							<span className="text-3xl font-bold text-gray-900">
								¥{textbook.price.toLocaleString()}
							</span>
						</div>

						<div>
							<h2 className="text-sm font-medium text-gray-700 mb-2">説明</h2>
							<p className="text-gray-600 whitespace-pre-wrap">
								{textbook.description}
							</p>
						</div>

						<div>
							<h2 className="text-sm font-medium text-gray-700 mb-2">
								出品者情報
							</h2>
							<div className="space-y-1 text-sm text-gray-600">
								<p>名前: {textbook.deal.seller_info.nickname}</p>
								<p>大学: {textbook.deal.seller_info.university_name}</p>
								<p>学部: {textbook.deal.seller_info.faculty_name}</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			{/* 購入者情報（売却済みの場合のみ） */}
			{textbook.deal.buyer_shipping_info && (
				<div className="bg-white rounded-lg shadow p-6">
					<h2 className="text-lg font-semibold text-gray-900 mb-4">
						購入者情報
					</h2>
					<div className="space-y-2 text-sm">
						<div>
							<span className="font-medium text-gray-700">名前:</span>
							<span className="ml-2 text-gray-600">
								{textbook.deal.buyer_shipping_info.name}
							</span>
						</div>
						<div>
							<span className="font-medium text-gray-700">郵便番号:</span>
							<span className="ml-2 text-gray-600">
								{textbook.deal.buyer_shipping_info.postal_code}
							</span>
						</div>
						<div>
							<span className="font-medium text-gray-700">住所:</span>
							<span className="ml-2 text-gray-600">
								{textbook.deal.buyer_shipping_info.address}
							</span>
						</div>
					</div>
				</div>
			)}

			{/* 取引履歴 */}
			<div className="bg-white rounded-lg shadow p-6">
				<h2 className="text-lg font-semibold text-gray-900 mb-4">取引履歴</h2>
				<div className="space-y-2">
					{textbook.deal.deal_events.map((event) => (
						<div
							key={event.id}
							className="flex items-center gap-2 text-sm text-gray-600"
						>
							<span className="w-2 h-2 bg-blue-500 rounded-full"></span>
							<span>
								{event.actor_type === "seller" ? "出品者" : "購入者"} -{" "}
								{event.event_type}
							</span>
						</div>
					))}
				</div>
			</div>
		</div>
	);
}