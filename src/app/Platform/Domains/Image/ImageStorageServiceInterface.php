<?php

namespace App\Platform\Domains\Image;

use Illuminate\Http\UploadedFile;

interface ImageStorageServiceInterface
{
    /**
     * 画像ファイルを保存し、保存されたパスを返す
     *
     * @param UploadedFile $file
     * @return string 保存されたパス（Local: 相対パス, S3: フルURL）
     */
    public function store(UploadedFile $file): string;
}