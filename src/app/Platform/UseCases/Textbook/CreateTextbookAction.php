<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook;

use App\Exceptions\DomainException;
use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealRepositoryInterface;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\DealDomainService;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\DealEvent\DealEventRepositoryInterface;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\Textbook\Textbook;
use App\Platform\Domains\University\UniversityRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use Exception;

readonly class CreateTextbookAction
{
    public function __construct(
        private TransactionInterface $transaction,
        private TextbookRepositoryInterface $textbookRepository,
        private UniversityRepositoryInterface $universityRepository,
        private FacultyRepositoryInterface $facultyRepository,
        private DealRepositoryInterface $dealRepository,
        private DealEventRepositoryInterface $dealEventRepository,
        private DealDomainService $dealDomainService,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws Exception
     */
    public function __invoke(
        CreateTextbookActionValuesInterface $actionValues,
    ): void {
        AppLog::start(__METHOD__);
        $requestParams = [];

        try {
            $name = $actionValues->getName();
            $price = $actionValues->getPrice();
            $description = $actionValues->getDescription();
            $imageIds = $actionValues->getImageIdList();
            $universityId = $actionValues->getUniversityId();
            $facultyId = $actionValues->getFacultyId();
            $conditionType = $actionValues->getConditionType();

            $requestParams = [
                'name' => $name->value,
                'price' => $price->value,
                'description' => $description->value,
                'image_ids' => $imageIds->toArray(),
                'university_id' => $universityId->value,
                'faculty_id' => $facultyId->value,
                'condition_type' => $conditionType->value,
            ];

            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            //大学チェック
            $university = $this->universityRepository->findById($universityId);
            if ($university === null) {
                throw new DomainException('指定された大学は存在しません。universityId: '. $universityId->value);
            }

            //学部チェック
            $faculty = $this->facultyRepository->findById($facultyId);
            if ($faculty === null) {
                throw new DomainException('指定された学部は存在しません。facultyId: '. $facultyId->value);
            }

            //認証されたユーザーを取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            if ($authenticatedUser === null) {
                throw new DomainException('認証済みユーザー情報が取得できませんでした。');
            }

            $textbook = Textbook::create(
                $name,
                $price,
                $description,
                $imageIds,
                $universityId,
                $facultyId,
                $conditionType,
            );

            //Dealを作成（売り手として）+ DealEventを作成
            $seller = new Seller($authenticatedUser->getUserId());
            [$deal, $dealEvent] = $this->dealDomainService->createListing($seller, $textbook->id);

            $this->transaction->begin();
            $this->textbookRepository->insert($textbook);
            $this->dealRepository->insert($deal);
            $this->dealEventRepository->insert($dealEvent);
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
