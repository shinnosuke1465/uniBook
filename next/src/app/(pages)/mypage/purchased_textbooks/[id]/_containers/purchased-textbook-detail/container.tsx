import { fetchPurchasedTextbookDetail } from "@/app/(pages)/mypage/_lib/fetchPurchasedTextbookDetail";
import { PurchasedTextbookDetailPresenter } from "./presenter";

type PurchasedTextbookDetailContainerProps = {
	textbookId: string;
};

export async function PurchasedTextbookDetailContainer({
	textbookId,
}: PurchasedTextbookDetailContainerProps) {
	const textbook = await fetchPurchasedTextbookDetail(textbookId);

	return <PurchasedTextbookDetailPresenter textbook={textbook} />;
}