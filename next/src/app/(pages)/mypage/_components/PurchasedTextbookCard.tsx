import type { PurchasedTextbook } from "@/types/response/responseType";
import Link from "next/link";

type PurchasedTextbookCardProps = {
	textbook: PurchasedTextbook;
};

export function PurchasedTextbookCard({
	textbook,
}: PurchasedTextbookCardProps) {
	return (
		<Link
			href={`/mypage/purchased_textbooks/${textbook.id}`}
			className="block bg-white rounded-lg shadow hover:shadow-md transition-shadow p-4"
		>
			<div className="flex gap-4">
				{textbook.image_url ? (
					<img
						src={textbook.image_url}
						alt={textbook.name}
						className="w-24 h-24 object-cover rounded"
					/>
				) : (
					<div className="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
						<span className="text-gray-400 text-sm">No Image</span>
					</div>
				)}
				<div className="flex-1">
					<h3 className="text-lg font-semibold text-gray-900">
						{textbook.name}
					</h3>
					<p className="text-sm text-gray-600 mt-1 line-clamp-2">
						{textbook.description}
					</p>
					<div className="mt-2 flex items-center gap-4">
						<span className="text-lg font-bold text-gray-900">
							¥{textbook.price.toLocaleString()}
						</span>
						<span
							className={`px-2 py-1 text-xs font-medium rounded ${
								textbook.deal.status === "completed"
									? "bg-gray-100 text-gray-800"
									: "bg-blue-100 text-blue-800"
							}`}
						>
							{textbook.deal.status === "completed" ? "取引完了" : "取引中"}
						</span>
					</div>
				</div>
			</div>
		</Link>
	);
}
