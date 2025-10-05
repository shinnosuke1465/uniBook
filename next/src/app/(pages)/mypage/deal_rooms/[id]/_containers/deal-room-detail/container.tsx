import { fetchDealRoomDetail } from "../../../../_lib/fetchDealRoomDetail";
import { getUserData } from "@/services/auth/getUserData";
import { DealRoomDetailPresentation } from "./presentational";

interface DealRoomDetailContainerProps {
  dealRoomId: string;
}

export async function DealRoomDetailContainer({
  dealRoomId,
}: DealRoomDetailContainerProps) {
  const dealRoom = await fetchDealRoomDetail(dealRoomId);
  const userData = await getUserData();

  return (
    <DealRoomDetailPresentation
      dealRoom={dealRoom}
      currentUserId={userData.id}
    />
  );
}
