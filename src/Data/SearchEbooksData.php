<?php

namespace App\Data;

use App\Entity\Expertise;
use App\Entity\Ebook;

class SearchEbooksData
{
    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Expertise[]
     */
    public $expertise = [];

    /**
     * @var Ebook[]
     */
    public $ebook = [];

        /**
     * @var null|date
     */
    public $to;

    /**
     * @var null|date
     */
    public $from;
}
