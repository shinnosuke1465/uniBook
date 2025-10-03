"use client";

type ImageFrameProps = {
  path: string;
  alt: string;
  width?: number;
  height?: number;
  className?: string;
};

export function ImageFrame({
  path,
  alt,
  className = "object-cover",
}: ImageFrameProps) {
  // 開発環境の場合、相対パスをLaravelのURLに変換
  const getImageUrl = () => {
    console.log("Original path:", path);

    // 本番環境またはCI環境の場合、DBにはフルURLが保存されているのでそのまま返す
    if (process.env.NODE_ENV === "production" || process.env.APP_ENV === "ci") {
      return path;
    }

    // 開発環境の場合、localhost:8080 のURLを返す
    // path が http://localhost:8080/storage/xxxxx の形式ならそのまま返す
    if (path.startsWith("http://localhost:8080")) {
      console.log("Already localhost:8080, returning:", path);
      return path;
    }

    // path が http:// で始まる場合（localhost:8080 以外）、localhost:8080 に置き換える
    if (path.startsWith("http")) {
      // http://localhost:8080/storage/xxxxx の形式から storage/xxxxx を抽出
      const match = path.match(/\/storage\/(.+)$/);
      if (match) {
        const newUrl = `http://localhost:8080/storage/${match[1]}`;
        console.log("Replaced URL:", newUrl);
        return newUrl;
      }
      console.log("Could not extract storage path, returning original:", path);
      return path;
    }

    // 相対パスの場合、localhost:8080 のURLに変換
    const newUrl = `http://localhost:8080/storage/${path}`;
    console.log("Converted relative path to:", newUrl);
    return newUrl;
  };

  const imageUrl = getImageUrl();

  return (
    <img
      src={imageUrl}
      alt={alt}
      className={className}
    />
  );
}