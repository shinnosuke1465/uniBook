"use client";

import { createContext, useContext, useEffect, useState } from "react";
import type { User } from "@/types/response/responseType";
import { getUserData } from "@/services/auth/getUserData";

type AuthContextType = {
	authUser: User | null;
	isLoaded: boolean;
	refreshUser: () => Promise<void>;
};

const AuthContext = createContext<AuthContextType>({
	authUser: null,
	isLoaded: false,
	refreshUser: async () => {},
});

/**
 * 認証コンテキストプロバイダー
 */
export const AuthContextProvider = ({ children }: React.PropsWithChildren) => {
	const [authUser, setAuthUser] = useState<User | null>(null);
	const [isLoaded, setIsLoaded] = useState<boolean>(false);

	const refreshUser = async () => {
		setIsLoaded(false);

		try {
			const user = await getUserData();
			setAuthUser(user);
		} catch (error) {
			// 未認証の場合は正常なのでエラーログを出さない
			setAuthUser(null);
		} finally {
			setIsLoaded(true);
		}
	};

	useEffect(() => {
		refreshUser();
	}, []);

	return (
		<AuthContext.Provider value={{ authUser, isLoaded, refreshUser }}>
			{children}
		</AuthContext.Provider>
	);
};

export const useAuthContext = (): AuthContextType =>
	useContext<AuthContextType>(AuthContext);