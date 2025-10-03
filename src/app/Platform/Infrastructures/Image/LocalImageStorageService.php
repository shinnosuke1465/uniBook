<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Image;

use App\Platform\Domains\Image\ImageStorageServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalImageStorageService implements ImageStorageServiceInterface
{
    /**
     * ローカル環境用の画像保存
     * storage/app/public/images に保存し、相対パスを返す
     *
     * @param UploadedFile $file
     * @return string 相対パス（例: images/xxxxx.jpg）
     */
    public function store(UploadedFile $file): string
    {
        // 元のファイル名を取得
        $originalName = $file->getClientOriginalName();

        // storage/app/public/images にオリジナル名で保存
        $path = $file->storeAs('images', $originalName, 'public');

        return $path;
    }
}