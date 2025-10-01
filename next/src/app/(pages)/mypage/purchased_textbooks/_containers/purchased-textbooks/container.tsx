import { fetchPurchasedTextbooks } from "@/app/(pages)/mypage/_lib/fetchPurchasedTextbooks";
import { PurchasedTextbooksPresenter } from "./presenter";

export async function PurchasedTextbooksContainer() {
	const textbooks = await fetchPurchasedTextbooks();

	return <PurchasedTextbooksPresenter textbooks={textbooks} />;
}