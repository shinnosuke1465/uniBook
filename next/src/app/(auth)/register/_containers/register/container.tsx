import { RegisterPresentation } from "./presentational";
import type { University } from "../../_lib/fetchUniversities";
import type { Faculty } from "../../_lib/fetchFaculties";

interface RegisterContainerProps {
  universities: University[];
  onFetchFaculties: (universityId: string) => Promise<Faculty[]>;
  onRefreshUniversities: () => Promise<University[]>;
}

export function RegisterContainer({
  universities,
  onFetchFaculties,
  onRefreshUniversities,
}: RegisterContainerProps) {
  return (
    <RegisterPresentation
      universities={universities}
      onFetchFaculties={onFetchFaculties}
      onRefreshUniversities={onRefreshUniversities}
    />
  );
}
