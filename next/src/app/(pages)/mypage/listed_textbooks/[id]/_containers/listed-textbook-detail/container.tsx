import { fetchListedTextbookDetail } from "@/app/(pages)/mypage/_lib/fetchListedTextbookDetail";
import { ListedTextbookDetailPresenter } from "./presenter";

type ListedTextbookDetailContainerProps = {
	textbookId: string;
};

export async function ListedTextbookDetailContainer({
	textbookId,
}: ListedTextbookDetailContainerProps) {
	const textbook = await fetchListedTextbookDetail(textbookId);

	return <ListedTextbookDetailPresenter textbook={textbook} />;
}