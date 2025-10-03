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
    profile_image_url: string | null;
    university_name: string;
    faculty_name: string;
};

//出品した教科書
export type ListedTextbook = {
    id: string;
    name: string;
    description: string;
    image_url: string | null;
    image_urls: string[];
    price: number;
    deal: {
        id: string;
        is_purchasable: boolean;
        seller_info: {
            id: string;
            nickname: string;
            profile_image_url: number | null;
            university_name: string;
            faculty_name: string;
        };
        status: string;
        deal_events: {
            id: string;
            actor_type: string;
            event_type: string;
            created_at: string | null;
        }[];
        buyer_shipping_info: {
            id: string;
            name: string;
            postal_code: string;
            address: string;
            nickname: string;
            profile_image_url: number | null;
        } | null;
    };
};

//購入した教科書
export type PurchasedTextbook = {
    id: string;
    name: string;
    description: string;
    image_url: string | null;
    image_urls: string[];
    price: number;
    deal: {
        id: string;
        is_purchasable: boolean;
        seller_info: {
            id: string;
            nickname: string;
            profile_image_url: number | null;
            university_name: string;
            faculty_name: string;
        };
        status: string;
        deal_events: {
            id: string;
            actor_type: string;
            event_type: string;
            created_at: string | null;
        }[];
        buyer_shipping_info: {
            id: string;
            name: string;
            postal_code: string;
            address: string;
            nickname: string;
            profile_image_url: number | null;
        } | null;
    };
};

//いいねした教科書
export type LikedTextbook = {
    id: string;
    name: string;
    price: number;
    description: string;
    image_url: string | null;
    image_urls: string[];
    university_name: string;
    faculty_name: string;
    condition_type: string;
    deal: {
        id: string;
        is_purchasable: boolean;
        seller_info: {
            id: string;
            nickname: string;
            profile_image_url: number | null;
        };
    };
    comments: {
        id: string;
        text: string;
        created_at: string;
        user: {
            id: string;
            name: string;
            profile_image_url: number | null;
        };
    }[];
    is_liked: boolean;
};