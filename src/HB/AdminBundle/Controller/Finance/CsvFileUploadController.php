<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use HB\AdminBundle\Entity\Teachable\TeachableTransactionRefunded;
use HB\AdminBundle\Service\TeachableCourseMapper;
use HB\AdminBundle\Service\TeachableWebhooksMapper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class CsvFileUploadController
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TeachableWebhooksMapper
     */
    private $teachableWebhookMapper;

    /**
     * @var TeachableCourseMapper
     */
    private $teachableCourseMapper;

    /**
     * @var array
     */
    private $fieldsIdx = [];

    /**
     * @var array
     */
    private $refundedFieldsIdx = [];

    /**
     * @var array
     */
    private $newTransactions = [];

    /**
     * @var array
     */
    private $refundedTransactions = [];

    /**
     * @var array
     */
    private $unknownCourseNames = [];

    /**
     * @var array
     */
    private $transactionsAlreadyExist = [];

    /**
     * CsvFileUploadController constructor.
     *
     * @param RouterInterface         $router
     * @param FlashBagInterface       $flashBag
     * @param EntityManagerInterface  $entityManager
     * @param TeachableWebhooksMapper $teachableWebhookMapper
     * @param TeachableCourseMapper   $teachableCourseMapper
     */
    public function __construct(RouterInterface $router, FlashBagInterface $flashBag, EntityManagerInterface $entityManager, TeachableWebhooksMapper $teachableWebhookMapper, TeachableCourseMapper $teachableCourseMapper)
    {
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->teachableWebhookMapper = $teachableWebhookMapper;
        $this->teachableCourseMapper = $teachableCourseMapper;
    }

    /**
     * @param Request $request
     */
    public function handleAction(Request $request)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('transaction_file');
        $row = 1;

        if (($handle = fopen($file->getRealPath(), "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if (1 === $row) {
                    try {
                        $this->defineFields($data);
                        $this->defineRefundedFields($data);
                    } catch (\InvalidArgumentException $exception) {
                        $this->flashBag->add('error', $exception->getMessage());

                        return new RedirectResponse($request->headers->get('referer', $this->router->generate('hb.finance.teachable_transaction')));
                    }

                } else {
                    $this->processRow($data);
                    $this->processRowForRefund($data);
                }

                $row++;
            }
            fclose($handle);
        }

        /** @var TeachableTransaction $transaction */
        foreach ($this->newTransactions as $transaction) {
            $this->processTransactionCreated($transaction);
        }

        /** @var TeachableTransactionRefunded $transaction */
        foreach ($this->refundedTransactions as $transaction) {
            $this->processTransactionRefunded($transaction);
        }

        if (!empty($this->unknownCourseNames)) {
            $this->flashBag->add('error', 'Не найдены id курсов с названиями:<br> '.implode('<br>', $this->unknownCourseNames));
        }

        if (!empty($this->transactionsAlreadyExist)) {
            $this->flashBag->add('error', 'Транзакции с id уже записаны:<br> '.implode(', ', $this->transactionsAlreadyExist));
        }

        return new RedirectResponse($this->router->generate('hb.finance.teachable_transaction'));
    }

    /**
     * @param TeachableTransaction $transaction
     *
     * @throws \Exception
     */
    private function processTransactionCreated(TeachableTransaction $transaction)
    {
        try {
            $transaction->updateIncome();
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();

            $this->teachableCourseMapper->mapTransactionOnCourse($transaction);
            $student = $this->teachableWebhookMapper->updateOrCreateStudent($transaction);
            $this->teachableCourseMapper->mapStudentOnCourse($student);
        } catch (\InvalidArgumentException $exception) {
            //do nothing
        }
    }

    /**
     * Process refunded transactions
     *
     * @param TeachableTransactionRefunded $transaction
     */
    private function processTransactionRefunded(TeachableTransactionRefunded $transaction)
    {
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $this->teachableCourseMapper->refundTransactions($transaction);
    }

    /**
     * @param array $data
     */
    private function processRow(array $data)
    {
        $courseNameFieldIdx = $this->fieldsIdx['course_name'];
//        $courseId = $this->getCourseIdByName($data[$courseNameFieldIdx]);

        $courseIdFieldIdx = $this->fieldsIdx['course_id'];
        $courseId = (int) $data[$courseIdFieldIdx];

        if (!$courseId) {
            $this->unknownCourseNames[$data[$courseNameFieldIdx]] = $data[$courseNameFieldIdx];

            return;
        }

        $transactionIdIdx = $this->fieldsIdx['transactionId'];
        $transactionId = $data[$transactionIdIdx];

        $transactionExist = $this->getTransactionById((int) $transactionId);
        if (!$transactionExist) {
            $newTransactionData = [];
            foreach ($this->fieldsIdx as $property => $idx) {
                $newTransactionData[$property] = $data[$idx];
            }
            $newTransactionData['course_id'] = $courseId;
            $newTransactionData['importedFromCsv'] = true;
            $newTransactionData['hookEventId'] = 1;
            $newTransactionData['createdAt'] = \DateTime::createFromFormat('Y-m-d', $newTransactionData['createdAt']);


            $this->newTransactions[] = TeachableTransaction::fromCsvData($newTransactionData);
        } else {
            $this->transactionsAlreadyExist[] = $transactionId;
        }
    }

    /**
     * @param array $data
     */
    private function processRowForRefund(array $data)
    {
        $courseNameFieldIdx = $this->refundedFieldsIdx['amountRefunded'];
        $refund = (int) $data[$courseNameFieldIdx];

        if ($refund > 0) {
            $courseIdFieldIdx = $this->refundedFieldsIdx['transactionId'];
            $transactionId = (int) $data[$courseIdFieldIdx];

            $existedRefund = $this->entityManager->getRepository(TeachableTransactionRefunded::class)->findOneBy(['transactionId' => $transactionId]);
            if (!$existedRefund) {
                $newRefundData = [];

                foreach ($this->refundedFieldsIdx as $property => $idx) {
                    $newRefundData[$property] = $data[$idx];
                }
                $newRefundData['hookEventId'] = 1;
                $newRefundData['importedFromCsv'] = true;
                $newRefundData['createdAt'] = new \DateTime($newRefundData['createdAt']);

                $this->refundedTransactions[] = TeachableTransactionRefunded::fromCsvData($newRefundData);
            }
        }
    }

    /**
     * @param string $name
     *
     * @return int | null
     */
    private function getCourseIdByName(string $name): ?int
    {
        $res = $this->entityManager->createQueryBuilder()
            ->select('c.course_id as id')
            ->from(TeachableTransaction::class, 'c')
            ->where('c.course_name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        return $res ? $res['id'] : null;
    }

    /**
     * @param int $id
     *
     * @return TeachableTransaction|null
     */
    private function getTransactionById(int $id): ?TeachableTransaction
    {
        return $this->entityManager->getRepository(TeachableTransaction::class)->findOneBy(['transactionId' => $id]);
    }

    /**
     * Define fields for new transactions
     *
     * @param array $fields
     */
    private function defineFields(array $fields)
    {
        $fieldsIdx['createdAt'] = $this->getFieldIdx($fields, 'purchased_at');
        $fieldsIdx['transactionId'] = $this->getFieldIdx($fields, 'id');
        $fieldsIdx['finalPrice'] = $this->getFieldIdx($fields, 'final_price');

        $fieldsIdx['affiliate_fees'] = $this->getFieldIdx($fields, 'affiliate_fees');

        //$fields['course_id'] = $this->getFieldIdx($fields, 'purchased_at'); ??!!!
        $fieldsIdx['course_name'] = $this->getFieldIdx($fields, 'course_name');
        $fieldsIdx['product_plan'] = $this->getFieldIdx($fields, 'product_name');

        $fieldsIdx['studentName'] = $this->getFieldIdx($fields, 'user');
        $fieldsIdx['studentEmail'] = $this->getFieldIdx($fields, 'user_email');
        $fieldsIdx['affiliateName'] = $this->getFieldIdx($fields, 'affiliate_email');
        $fieldsIdx['course_id'] = $this->getFieldIdx($fields, 'Course_id');

        $this->fieldsIdx = $fieldsIdx;
    }

    /**
     * Define fields for refunded transactions
     *
     * @param array $fields
     */
    private function defineRefundedFields(array $fields)
    {
        $fieldsIdx['createdAt'] = $this->getFieldIdx($fields, 'refunded_at');
        $fieldsIdx['transactionId'] = $this->getFieldIdx($fields, 'id');
        $fieldsIdx['amountRefunded'] = $this->getFieldIdx($fields, 'amount_refunded');
        $fieldsIdx['course_id'] = $this->getFieldIdx($fields, 'Course_id');

        $this->refundedFieldsIdx = $fieldsIdx;
    }

    /**
     * Get field idx by field name from csv file
     *
     * @param array  $fields
     * @param string $fieldName
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    private function getFieldIdx(array $fields, string $fieldName): int
    {
        foreach ($fields as $key => $field) {
            if ($field === $fieldName) {
                return $key;
            }
        }

        throw new \InvalidArgumentException('Не найдены поля в csv файле. Отсутствует: '.$fieldName);
    }
}