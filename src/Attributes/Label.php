<?php

namespace IBroStudio\Git\Attributes;

#[\Attribute]
class Label
{
    public function __construct(public string $text) {}
}
