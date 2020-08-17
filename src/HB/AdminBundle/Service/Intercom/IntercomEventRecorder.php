<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Service\Intercom;

use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\UploadCareFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IntercomEventRecorder
{
    /**
     * @var IntercomClient
     */
    private $intercomClient;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * IntercomEventHandler constructor.
     *
     * @param IntercomClient        $intercomClient
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(IntercomClient $intercomClient, TokenStorageInterface $tokenStorage)
    {
        $this->intercomClient = $intercomClient;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string $event
     * @param array  $metadata
     */
    public function registerEvent(string $event, array $metadata = [])
    {
        if ($this->tokenStorage->getToken()) {
            $this->customer = $this->tokenStorage->getToken()->getUser();

            $timestamp = (new \DateTime())->getTimestamp();

            if ($this->customer && $this->customer->getValidEmail()) {
                $event = [
                    "event_name" => $event,
                    "created_at" => $timestamp,
                    "email"      => $this->customer->getValidEmail()->getValue(),
                    // "user_id"    => $this->customer->getId(),
                    "user_id"    => $this->customer->getValidEmail()->getValue(),
                ];

                if (!empty($metadata)) {
                    $event['metadata'] = $metadata;
                }

                $this->intercomClient->getClient()->events->create($event);
            }
        }
    }

    /**
     * @param UploadCareFile $file
     * @param array | [] $metadataCustom
     */
    public function registerFileAddEvent(UploadCareFile $file, array $metadataCustom = [])
    {
        $metadata = ['Link' => $file->getFileUrl(),];
        $metadata = array_merge($metadata, $metadataCustom);

        $this->registerEvent(IntercomEvents::ADD_FILE, $metadata);
    }

    /**
     * @param UploadCareFile $file
     * @param array | [] $metadata
     */
    public function registerImageAddEvent(UploadCareFile $file, array $metadataCustom = [])
    {
        $metadata = ['Link' => $file->getFileUrl(),];
        $metadata = array_merge($metadata, $metadataCustom);

        $this->registerEvent(IntercomEvents::ADD_IMAGE, $metadata);
    }

    /**
     * @param \Exception $exception
     */
    public function registerCriticalExceptionEvent(\Exception $exception)
    {
        $this->registerEvent(IntercomEvents::CRITICAL_EXCEPTION, [
            'message'   => $exception->getMessage(),
            'exception' => get_class($exception),
        ]);
    }

    /**
     * @param \Exception $exception
     */
    public function registerAccessExceptionEvent(\Exception $exception)
    {
        $this->registerEvent(IntercomEvents::ACCESS_EXCEPTION, [
            'message'   => $exception->getMessage(),
            'exception' => get_class($exception),
        ]);
    }
}