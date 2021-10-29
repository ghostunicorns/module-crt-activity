<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model\Utils;

use Magento\Framework\Serialize\SerializerInterface;

class GetActivityReportByExtra
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param string $extraString
     * @return string
     */
    public function execute(string $extraString): string
    {
        $extraData = $this->serializer->unserialize($extraString);

        $result = '';

        if (array_key_exists('error', $extraData)) {
            $message = $extraData['error'];
            $res = strpos($message, '<body');
            if ($res != false) {
                preg_match_all('~<body(.*?)body>~s', $message, $messages);
                $message = $messages[0][0];
                $message = str_replace('<body>', '', $message);
                $message = str_replace('</body>', '', $message);
            }

            $result .= "<b>Error message: </b>" . $message;
        }

        if ($result !== '') {
            $result .= "<br>Imported: ";
        } else {
            $result .= 'Imported: ';
        }

        if (array_key_exists('ok', $extraData)) {
            $result .= $extraData['ok'];
        } else {
            $result .= '0';
        }

        $result .= ' <br>Errors: ';
        if (array_key_exists('ko', $extraData)) {
            $result .= $extraData['ko'];
        } else {
            $result .= '0';
        }

        return $result;
    }
}
