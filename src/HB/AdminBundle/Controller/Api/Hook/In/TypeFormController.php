<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api\Hook\In;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Component\Model\Email;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use HB\AdminBundle\Entity\WebHook;
use HB\AdminBundle\Service\Customer\SignUpService;
use HB\AdminBundle\Service\Intercom\IntercomAttributes;
use HB\AdminBundle\Service\Intercom\IntercomClient;
use Symfony\Component\HttpFoundation\Request;

class TypeFormController
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

    private $predefinedFields;

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

    /**
     * @param Request $request
     */
    public function handleAction(Request $request)
    {
        try {
            $content = $request->getContent();
            $referer = $request->headers->get('referer');

            $webhook = new WebHook($content, $referer);
            $this->entityManager->persist($webhook);
            $this->entityManager->flush();

            $this->data = json_decode($content, true);

            $this->setUpPredefinedFields($this->data['form_response']['form_id']);

            $answers = $this->data['form_response']['answers'];

            return $this->executeFormAction($answers);
        } catch (\Exception $e) {
            return Json::error($e->getMessage().' in file '.$e->getFile().' on line '.$e->getLine().PHP_EOL.PHP_EOL.'Stack trace: '.PHP_EOL.$e->getTraceAsString());
            // return Json::error('Something went wrong! We are into it already.');
        }
    }

    private function setUpPredefinedFields($formId){
      switch ($formId) {
        case 'ZVgjad':
          // onlineschool
          $this->predefinedFields = [
              'oSIHyPAxm0OY' => 'email', // email
              'MP57vRzAMvaz' => 'name', // text
              'I5KMpZkfAfp9' => 'phone', // text
              'XhXCCrWzLYsy' => 'role', // multiple_choice
              'g6nHtvrmB5Nk' => 'sell_amount1', // multiple_choice
              'LRKXB41IBWFA' => 'sell_amount2', // multiple_choice
              'XYG8IzwerLeu' => 'sell_experience', // boolean
              // 'ACkZUgfotAAF' => 'has_product', // phone
          ];
          break;
        case 'hxswPR':
          // premium
          $this->predefinedFields = [
              'q9ZUgCCp5H4g' => 'email', // email
              'dKNNBHsJe68z' => 'name', // text
              'X9IuSLLbF21K' => 'phone', // text
              'OUceSpO83F54' => 'role', // multiple_choice
              'LrcylSGD7Cjf' => 'sell_amount1', // multiple_choice
              'weBTKqWUTE8p' => 'sell_amount2', // multiple_choice
              'LQkh2xwe0WX9' => 'sell_experience', // boolean
              // 'ACkZUgfotAAF' => 'has_product', // phone
          ];
          break;
        case 'hrumdF':
          // pro
          $this->predefinedFields = [
              'xmm9oGqJhbCF' => 'email', // email
              'ftIFxktTX0ac' => 'name', // text
              'yYO2cgSdajvD' => 'phone', // text
              'LDDE6tT8ErrZ' => 'role', // multiple_choice
              'W2tr9VDeuyzx' => 'sell_amount1', // multiple_choice
              'RcbvYZNUlW39' => 'sell_amount2', // multiple_choice
              'nIqv3O7Jr1mq' => 'sell_experience', // boolean
              // 'ACkZUgfotAAF' => 'has_product', // phone
          ];
          break;
        case 'LJ8FQ8':
          // VIP
          $this->predefinedFields = [
              'hCYWId5CLR7O' => 'email', // email
              'b1eZJIrDHz3K' => 'name', // text
              'pkThGKGslgwc' => 'phone', // text
              'ZQcLVyOSx415' => 'role', // multiple_choice
              'T8mSDnmuuHhb' => 'sell_amount1', // multiple_choice
              'iTYtuCPmQZP7' => 'sell_amount2', // multiple_choice
              'r3jh9xT4FNt1' => 'sell_experience', // boolean
              // 'ACkZUgfotAAF' => 'has_product', // phone
          ];
          break;
        case 'u2PhOz':
          // individual
          $this->predefinedFields = [
              'j5pZ17VJzI13' => 'email', // email
              'At9wKvTwr9Rs' => 'name', // text
              'P5vtSC4vwhB8' => 'phone', // text
              'mtcU8a1FfXq5' => 'role', // multiple_choice
              'ABG8bp3grbvA' => 'sell_amount1', // multiple_choice
              'pwGRAYGesb0Z' => 'sell_amount2', // multiple_choice
              'XB0p4Qs78UE5' => 'sell_experience', // boolean
              // 'ACkZUgfotAAF' => 'has_product', // phone
          ];
          break;
        case 'kLMPBu':
          // webinar
          $this->predefinedFields = [
              'p91lVVkZwI4p' => 'email', // email
              'SkW2SdckCRN6' => 'name', // text
              'AhfUNPLVACTR' => 'phone', // text
              'bQtFbiwQXBPG' => 'role', // multiple_choice
              'Hmhu0hMx15N4' => 'sell_amount1', // multiple_choice
              'I5ibH0bVbNOA' => 'sell_amount2', // multiple_choice
              'iHSCLGRNauZO' => 'sell_experience', // boolean
              // 'ACkZUgfotAAF' => 'has_product', // phone
          ];
          break;

        default:
          $this->predefinedFields = [
              'v5Z8pPkurgwk' => 'email', // email
              'iuNqBzUOwRFK' => 'name', // text
              'EjsmI8nAvbfL' => 'phone', // text
              'CqsIr0gcH1wt' => 'role', // multiple_choice
              'ACkZUgfotAAF' => 'sell_amount1', // multiple_choice
              'SYjv5nKMJsSv' => 'sell_amount2', // multiple_choice
              'ooWwmBkQU2cQ' => 'sell_experience', // boolean
              // 'ACkZUgfotAAF' => 'has_product', // phone
          ];
          break;
      }

      $this->predefinedFields[$formId]='formId';
    }

    private function executeFormAction($answers){
      try {
        // parse answers array
        $regData = $this->returnRegData($answers);

        // get hidden fields` data
        $regData['tarif'] = $this->getHiddenField($this->data['form_response']['hidden'], 'tarif');
        $regData['utm_source'] = $this->getHiddenField($this->data['form_response']['hidden'], 'utm_source');

        $email = new Email($regData['email']);

        $role = $regData['role'] == CustomerRole::ROLE_MANAGER ? CustomerRole::ROLE_AUTHOR : $regData['role'];
        $customer = $this->signUpService->signUp($email, $regData['name'], $role, new CustomerPacketType($regData['tarif']), (string) $regData['phone']);

        // send attributes to intercom
        if (!empty($regData['email'])) {
          $this->sendAttributes($regData);
        }

        $this->signUpService->sendFirstSignUpFromTypeFormEmail($customer);

        return Json::ok();
      } catch (\Exception $e) {
        return Json::error($e->getMessage().' in file '.$e->getFile().' on line '.$e->getLine().PHP_EOL.PHP_EOL.'Stack trace: '.PHP_EOL.$e->getTraceAsString());
      }
    }

    private function getChoice(Array $field)
    {
        $result = '';

        if (!empty($this->predefinedFields[$field['field']['id']])) {
          // $result=var_export($field, true);
          switch ($this->predefinedFields[$field['field']['id']]) {
              case 'sell_amount1':
              case 'sell_amount2':
                  $translate = [
                      'меньше 1 000$ в месяц'  => 'less 1K',
                      'от 1 до 10 тысяч $'     => 'less 10K',
                      'от 10 до 100 тысяч $'   => 'less 100K',
                      'больше 100 000$'        => 'more 100K',
                      'пока не готов ответить' => 'unknown',
                  ];

                  $result = strtr($field['choice']['label'], $translate);
                  break;

              case 'role':
                  $result = CustomerRole::ROLE_AUTHOR;
                  if (!empty($field['choice']['label'])) {
                    switch ($field['choice']['label']) {
                        case 'Продюсер образовательных программ':
                            $result = CustomerRole::ROLE_PRODUCER;
                            break;
                        case 'Менеджер (работаю в команде автора или продюсера)':
                            $result = CustomerRole::ROLE_MANAGER;
                            break;

                        default:
                            $result = CustomerRole::ROLE_AUTHOR;
                            break;
                    }
                  }

                  break;
          }
        }

        return $result;
    }

    private function getHiddenField(Array $field, $type)
    {
        $result = '';
        if (!empty($field[$type])) {
          switch ($type) {
              case 'tarif':
                  switch ($field[$type]) {
                      case 'onlineSchool':
                          $result = CustomerPacketType::ONLINE_SCHOOL;
                          break;
                      case 'webinar':
                          $result = CustomerPacketType::WEBINAR;
                          break;
                      case 'pro':
                          $result = CustomerPacketType::PROFESSIONAL;
                          break;
                      case 'vip':
                          $result = CustomerPacketType::VIP;
                          break;
                      case 'ind':
                          $result = CustomerPacketType::CUSTOM;
                          break;

                      case 'premium':
                      default:
                          $result = CustomerPacketType::PREMIUM;
                          break;
                  }
                  break;

              case 'utm_source':
              default:
                  $result = $field[$type];
                  break;
          }
        } else {
          $formId=array_search('formId', $this->predefinedFields);

          switch ($type) {
              case 'tarif':
                  switch ($formId) {
                    case 'kLMPBu':
                      $result = CustomerPacketType::WEBINAR;
                      break;
                    case 'u2PhOz':
                      $result = CustomerPacketType::CUSTOM;
                      break;
                    case 'LJ8FQ8':
                      $result = CustomerPacketType::VIP;
                      break;
                    case 'ZVgjad':
                      $result = CustomerPacketType::ONLINE_SCHOOL;
                      break;
                    case 'hrumdF':
                      $result = CustomerPacketType::PROFESSIONAL;
                      break;

                    case 'hxswPR':
                    default:
                      $result = CustomerPacketType::PREMIUM;
                      break;
                  }
                  break;

              case 'utm_source':
              default:
                  $result = '';
                  break;
          }
        }

        return $result;
    }

    private function sendAttributes($data)
    {
      $payload=[
        'email'=>$data['email'],
        'name'=>(!empty($data['name']))?$data['name']:'unknown',
        'phone'=>(!empty($data['phone']))?$data['phone']:'unknown',
      ];

      $attrArr = ['sell_experience', 'sell_amount', 'utm_source', 'tarif', 'email'];
      $attributes = [
        IntercomAttributes::MONTHS_SINCE_SIGNUP     => 0,
        IntercomAttributes::DAYS_SINCE_SIGNUP       => 0,
        IntercomAttributes::NOT_EMPTY_LESSONS_COUNT => 0,
        IntercomAttributes::PUBLISHED_PRODUCTS      => 0,
        IntercomAttributes::TEAM_SIZE               => 1,
        IntercomAttributes::PROFILE_COMPLETED       => false,
        IntercomAttributes::FUNNELS_ACTIVATED       => 0,
      ];

      foreach ($data as $key => $val) {
          if (!in_array($key, $attrArr) || empty($val)) {
              continue;
          }

          $ind = '';

          switch ($key) {
              case 'sell_experience':
                  $ind = IntercomAttributes::PREVIOUS_PRODUCT;
                  $attributes[$ind] = (!empty($val)&&$val) ? true : false;
                  break;

              case 'sell_amount':
                  $ind = IntercomAttributes::PREVIOUS_SALES;
                  $attributes[$ind] = $val;
                  break;

              case 'utm_source':
                  $ind = IntercomAttributes::SOURCE;
                  $attributes[$ind] = $val;
                  break;

              case 'email':
                  $ind = IntercomAttributes::EMAIL;
                  $attributes[$ind] = $val;
                  break;

              case 'tarif':
                  $translate = [
                      CustomerPacketType::WEBINAR       => 'Webinar',
                      CustomerPacketType::CUSTOM        => 'Ind',
                      CustomerPacketType::VIP           => 'VIP',
                      CustomerPacketType::ONLINE_SCHOOL => 'Shkola',
                      CustomerPacketType::PROFESSIONAL  => 'Pro',
                      CustomerPacketType::PREMIUM       => 'Premium',
                  ];
                  $ind = IntercomAttributes::RATE_PLAN;
                  $attributes[$ind] = strtr($val, $translate);
                  break;
          }

      }

      if (!isset($attributes[IntercomAttributes::PREVIOUS_SALES])) {
        $attributes[IntercomAttributes::PREVIOUS_SALES] = 'unknown';
      }

      if (!isset($attributes[IntercomAttributes::PREVIOUS_PRODUCT])) {
        $attributes[IntercomAttributes::PREVIOUS_PRODUCT] = $attributes[IntercomAttributes::PREVIOUS_SALES] === 'unknown' ? false : true;
      }

      $role=ucfirst(
        strtolower(
          str_replace('ROLE_', '',
            (
              (!empty($data['role']))?
                $data['role']:CustomerRole::ROLE_AUTHOR
            )
          )
        )
      );

      $attributes[IntercomAttributes::ACCOUNT_TYPE]=$role;

      if (count($attributes) > 0) {
        $payload['custom_attributes']=$attributes;
      }

      // send data
      $this->intercomClient->getClient()->users->update($payload);
    }

    private function returnRegData(Array $answers)
    {
        $fields = $this->predefinedFields;
        $regData = [];

        foreach ($answers as $field) {
            $fieldId = $field['field']['id'];
            if (array_key_exists($fieldId, $fields)) {
                switch ($fields[$fieldId]) {
                    case 'email':
                        $regData[$fields[$fieldId]] = $field['email'];
                        break;
                    case 'role':
                        $regData[$fields[$fieldId]] = $this->getChoice($field);
                      break;
                    case 'sell_amount1':
                    case 'sell_amount2':
                        $regData['sell_amount'] = $this->getChoice($field);
                        break;

                    case 'sell_experience':
                        $regData['sell_experience'] = $field['boolean'];
                        break;

                    default:
                        $regData[$fields[$fieldId]] = $field['text'];
                        break;
                }
            }
        }

        return $regData;
    }
}
