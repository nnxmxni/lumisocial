<?php

/**
 * Generate one time tokens.
 *
 * @return string
 * @throws OneTimePasswordGenerationException
 */

use App\Exceptions\OneTimePasswordGenerationException;

/**
 * Generate one time tokens.
 *
 * @param int $length
 * @return string
 * @throws OneTimePasswordGenerationException
 */
function generateToken(int $length): string
{
    if ($length <= 0) {
        throw new InvalidArgumentException('Length must be a positive integer');
    }

    $max = intval(str_repeat('9', $length));
    $min = intval('1' . str_repeat('0', $length - 1));

    try {
        $number = random_int($min, $max);
    } catch (Throwable $exception) {
        throw new OneTimePasswordGenerationException(
            message: 'Failed to generate a random integer',
            code: 500
        );
    }

    return str_pad(
        string: strval($number),
        length: $length,
        pad_string: '0',
        pad_type: STR_PAD_LEFT,
    );
}

/**
 * Prepare a standardized success payload.
 *
 * @param string $message
 * @param int $status
 * @param array $data
 * @return stdClass
 */
function prepareSuccessPayload(string $message, int $status = 200, array $data = []): stdClass
{
    return (object) [
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ];
}

/**
 *  Centralized exception handling.
 *
 * @param Exception $exception
 * @param int $status
 * @return stdClass
 */
function handleException(Exception $exception, int $status = 500): stdClass
{
    return (object) [
        'message' => $exception->getMessage(),
        'status' => $exception->getCode() ?: $status
    ];
}
