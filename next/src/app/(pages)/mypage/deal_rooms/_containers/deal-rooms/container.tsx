import { fetchDealRooms } from "../../../_lib/fetchDealRooms";
import { DealRoomsPresentation } from "./presentational";

export async function DealRoomsContainer() {
  const dealRooms = await fetchDealRooms();

  return <DealRoomsPresentation dealRooms={dealRooms} />;
}
