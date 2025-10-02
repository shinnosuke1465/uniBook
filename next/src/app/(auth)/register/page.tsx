import { RegisterContainer } from "./_containers/register";
import { fetchUniversities } from "./_lib/fetchUniversities";
import { fetchFaculties } from "./_lib/fetchFaculties";

export default async function RegisterPage() {
  const universities = await fetchUniversities();

  // Server Actionとして学部取得関数を定義
  async function handleFetchFaculties(universityId: string) {
    "use server";
    return await fetchFaculties(universityId);
  }

  return (
    <RegisterContainer
      universities={universities}
      onFetchFaculties={handleFetchFaculties}
    />
  );
}
