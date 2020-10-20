<?php

/**
 * Class for converting NBT data to PHP data types
 */
class NBT {
    const TAG_END = 0;
    const TAG_BYTE = 1;
    const TAG_SHORT = 2;
    const TAG_INT = 3;
    const TAG_LONG = 4;
    const TAG_FLOAT = 5;
    const TAG_DOUBLE = 6;
    const TAG_BYTE_ARRAY = 7;
    const TAG_STRING = 8;
    const TAG_LIST = 9;
    const TAG_COMPOUND = 10;
    const TAG_INT_ARRAY = 11;
    const TAG_LONG_ARRAY = 12;

    /**
     * Convert a big endian binary string to machine order
     * @param $str - A number expressed as a string
     * @return string - the output string
    */
    private static function bigToSys($str) {
        if (pack('L', 1) === pack('N', 1)) {
            return $str;
        }
        return strrev($str);
    }

    /**
     * Reads a list of type
     * @param int $tag the tag number
     * @param resource $stream the NBT data stream
     * @return array the list that was read
     */
    private static function readList($tag, $stream) {
        $out = [];
        $len = self::readTag(self::TAG_INT, $stream);
        for ($i = 0; $i < $len; $i++)
            $out[] = self::readTag($tag, $stream);
        return $out;
    }

    /**
     * Reads a file
     * @param string $filename the name of the file to read from
     * @param string $wrapper the wrapper to use to open the file
     * @return array an array containing the root tag
     */
    public static function readFile($filename, $wrapper = 'compress.zlib://') {
        if (is_file($filename))
            return self::readStream(fopen($wrapper . $filename, 'rb'));
        trigger_error('The file "' . $filename . '" does not exist.', E_USER_WARNING);
        return [];
    }

    /**
     * * Reads a string of NBT data
     * @param string $str the NBT string to read
     * @return array an array containing the root tag
     */
    public static function readString($str) {
        $stream = fopen('php://memory', 'r+b');
        fwrite($stream, $str);
        return self::readStream($stream);
    }


    /**
     * Reads a stream of NBT data
     * @param resource $resource the resource to read from
     * @return array an array containing the root tag
     */
    public static function readStream($stream) {
        rewind($stream);
        $tag = self::readTag(self::TAG_BYTE, $stream);
        $name = self::readTag(self::TAG_STRING, $stream) ?: 0;
        return [ $name => self::readTag($tag, $stream) ];
    }

    /**
     * Reads a tag of type from the stream
     * @param int $type the tag number
     * @param resource $stream the NBT data stream
     * @return any the data the was read
     */
    public static function readTag($type, $stream) {
        switch($type) {
            case self::TAG_END:
                return null;
            case self::TAG_BYTE:
                return unpack('c', fread($stream, 1))[1];
            case self::TAG_SHORT:
                return unpack('s', self::bigToSys(fread($stream, 2)))[1];
            case self::TAG_INT:
                return unpack('l', self::bigToSys(fread($stream, 4)))[1];
            case self::TAG_LONG:
                return unpack('q', self::bigToSys(fread($stream, 8)))[1];
            case self::TAG_FLOAT:
                return unpack('G', fread($stream, 4))[1];
            case self::TAG_DOUBLE:
                return unpack('E', fread($stream, 8))[1];
            case self::TAG_STRING:
                $len = self::readTag(self::TAG_SHORT, $stream);
                if ($len === 0)
                    return '';
                return fread($stream, $len);
            case self::TAG_LIST:
                $tag = self::readTag(self::TAG_BYTE, $stream);
                return self::readList($tag, $stream);
            case self::TAG_BYTE_ARRAY:
                return self::readList(self::TAG_BYTE, $stream);
            case self::TAG_INT_ARRAY:
                return self::readList(self::TAG_INT, $stream);
            case self::TAG_LONG_ARRAY:
                return self::readList(self::TAG_LONG, $stream);
            case self::TAG_COMPOUND:
                $out = [];
                while (true) {
                    if (feof($stream))
                        break;
                    $tag = self::readTag(self::TAG_BYTE, $stream);
                    if ($tag == self::TAG_END)
                        break;
                    $name = self::readTag(self::TAG_STRING, $stream);
                    $out[ $name ] = self::readTag($tag, $stream, $out);
                }
                return $out;
        }
    }
}
