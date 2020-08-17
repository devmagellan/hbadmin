<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable\Webhook;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebHook
 *
 * @ORM\Table(name="webhook_teachable_comment_created")
 * @ORM\Entity()
 */
class CommentCreated extends AbstractWebhook
{
}
