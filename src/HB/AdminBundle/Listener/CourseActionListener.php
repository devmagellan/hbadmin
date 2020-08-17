<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Listener;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\PersistentCollection;
use HB\AdminBundle\Entity\ActionLog;
use HB\AdminBundle\Helper\ClassNameHelper;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Sentry\SentryBundle\SentrySymfonyClient;
use Symfony\Component\Translation\TranslatorInterface;

class CourseActionListener
{
    /**
     * @var CourseAccessService
     */
    private $accessService;

    /**
     * @var ActionLog[]
     */
    private $logs = [];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var IntercomEventRecorder
     */
    private $intercomEventRecorder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SentrySymfonyClient
     */
    private $sentryErrorHandler;

    /**
     * CourseActionListener constructor.
     *
     * @param CourseAccessService   $accessService
     * @param IntercomEventRecorder $intercomEventRecorder
     * @param TranslatorInterface   $translator
     * @param SentrySymfonyClient   $sentryErrorHandler
     */
    public function __construct(CourseAccessService $accessService, IntercomEventRecorder $intercomEventRecorder, TranslatorInterface $translator, SentrySymfonyClient $sentryErrorHandler)
    {
        $this->accessService = $accessService;
        $this->intercomEventRecorder = $intercomEventRecorder;
        $this->translator = $translator;
        $this->sentryErrorHandler = $sentryErrorHandler;
    }

    /**
     * Event calls if entity exist and updates
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($this->accessService->getCourse() && $this->accessService->getCourse()->needSaveLogs()) {
            $this->entityManager = $args->getEntityManager();

            if (empty($args->getEntityChangeSet())) {
                try {
                    $changeSet = $this->catchCollectionChangeSet($args);
                } catch (\Exception $exception) {
                    $this->sentryErrorHandler->captureException($exception);
                    $changeSet = [];
                }
            } else {
                $changeSet = $args->getEntityChangeSet();
            }
            if ( array_key_exists('lastActivityAt', $changeSet)) {
                return false;
            }

            $this->registerChangeEvent($args->getEntityChangeSet());

            $log = new ActionLog(
                $this->accessService->getCourse(),
                $this->accessService->getCurrentUser(),
                $changeSet,
                $this->getClassName(get_class($args->getObject())),
                $args->getObject()->getId(),
                $this->getFunnelName(),
                $this->getFunnelId()

            );
            $this->logs[] = $log;
        }
    }

    /**
     * * Event calls if entity not exist and created
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($this->accessService->getCourse() && $this->accessService->getCourse()->needSaveLogs()) {
            $this->entityManager = $args->getEntityManager();
            $changeSet = [LogEvents::CREATED => [0, 1]];

            $this->registerChangeEvent($changeSet);

            $log = new ActionLog(
                $this->accessService->getCourse(),
                $this->accessService->getCurrentUser(),
                $changeSet,
                $this->getClassName(get_class($args->getObject())),
                $args->getObject()->getId(),
                $this->getFunnelName(),
                $this->getFunnelId()

            );
            $this->logs[] = $log;
        }
    }

    /**
     * Event calls if entity was exist and removed
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($this->accessService->getCourse() && $this->accessService->getCourse()->needSaveLogs()) {
            $this->entityManager = $args->getEntityManager();
            $changeSet = [LogEvents::REMOVED => [1, 0]];

            $log = new ActionLog(
                $this->accessService->getCourse(),
                $this->accessService->getCurrentUser(),
                $changeSet,
                $this->getClassName(get_class($args->getObject())),
                $args->getObject()->getId(),
                $this->getFunnelName(),
                $this->getFunnelId()

            );
            $this->logs[] = $log;
        }
    }

    /**
     * It's impossible to flush before, cuz get recursion
     * so flush log on kernel.terminate event
     */
    public function kernelTerminate()
    {
        if ($this->entityManager) {
            foreach ($this->logs as $log) {
                $this->entityManager->persist($log);
            }
            $this->entityManager->flush();
        }
    }

    /**
     * @param string $namespace
     *
     * @return string
     */
    private function getClassName(string $namespace)
    {
        return ClassNameHelper::getClassName($namespace);
    }

    /**
     * @return int
     */
    private function getFunnelId(): ?int
    {
        $id = null;

        if (strlen($this->getFunnelName()) > 0) {
            try {
                $id = $this->accessService->getFunnel()->getId();
            } catch (\Exception $exception) {}
        }

        return $id;
    }

    /**
     * @return string
     */
    private function getFunnelName(): string
    {
        $name = '';
        if ($this->accessService->getFunnel()) {
            $name = $this->getClassName(get_class($this->accessService->getFunnel()));
        }

        return $name;
    }

    /**
     * @param array $changeSet
     */
    private function registerChangeEvent(array $changeSet)
    {
        if ($this->accessService->getCourse()) {
            foreach ($changeSet as $key => $set) {
                if (!is_object($set[0]) && $set[1] && !is_object($set[1])) {
                    if (0 === strlen((string) $set[0]) && strlen((string) $set[1]) > 1) {

                        $metadata = [
                            'course' => $this->accessService->getCourse()->getId(),
                        ];

                        $metadata['field'] = $this->translator->trans($key);
                        $metadata['new_value'] = $set[1];

                        $funnel = $this->getFunnelName();

                        if (\strlen($funnel) > 0) {
                            $metadata['funnel'] = $this->translator->trans($funnel);
                        }

                        $this->intercomEventRecorder->registerEvent(IntercomEvents::COURSE_ADD_TEXT, $metadata);
                    }
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return array
     */
    private function catchCollectionChangeSet(LifecycleEventArgs $args): array
    {
        $result = [];
        $unitOfWork = $args->getEntityManager()->getUnitOfWork();

        $objectClass = null;

        /** @var PersistentCollection $persistentCollection */
        foreach ($unitOfWork->getScheduledCollectionUpdates() as $persistentCollection) {

            $previousCount = count($persistentCollection->getSnapshot());
            $currentCount = $persistentCollection->count();

            foreach ($persistentCollection as $item) {
                $objectClass = $this->getClassName(get_class($item));
                break;
            }
            $result[$objectClass] = [$previousCount, $currentCount];

        }

        return $result;
    }
}