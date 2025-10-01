import { PurchasedTextbookDetailContainer } from "./_containers/purchased-textbook-detail";

type PurchasedTextbookDetailPageProps = {
	params: Promise<{ id: string }>;
};

export default async function PurchasedTextbookDetailPage({
	params,
}: PurchasedTextbookDetailPageProps) {
	const { id } = await params;

	return <PurchasedTextbookDetailContainer textbookId={id} />;
}