<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Image;

use App\Platform\Domains\Image\ImageStorageServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3ImageStorageService implements ImageStorageServiceInterface
{
    /**
     * 本番環境用の画像保存（S3）
     * S3に保存し、フルURLを返す
     *
     * @param UploadedFile $file
     * @return string フルURL（例: https://bucket.s3.amazonaws.com/images/xxxxx.jpg）
     */
    public function store(UploadedFile $file): string
    {
        // 元のファイル名を取得
        $originalName = $file->getClientOriginalName();

        // S3の images ディレクトリにオリジナル名で保存
        $path = $file->storeAs('images', $originalName, 's3');

        // S3のフルURLを返す
        return Storage::disk('s3')->url($path);
    }
}