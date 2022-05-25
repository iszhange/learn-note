<?php


namespace App\Library;


class MQTT
{

    public static function decodeValue($data)
    {
        return 256 * ord($data[0]) + ord($data[1]);
    }

    public static function decodeString($data)
    {
        $length = self::decodeValue($data);
        return substr($data, 2, $length);
    }

    public static function getHeader($data)
    {
        $byte = ord($data[0]);

        return [
            'type' => ($byte & 0xF0) >> 4,
            'dup' => ($byte & 0x08) >> 3,
            'qos' => ($byte & 0x06) >> 1,
            'retain' => $byte & 0x01,
        ];
    }

    public static function connect($header, $data)
    {
        $connectInfo = [];
        $connectInfo['protocol_name'] = self::decodeString($data);

        $offset = strlen($connectInfo['protocol_name']) + 2;
        $connectInfo['version'] = ord(substr($data, $offset, 1));

        $offset += 1;
        $byte = ord($data[$offset]);
        $connectInfo['willRetain'] = ($byte & 0x20 == 0x20);
        $connectInfo['willQos'] = ($byte & 0x18 >> 3);
        $connectInfo['willFlag'] = ($byte & 0x04 == 0x04);
        $connectInfo['cleanStart'] = ($byte & 0x02 == 0x02);

        $offset += 1;
        $connectInfo['keepalive'] = self::decodeValue(substr($data, $offset, 2));

        $offset += 2;
        $connectInfo['clientId'] = self::decodeString(substr($data, $offset));

        return $connectInfo;
    }
}