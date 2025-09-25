<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Comment;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Comment\Comment;
use App\Platform\Domains\Comment\CommentRepositoryInterface;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Exception;

readonly class CreateCommentAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface $userRepository,
        private TextbookRepositoryInterface $textbookRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws Exception
     */
    public function __invoke(
        CreateCommentActionValuesInterface $actionValues,
        string $textbookIdString,
    ): void {
        AppLog::start(__METHOD__);
        $textbookId = new TextbookId($textbookIdString);
        $requestParams = [];

        try {
            $text = $actionValues->getText();

            $requestParams = [
                'text' => $text->value,
                'textbook_id' => $textbookId->value,
            ];

            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            //教科書確認
             $textbook = $this->textbookRepository->findById($textbookId);
             if ($textbook === null) {
                 throw new NotFoundException('指定された教科書が存在しません。');
             }

            //認証されたユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            $comment = Comment::create(
                $text,
                $authenticatedUser->getUserId(),
                $textbookId,
            );

            $this->transaction->begin();
            $this->commentRepository->insert($comment);
            $this->transaction->commit();
        } catch (Exception $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            $this->transaction->rollback();
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
