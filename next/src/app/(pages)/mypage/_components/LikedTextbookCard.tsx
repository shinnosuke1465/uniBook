import type { LikedTextbook } from "@/types/response/responseType";

type LikedTextbookCardProps = {
	textbook: LikedTextbook;
};

export function LikedTextbookCard({ textbook }: LikedTextbookCardProps) {
	return (
		<div className="block bg-white rounded-lg shadow hover:shadow-md transition-shadow p-4">
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
						<span className="text-sm text-gray-600">
							{textbook.university_name} / {textbook.faculty_name}
						</span>
					</div>
					<div className="mt-2 flex items-center gap-2">
						<span
							className={`px-2 py-1 text-xs font-medium rounded ${
								textbook.deal.is_purchasable
									? "bg-green-100 text-green-800"
									: "bg-gray-100 text-gray-800"
							}`}
						>
							{textbook.deal.is_purchasable ? "購入可能" : "売却済み"}
						</span>
						<span className="text-xs text-gray-600">
							出品者: {textbook.deal.seller_info.nickname}
						</span>
					</div>
				</div>
			</div>
		</div>
	);
}