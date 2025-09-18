import { getUniversities } from "@/services/universities";
import { getFaculties } from "@/services/faculties";
import RegisterForm from "./register-form";

export default async function Page() {
    // Server Componentで大学一覧を取得
    let universities: University[] = [];
    try {
        universities = await getUniversities();
        console.log("取得成功:", universities);
    } catch (error) {
        console.error("大学一覧取得エラー:", error);
        universities = [];
    }

    // Client から呼ばせたい処理は、関数内の先頭で `use server` を宣言する
    async function handleUniversityChange(universityId: string) {
        "use server";
        try {
            return await getFaculties(universityId);
        } catch (error) {
            console.error("学部一覧取得エラー:", error);
            return [];
        }
    }

    return (
        <RegisterForm
            initialUniversities={universities}
            onUniversityChange={handleUniversityChange}
        />
    );
}
