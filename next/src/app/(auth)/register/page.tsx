import { getUniversities } from "@/services/universities";
import { getFaculties } from "@/services/faculties";
import RegisterForm from "./register-form";

export default async function Page() {
    // Server Componentで大学一覧を取得
    const universities = await getUniversities().catch(() => []);
    console.log(universities);

    // Client から呼ばせたい処理は、関数内の先頭で `use server` を宣言する
    async function handleUniversityChange(universityId: string) {
        "use server";
        return getFaculties(universityId);
    }

    return (
        <RegisterForm
            initialUniversities={universities}
            onUniversityChange={handleUniversityChange}
        />
    );
}
