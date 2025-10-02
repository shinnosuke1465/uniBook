import { fetchDealRoomDetail } from "../../../../_lib/fetchDealRoomDetail";
import { DealRoomDetailPresentation } from "./presentational";

interface DealRoomDetailContainerProps {
  dealRoomId: string;
}

export async function DealRoomDetailContainer({
  dealRoomId,
}: DealRoomDetailContainerProps) {
  const dealRoom = await fetchDealRoomDetail(dealRoomId);

  return <DealRoomDetailPresentation dealRoom={dealRoom} />;
}
