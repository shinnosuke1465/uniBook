<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User;

use App\Exceptions\UseCaseException;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\User\User;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use AppLog;
use App\Platform\Domains\User\UserRepositoryInterface;
use Exception;

readonly class CreateUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TransactionInterface $transaction,
    ){
    }

    public function __invoke(
        CreateUserValuesInterface $actionValues,
    ): void {
        AppLog::start(__METHOD__);

        $requestParams = [];
        try {
            $name = $actionValues->getName();
            $password = $actionValues->getUserPassword();
            $postCode = $actionValues->getPostCode();
            $address = $actionValues->getAddress();
            $mailAddress = $actionValues->getMailAddress();
            $imageId = $actionValues->getImageId();
            $facultyId = $actionValues->getFacultyId();
            $universityId = $actionValues->getUniversityId();

            $requestParams = [
                'name' => $name->name,
                'postCode' => $postCode->postCode->value,
                'address' => $address->address->value,
                'mailAddress' => $mailAddress->mailAddress->value,
                'imageId' => $imageId?->value,
                'facultyId' => $facultyId->value,
                'universityId' => $universityId->value,
            ];

            //ログ出力
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            //ログインID = メールアドレス
            $loginId = new MailAddress($mailAddress->mailAddress);
            $existUser = $this->userRepository->findByLoginId($loginId);
            if ($existUser !== null) {
                throw new UseCaseException('ユーザーがすでに存在します。');
            }

            $this->transaction->begin();

            $insertUser = User::create(
                $name,
                $password,
                $postCode,
                $address,
                $mailAddress,
                $imageId,
                $facultyId,
                $universityId,
            );
            $this->userRepository->insertWithLoginId($insertUser, $loginId);

            $this->transaction->commit();
        } catch (UseCaseException $e) {
            $this->transaction->rollback();
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            //エラー詳細はフロントに返さず、400エラー(Bad Request)で統一する
            throw new UseCaseException('Bad Request');
        } catch (Exception $e) {
            $this->transaction->rollback();
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
