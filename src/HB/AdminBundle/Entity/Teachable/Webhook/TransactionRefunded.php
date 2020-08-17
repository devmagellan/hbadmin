<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable\Webhook;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebHook
 *
 * @ORM\Table(name="webhook_teachable_transaction_refunded")
 * @ORM\Entity()
 */
class TransactionRefunded extends AbstractWebhook
{
}
