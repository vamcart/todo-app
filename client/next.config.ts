import type { NextConfig } from "next";

const nextConfig: NextConfig = {
    async rewrites() {
        return [
            { source: '/api-docs', destination: 'http://localhost:8000/api-docs' },
        ];
    },
    /* config options here */
};

export default nextConfig;
