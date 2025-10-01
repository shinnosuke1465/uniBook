//大学
export type University = {
    id: string;
    name: string;
};

//ユーザー
export type User = {
    id: string;
    name: string;
    mail_address: string;
    post_code: string;
    address: string;
    image_id: number | null;
    university_name: string;
    faculty_name: string;
};