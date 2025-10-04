import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  /* config options here */
    experimental:{
        serverActions:{
            allowedOrigins: [
                `${process.env.API_BASE_DOMAIN}`,
                `${process.env.WEB_BASE_DOMAIN}`,
            ],
        }
    }
};

export default nextConfig;
