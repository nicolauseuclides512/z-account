<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;

class MultiResImage implements Arrayable
{
    private $original;
    private $medium;
    private $small;

    public function __construct($original)
    {
        $this->original = $original;
        $this->medium = $this->generateMulitResName('medium');
        $this->small = $this->generateMulitResName('small');
    }

    private function generateMulitResName($multiResName)
    {
        $path = parse_url($this->original, PHP_URL_PATH);
        $parts = pathinfo($path);
        if (!isset($parts['filename']) || !isset($parts['extension'])) {
            return null;
        }

        $newBaseName = $parts['filename']
            . "_" . $multiResName
            . "." . $parts['extension'];

        $newUrl = str_replace($parts['basename'], $newBaseName, $this->original);

        return $newUrl;
    }

    public function toArray()
    {
        return [
            'original' => $this->original,
            'medium' => $this->medium,
            'small' => $this->small,
        ];
    }
}

