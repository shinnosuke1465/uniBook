import { RegisterPresentation } from "./presentational";
import type { University } from "../../_lib/fetchUniversities";
import type { Faculty } from "../../_lib/fetchFaculties";

interface RegisterContainerProps {
  universities: University[];
  onFetchFaculties: (universityId: string) => Promise<Faculty[]>;
}

export function RegisterContainer({
  universities,
  onFetchFaculties,
}: RegisterContainerProps) {
  return (
    <RegisterPresentation
      universities={universities}
      onFetchFaculties={onFetchFaculties}
    />
  );
}
