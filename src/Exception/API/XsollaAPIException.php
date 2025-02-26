<?php

namespace Xsolla\SDK\Exception\API;

use GuzzleHttp\Exception\BadResponseException;
use Xsolla\SDK\Exception\XsollaException;
// use function GuzzleHttp\Psr7\str;
use GuzzleHttp\Psr7\Message;


class XsollaAPIException extends XsollaException
{
    protected static $exceptions = [
        422 => '\Xsolla\SDK\Exception\API\UnprocessableEntityException',
        403 => '\Xsolla\SDK\Exception\API\AccessDeniedException',
    ];

    protected static $messageTemplate =
<<<'EOF'
Xsolla API Error Response:

Previous Exception:
===================
%s

Request:
===================
%s

Response:
===================
%s
EOF;

    /**
     * @return XsollaAPIException
     */
    public static function fromBadResponse(BadResponseException $previous)
    {
        $statusCode = $previous->getResponse()->getStatusCode();
        $message = sprintf(
            static::$messageTemplate,
            $previous->getMessage(),
//             str($previous->getRequest()),
//             str($previous->getResponse())
            Message::toString($previous->getRequest()),
            Message::toString($previous->getResponse())
        );
        if (array_key_exists($statusCode, static::$exceptions)) {
            return new static::$exceptions[$statusCode]($message, 0, $previous);
        }

        return new self($message, 0, $previous);
    }
}
