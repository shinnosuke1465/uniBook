import type { PurchasedTextbook } from "@/types/response/responseType";
import { PurchasedTextbookCard } from "@/app/(pages)/mypage/_components/PurchasedTextbookCard";

type PurchasedTextbooksPresenterProps = {
	textbooks: PurchasedTextbook[];
};

export function PurchasedTextbooksPresenter({
	textbooks,
}: PurchasedTextbooksPresenterProps) {
	if (textbooks.length === 0) {
		return (
			<div className="text-center py-12">
				<p className="text-gray-500">購入した教科書はありません</p>
			</div>
		);
	}

	return (
		<div className="space-y-4">
			{textbooks.map((textbook) => (
				<PurchasedTextbookCard key={textbook.id} textbook={textbook} />
			))}
		</div>
	);
}