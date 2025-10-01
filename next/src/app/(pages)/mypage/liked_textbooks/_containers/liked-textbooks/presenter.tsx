import type { LikedTextbook } from "@/types/response/responseType";
import { LikedTextbookCard } from "@/app/(pages)/mypage/_components/LikedTextbookCard";

type LikedTextbooksPresenterProps = {
	textbooks: LikedTextbook[];
};

export function LikedTextbooksPresenter({
	textbooks,
}: LikedTextbooksPresenterProps) {
	if (textbooks.length === 0) {
		return (
			<div className="text-center py-12">
				<p className="text-gray-500">いいねした教科書はありません</p>
			</div>
		);
	}

	return (
		<div className="space-y-4">
			{textbooks.map((textbook) => (
				<LikedTextbookCard key={textbook.id} textbook={textbook} />
			))}
		</div>
	);
}