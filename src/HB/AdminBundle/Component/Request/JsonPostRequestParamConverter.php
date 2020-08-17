<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Component\Request;


use HB\AdminBundle\Component\Request\Exception\ViolationListException;
use HB\AdminBundle\Controller\Api\Resource\QueryResourceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * The parameter converter for convert the GET query to resource.
 */
class JsonPostRequestParamConverter implements ParamConverterInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param ValidatorInterface  $validator
     * @param SerializerInterface $serializer
     */
    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ViolationListException
     */
    public function apply(Request $request, ParamConverter $configuration): void
    {
        if ($request->isMethod(Request::METHOD_POST)) {

            $json = $request->getContent();

            $json = str_replace("\t", "", $json);
            $json = str_replace("\n", "", $json);

            $queryResource = $this->serializer->deserialize($json, $configuration->getClass(), 'json');

            $violations = $this->validator->validate($queryResource);

            if (count($violations)) {
                throw ViolationListException::create($violations);
            }

            $request->attributes->set($configuration->getName(), $queryResource);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        return \is_a($configuration->getClass(), QueryResourceInterface::class, true);
    }
}