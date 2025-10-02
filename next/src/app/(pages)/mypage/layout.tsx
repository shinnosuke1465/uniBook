"use client";

import { Route } from "@/types/route";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useRequireLogin } from "@/hooks/useRequireLogin";

export interface CommonLayoutProps {
	children?: React.ReactNode;
}

const pages: {
	name: string;
	link: Route;
}[] = [
	{
		name: "プロフィール",
		link: "/mypage/profile",
	},
	{
		name: "購入した教科書",
		link: "/mypage/purchased_textbooks",
	},
	{
		name: "出品した教科書",
		link: "/mypage/listed_textbooks",
	},
	{
		name: "いいねした教科書",
		link: "/mypage/liked_textbooks",
	},
	{
		name: "取引一覧",
		link: "/mypage/deal_rooms",
	},
];

export default function CommonLayout({ children }: CommonLayoutProps) {
	const pathname = usePathname();
	const isLoaded = useRequireLogin();

	if (!isLoaded) {
		return <div className="container"></div>;
	}

	// @ts-ignore
    return (
		<div className="container">
			<div className="mt-14 sm:mt-20">
				<div className="max-w-4xl mx-auto">
					<div className="max-w-2xl">
						<h2 className="text-3xl xl:text-4xl font-semibold">マイページ</h2>
					</div>
					<hr className="mt-10 border-slate-200" />

					<div className="flex space-x-8 md:space-x-14 overflow-x-auto">
						{pages.map((item, index) => {
							return (
								<Link
									key={index}
									href={item.link}
									className={`block py-5 md:py-8 border-b-2 flex-shrink-0 text-sm sm:text-base ${
										pathname.startsWith(item.link)
											? "border-primary-500 font-medium text-slate-900"
											: "border-transparent text-slate-500 hover:text-slate-800"
									}`}
								>
									{item.name}
								</Link>
							);
						})}
					</div>
					<hr className="border-slate-200" />
				</div>
			</div>
			<div className="max-w-4xl mx-auto pt-14 sm:pt-26 pb-24 lg:pb-32">
				{children}
			</div>
		</div>
	);
}