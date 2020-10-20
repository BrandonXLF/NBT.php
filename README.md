# NBT to PHP

NBT to PHP allows you to convert NBT data to PHP data types to easily use within a PHP program.

## Introduction

Since this program converts the NBT data to native PHP data types, there is currently no way to convert the data back to NBT data since PHP has fewer data types than NBT.

An advantage is having no PHP to NBT converter is this library is very lightweight and works very well in applications where read-only functionality is needed, like reading from an API or file.

An advantage of the approach of converting to PHP data types is that it is very easy to display and read the output using [`print_r`](https://www.php.net/manual/en/function.print-r.php).

## Installation

To install this libary, download the NBT.php file and load it using:
```php
require 'NBT.php';
```

## Documentation

As of now, all functions in this library as static, meaning there's no need to create a new object of type NBT, but you can.

There are 3 main functions that you should access, `NBT::readFile`, `NBT::readString`, and `NBT::readStream`.

### NBT::readFile

NBT::readFile is used to read NBT data from a file.

```php
NBT::readFile( string $filename, string $wrapper = 'compress.zlib://' ) : array
```

#### Parameters

* **$filename** - The name of the file to read from
* **$wrapper** - The wrapper to use to read the file, by default it is `compress.zlib://`. Change the wrapper to `file://` to  read a uncompressed file.

#### Returns

An array with the NBT data converted to PHP.

### NBT::readString

NBT::readString is used to read NBT data from a string.

```php
NBT::readString( string $str ) : array
```

#### Parameters

* **$str** - The string to binary uncompressed NBT data. If you need to read compressed data, you'll likely need to use [`gzdecode`](https://www.php.net/manual/en/function.gzdecode) to decompress the string first.

#### Returns

An array with the NBT data converted to PHP.

### NBT::readStream

NBT::readStream is used to read NBT data from a file stream.

```php
NBT::readStream( resource $stream ) : array
```

#### Parameters

* **$str** - A binary
 file stream to read the NBT data from.

#### Returns

An array with the NBT data converted to PHP.

### NBT::readTag

NBT::readTag is used to read a NBT tag from a file stream.

```php
NBT::readTag( int $type, resource $stream ) : mixed
```

#### Parameters

* **$type** - The ID of the type of NBT tag. It is recommended to use one of the constants defined at the top of NBT.php.
* **$stream** - The file stream to read from.

#### Returns

The NBT data read as PHP data, return type depends on the `$type` parameter.

## Testing

Tests for this script can be found in the `tests/test.php` php file. You may run `php tests/test.php` to test this script.