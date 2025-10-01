import type { User } from "@/types/response/responseType";

type ProfilePresenterProps = {
	user: User;
};

export function ProfilePresenter({ user }: ProfilePresenterProps) {
	return (
		<div className="space-y-6">
			<div className="bg-white rounded-lg shadow p-6">
				<h3 className="text-lg font-semibold mb-4">プロフィール情報</h3>
				<div className="space-y-4">
					<div>
						<label className="block text-sm font-medium text-gray-700">
							名前
						</label>
						<p className="mt-1 text-sm text-gray-900">{user.name}</p>
					</div>
					<div>
						<label className="block text-sm font-medium text-gray-700">
							メールアドレス
						</label>
						<p className="mt-1 text-sm text-gray-900">{user.mail_address}</p>
					</div>
					<div>
						<label className="block text-sm font-medium text-gray-700">
							大学
						</label>
						<p className="mt-1 text-sm text-gray-900">{user.university_name}</p>
					</div>
					<div>
						<label className="block text-sm font-medium text-gray-700">
							学部
						</label>
						<p className="mt-1 text-sm text-gray-900">{user.faculty_name}</p>
					</div>
					<div>
						<label className="block text-sm font-medium text-gray-700">
							郵便番号
						</label>
						<p className="mt-1 text-sm text-gray-900">{user.post_code}</p>
					</div>
					<div>
						<label className="block text-sm font-medium text-gray-700">
							住所
						</label>
						<p className="mt-1 text-sm text-gray-900">{user.address}</p>
					</div>
				</div>
			</div>
		</div>
	);
}