import type { ListedTextbook } from "@/types/response/responseType";
import { ListedTextbookCard } from "@/app/(pages)/mypage/_components/ListedTextbookCard";

type ListedTextbooksPresenterProps = {
	textbooks: ListedTextbook[];
};

export function ListedTextbooksPresenter({
	textbooks,
}: ListedTextbooksPresenterProps) {
	if (textbooks.length === 0) {
		return (
			<div className="text-center py-12">
				<p className="text-gray-500">出品した教科書はありません</p>
			</div>
		);
	}

	return (
		<div className="space-y-4">
			{textbooks.map((textbook) => (
				<ListedTextbookCard key={textbook.id} textbook={textbook} />
			))}
		</div>
	);
}