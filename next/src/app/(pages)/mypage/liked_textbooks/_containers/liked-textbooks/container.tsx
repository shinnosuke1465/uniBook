import { fetchLikedTextbooks } from "@/app/(pages)/mypage/_lib/fetchLikedTextbooks";
import { LikedTextbooksPresenter } from "./presenter";

export async function LikedTextbooksContainer() {
	const textbooks = await fetchLikedTextbooks();

	return <LikedTextbooksPresenter textbooks={textbooks} />;
}