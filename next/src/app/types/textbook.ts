export interface SellerInfo {
  id: string;
  nickname: string;
  profile_image_url: string | null;
}

export interface Deal {
  id: string;
  is_purchasable: boolean;
  seller_info: SellerInfo;
  user: any;
}

export interface CommentUser {
  id: string;
  name: string;
  profile_image_url: string | null;
}

export interface Comment {
  id: string;
  text: string;
  created_at: string;
  user: CommentUser;
}

export interface Textbook {
  id: string;
  name: string;
  price: number;
  description: string;
  condition_type: "new" | "near_new" | "no_damage" | "slight_damage" | "damage" | "poor_condition";
  university_id: string;
  university_name: string;
  faculty_id: string;
  faculty_name: string;
  image_urls: string[];
  deal: Deal | null;
  comments: Comment[];
  is_liked: boolean;
}

export interface TextbooksResponse {
  textbooks: Textbook[];
}