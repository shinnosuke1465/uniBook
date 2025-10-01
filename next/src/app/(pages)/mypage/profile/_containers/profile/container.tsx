"use client";

import { useAuthContext } from "@/contexts/AuthContext";
import { ProfilePresenter } from "./presenter";

export function ProfileContainer() {
	const { authUser, isLoaded } = useAuthContext();
    console.log(authUser);

	if (!isLoaded) {
		return <div>読み込み中...</div>;
	}

	if (!authUser) {
		return null;
	}

	return <ProfilePresenter user={authUser} />;
}