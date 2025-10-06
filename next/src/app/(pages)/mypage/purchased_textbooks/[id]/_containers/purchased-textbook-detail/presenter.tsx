import type { PurchasedTextbook } from "@/types/response/responseType";
import { DealEventList } from "@/app/(pages)/mypage/_components/DealEventList";
import Link from "next/link";
import { ImageFrame } from "@/components/image/image-frame";
import {
	LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL,
	S3_DEFAULT_TEXTBOOK_IMAGE_URL,
} from "@/constants";

type PurchasedTextbookDetailPresenterProps = {
	textbook: PurchasedTextbook;
};

export function PurchasedTextbookDetailPresenter({
	textbook,
}: PurchasedTextbookDetailPresenterProps) {
	return (
		<div className="space-y-6">
			{/* パンくずリスト */}
			<nav className="flex items-center gap-2 text-sm text-gray-600">
				<Link
					href="/mypage/purchased_textbooks"
					className="hover:text-gray-900"
				>
					購入した教科書
				</Link>
				<span>/</span>
				<span className="text-gray-900">{textbook.name}</span>
			</nav>

			{/* メイン情報 */}
			<div className="bg-white rounded-lg shadow p-6">
				<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
					{/* 画像 */}
					<div>
						{textbook.image_urls.length > 0 ? (
							<ImageFrame
								path={textbook.image_urls[0]}
								alt={textbook.name}
								width={600}
								height={600}
								className="w-full rounded-lg"
							/>
						) : process.env.NODE_ENV === "production" ? (
							<ImageFrame
								path={S3_DEFAULT_TEXTBOOK_IMAGE_URL}
								alt="デフォルト画像"
								width={600}
								height={600}
								className="w-full rounded-lg"
							/>
						) : (
							<ImageFrame
								path={LOCAL_DEFAULT_TEXTBOOK_IMAGE_URL}
								alt="デフォルト画像"
								width={600}
								height={600}
								className="w-full rounded-lg"
							/>
						)}
						{textbook.image_urls.length > 1 && (
							<div className="grid grid-cols-4 gap-2 mt-2">
								{textbook.image_urls.slice(1).map((url, index) => (
									<ImageFrame
										key={index}
										path={url}
										alt={`${textbook.name} ${index + 2}`}
										width={150}
										height={150}
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
										textbook.deal.status === "completed"
											? "bg-gray-100 text-gray-800"
											: "bg-blue-100 text-blue-800"
									}`}
								>
									{textbook.deal.status === "completed" ? "取引完了" : "取引中"}
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

			{/* 配送先情報（取引完了の場合のみ） */}
			{textbook.deal.buyer_shipping_info && (
				<div className="bg-white rounded-lg shadow p-6">
					<h2 className="text-lg font-semibold text-gray-900 mb-4">
						配送先情報
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
			<DealEventList
				events={textbook.deal.deal_events}
				sellerName={textbook.deal.seller_info.nickname}
				buyerName={textbook.deal.buyer_shipping_info?.nickname || "購入者"}
			/>
		</div>
	);
}