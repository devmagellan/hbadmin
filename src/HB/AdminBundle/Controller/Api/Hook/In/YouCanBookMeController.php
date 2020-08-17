<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api\Hook\In;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Component\Model\Email;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use HB\AdminBundle\Entity\WebHook;
use HB\AdminBundle\Service\Customer\SignUpService;
use HB\AdminBundle\Service\Intercom\IntercomAttributes;
use HB\AdminBundle\Service\Intercom\IntercomClient;
use Symfony\Component\HttpFoundation\Request;

class YouCanBookMeController
{
  /**
   * @var EntityManagerInterface
   */
  private $entityManager;

  /**
   * @var SignUpService
   */
  private $signUpService;

  /**
   * @var IntercomClient
   */
  private $intercomClient;

  /**
   * TypeFormController constructor.
   *
   * @param EntityManagerInterface $entityManager
   * @param SignUpService          $signUpService
   * @param IntercomClient         $intercomClient
   */
  public function __construct(EntityManagerInterface $entityManager, SignUpService $signUpService, IntercomClient $intercomClient)
  {
      $this->entityManager = $entityManager;
      $this->signUpService = $signUpService;
      $this->intercomClient = $intercomClient;
  }

  public function handleAction(Request $request){
    try {
      $content = $request->getContent();
      $referer = $request->headers->get('referer');

      $webhook = new WebHook($content, $referer);
      $this->entityManager->persist($webhook);
      $this->entityManager->flush();

      $data = json_decode($content, true);

      $customer=$this->queryUserByEmail($data['email']);

      if (!empty($customer)&&$customer->getUsername()===$data['email']) {
        $this->sendAttributes($data);

        return Json::ok();
      } else {
        return Json::error('No user found');
      }
    } catch (\Exception $e) {
      return Json::error($e->getMessage().' in file '.$e->getFile().' on line '.$e->getLine().PHP_EOL.PHP_EOL.'Stack trace: '.PHP_EOL.$e->getTraceAsString());
      // return Json::error('Something went wrong! We are into it already.');
    }

  }

  private function queryUserByEmail($email){
    $query = $this->entityManager->createQueryBuilder()
      ->select('customer')
      ->from(Customer::class, 'customer')
      ->where('customer.email = :email')
      ->setParameters(['email' => $email])
      ->getQuery();

    return $query->getOneOrNullResult();
  }

  private function sendAttributes($data){
    $attributes=[];

    foreach ($data as $key => $val) {
      if ($key!=='event'||empty($val)) {
        continue;
      }

      switch ($val) {
        case 'new_booking':
          $attributes[IntercomAttributes::YOUCANBOOKME_BOOKED]='Yes';
          break;
      }
    }

    if (count($attributes)>0) {
      $this->intercomClient->getClient()->users->update([
        "email"             => $data['email'],
        "custom_attributes" => $attributes,
      ]);
    }

  }
}