import { ListedTextbookDetailContainer } from "./_containers/listed-textbook-detail";

type ListedTextbookDetailPageProps = {
	params: Promise<{ id: string }>;
};

export default async function ListedTextbookDetailPage({
	params,
}: ListedTextbookDetailPageProps) {
	const { id } = await params;

	return <ListedTextbookDetailContainer textbookId={id} />;
}