<?php

declare(strict_types=1);

namespace App\Platform\Domains\Textbook;

use App\Platform\Domains\Shared\String\String255;
use Illuminate\Support\Collection;

interface TextbookRepositoryInterface
{
    public function findAll(): array;

    public function findById(TextbookId $textbookId): ?Textbook;

    public function findAllWithRelations(): Collection;

    public function findByIdWithRelations(TextbookId $textbookId): mixed;

    public function insert(Textbook $textbook): void;

    public function update(Textbook $textbook): void;

    public function delete(TextbookId $textbookId): void;

}
