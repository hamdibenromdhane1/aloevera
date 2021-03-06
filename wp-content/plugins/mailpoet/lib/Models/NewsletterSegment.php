<?php

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


/**
 * @property int $newsletterId
 * @property int $segmentId
 * @property string $updatedAt
 */
class NewsletterSegment extends Model {
  public static $_table = MP_NEWSLETTER_SEGMENT_TABLE;
}
