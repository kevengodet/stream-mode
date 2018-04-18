<?php

namespace Keven\StreamMode;

/**
 * @todo Handle option 'e' (close-on-exec)
 */
final class Mode
{
    const
        // Readable stream
        READ                =  1,

        // Writable stream
        WRITE               =  2,

        // Create the file if it does not already exists
        CREATE              =  4,

        // Move the pointer to the end of the stream (instead of the beginning by default)
        POINTER_END         =  8,

        // Truncate the content of the file
        TRUNCATE            = 16,

        // Text translation mode (instead of binary mode by default)
        TEXT                = 32,

        // Overwrite already existing file
        OVERWRITE           = 64;

    private static $modes = array(
        'r'  => self::READ,
        'r+' => self::READ | self::WRITE,
        'w'  =>              self::WRITE | self::CREATE | self::TRUNCATE | self::OVERWRITE,
        'w+' => self::READ | self::WRITE | self::CREATE | self::TRUNCATE | self::OVERWRITE,
        'a'  =>              self::WRITE | self::CREATE                                    | self::POINTER_END,
        'a+' => self::READ | self::WRITE | self::CREATE                                    | self::POINTER_END,
        'x'  =>              self::WRITE | self::CREATE,
        'x+' => self::READ | self::WRITE | self::CREATE,
        'c'  =>              self::WRITE | self::CREATE                  | self::OVERWRITE,
        'c+' => self::READ | self::WRITE | self::CREATE                  | self::OVERWRITE,
    );

    /** @var string */
    private $mode;

    /** @var string */
    private $translation;

    public function __construct($mode)
    {
        $isText = false !== strpos($mode, 't');
        $isBinary = false !== strpos($mode, 'b');

        if ($isText && $isBinary) {
            throw new \DomainException('Cannot have text and binary mode at the same time.');
        }

        // Binary by default
        $this->translation = $isText ? 't' : 'b';
        $modeWithoutTranslation = str_replace(array('t', 'b'), '', $mode);

        // Put the + sign at the end of the mode
        if (false !== strpos($modeWithoutTranslation, '+')) {
            $normalizedMode = str_replace('+', '', $modeWithoutTranslation);
        } else {
            $normalizedMode = $modeWithoutTranslation;
        }

        if (!isset(self::$modes[$normalizedMode])) {
            throw new \DomainException("Unknown mode '$mode'");
        }

        $this->mode = self::$modes[$normalizedMode];
    }

    /**
     *
     * @param int $options
     *  @return Mode
     */
    public static function build($options = self::READABLE)
    {
        $translation = ($options & self::TEXT ) ? 't' : 'b';
        $optionsWithoutTranslation = $options & ~self::TEXT;

        if (!in_array($optionsWithoutTranslation, self::$modes)) {
            throw new \OutOfRangeException('There is no mode for the requested options. Available modes are: '.implode(', ', array_values(self::$modes)));
        }

        return array_search($optionsWithoutTranslation, self::$modes).$translation;
    }

    /** @return boolean */
    public function isReadable()
    {
        return (bool) ($this->mode & self::READ);
    }

    /** @return boolean */
    public function isWritable()
    {
        return (bool) ($this->mode & self::WRITE);
    }

    /** @return boolean */
    public function isBinary()
    {
        return !$this->isText();

    }

    /** @return boolean */
    public function isText()
    {
        return (bool) ($this->mode & self::TEXT);
    }

    /** @return boolean */
    public function isCreatable()
    {
        return (bool) ($this->mode & self::CREATE);
    }

    /** @return boolean */
    public function isOverwritable()
    {
        return (bool) ($this->mode & self::OVERWRITE);
    }

    /** @return boolean */
    public function isTruncatable()
    {
        return (bool) ($this->mode & self::TRUNCATE);
    }

    public function isPointerAtTheBeginning()
    {
        return !$this->isPointerAtTheEnd();
    }

    public function isPointerAtTheEnd()
    {
        return (bool) ($this->mode & self::POINTER_END);
    }

    /** @return string */
    public function __toString()
    {
        return array_search($this->mode, self::$modes).$this->translation;
    }
}
