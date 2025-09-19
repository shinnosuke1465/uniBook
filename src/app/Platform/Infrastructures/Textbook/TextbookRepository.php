<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Textbook;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\RepositoryException;
use App\Models\Textbook as TextbookDB;
use App\Models\TextbookImage;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\ConditionType;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Domains\Textbook\Textbook;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\University\UniversityId;

readonly class TextbookRepository implements TextbookRepositoryInterface
{
    /**
     * @return Textbook[]
     */
    public function findAll(): array
    {
        $textbookModels = TextbookDB::query()->with('imageIds')->get();

        return $textbookModels->map(
            fn ($textbookModel) => TextbookFactory::create($textbookModel)
        )->all();
    }

    /**
     * @throws DomainException
     */
    public function findById(TextbookId $textbookId): ?Textbook
    {
        $textbookDB = TextbookDB::query()
            ->with('imageIds')
            ->where('id',$textbookId->value)
            ->first();

        if (!$textbookDB) {
            return null;
        }

        return TextbookFactory::create($textbookDB);
    }


    /**
     * @throws DuplicateKeyException
     */
    public function insert(Textbook $textbook): void
    {
        $textbookModel = TextbookDB::query()
            ->where('id',$textbook->id->value);

        if ($textbookModel->exists()) {
            throw new DuplicateKeyException('指定された教科書は既に存在します。textbookId: '. $textbook->id->value);
        }

        TextbookDB::create([
            'id' => $textbook->id->value,
            'name' => $textbook->name->value,
            'price' => $textbook->price->value,
            'description' => $textbook->description->value,
            'condition_type' => $textbook->conditionType->value,
            'university_id' => $textbook->universityId->value,
            'faculty_id' => $textbook->facultyId->value,
        ]);

        // 画像の関連付け
        if ($textbook->imageIdList->isNotEmpty()) {
            $imageData = [];
            foreach ($textbook->imageIdList->toArray() as $imageId) {
                $imageData[] = [
                    'textbook_id' => $textbook->id->value,
                    'image_id' => $imageId->value,
                ];
            }
            TextbookImage::insert($imageData);
        }
    }

    /**
     * @throws RepositoryException
     */
    public function update(Textbook $textbook): void
    {
        $textbookModel = TextbookDB::query()
            ->where('id',$textbook->id->value)
            ->first();

        if (!$textbookModel) {
            throw new RepositoryException('指定された教科書が見つかりません。textbookId: '. $textbook->id->value);
        }

        $textbookModel->update([
            'name' => $textbook->name->value,
            'price' => $textbook->price->value,
            'description' => $textbook->description->value,
            'condition_type' => $textbook->conditionType->value,
        ]);

        // 画像の関連付けを更新
        if ($textbook->imageIdList->isNotEmpty()) {
            $imageData = [];
            foreach ($textbook->imageIdList->toArray() as $imageId) {
                $imageData[] = [
                    'textbook_id' => $textbook->id->value,
                    'image_id' => $imageId->value,
                ];
            }
            TextbookImage::insert($imageData);
        }
    }

    /**
     * @throws RepositoryException
     */
    public function delete(TextbookId $textbookId): void
    {
        $textbookModel = TextbookDB::query()
            ->where('id',$textbookId->value)
            ->first();

        if (!$textbookModel) {
            throw new RepositoryException('指定された教科書が見つかりません。textbookId: '. $textbookId->value);
        }

        // 画像の関連付けを削除
        TextbookImage::where('textbook_id', $textbookId->value)->delete();

        // ソフトデリート
        $textbookModel->delete();
    }

}
