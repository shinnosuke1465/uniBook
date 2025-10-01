import { fetchListedTextbooks } from "@/app/(pages)/mypage/_lib/fetchListedTextbooks";
import { ListedTextbooksPresenter } from "./presenter";

export async function ListedTextbooksContainer() {
	const textbooks = await fetchListedTextbooks();

	return <ListedTextbooksPresenter textbooks={textbooks} />;
}