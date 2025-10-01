"use client";

import { createContext, useContext, useEffect, useState } from "react";
import type { User } from "@/types/response/responseType";
import { getUserData } from "@/services/auth/getUserData";

type AuthContextType = {
	authUser: User | null;
	isLoaded: boolean;
};

const AuthContext = createContext<AuthContextType>({
	authUser: null,
	isLoaded: false,
});

/**
 * 認証コンテキストプロバイダー
 */
export const AuthContextProvider = ({ children }: React.PropsWithChildren) => {
	const [authUser, setAuthUser] = useState<User | null>(null);
	const [isLoaded, setIsLoaded] = useState<boolean>(false);

	useEffect(() => {
		(async () => {
			setIsLoaded(false);

			try {
				const user = await getUserData();
				setAuthUser(user);
			} catch (error) {
				console.error("Failed to fetch user data:", error);
				setAuthUser(null);
			} finally {
				setIsLoaded(true);
			}
		})();
	}, []);

	return (
		<AuthContext.Provider value={{ authUser, isLoaded }}>
			{children}
		</AuthContext.Provider>
	);
};

export const useAuthContext = (): AuthContextType =>
	useContext<AuthContextType>(AuthContext);