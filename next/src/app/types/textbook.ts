export interface Deal {
  id: string;
  status: string;
  buyer_id: string;
  seller_id: string;
  created_at: string;
  updated_at: string;
}

export interface Comment {
  id: string;
  content: string;
  user_id: string;
  user_name: string;
  created_at: string;
}

export interface Textbook {
  id: string;
  name: string;
  price: number;
  description: string;
  condition_type: "new" | "like_new" | "good" | "fair" | "poor";
  university_name: string;
  faculty_name: string;
  image_ids: string[];
  deal: Deal | null;
  comments: Comment[];
  is_liked: boolean;
}

export interface TextbooksResponse {
  textbooks: Textbook[];
}