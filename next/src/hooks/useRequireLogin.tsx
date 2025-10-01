"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuthContext } from "@/contexts/AuthContext";

export function useRequireLogin() {
	const { authUser, isLoaded } = useAuthContext();
	const router = useRouter();

	useEffect(() => {
		if (!isLoaded) {
			return;
		}

		if (!authUser) {
			router.push("/login");
		}
	}, [router, isLoaded, authUser]);

	return authUser !== null;
}