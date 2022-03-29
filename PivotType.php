<?php


namespace AcMarche\Pivot;

class PivotType
{
    //http://pivot.tourismewallonie.be/index.php/9-pivot-gest-pc/142-types-de-fiches-pivot
    //https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr;fmt=json

    const TYPE_EVENEMENT = 9;
    const TYPE_MEDIA = 268;

    const TYPES = [
        9 => 'Evenement',
        11 => 'Découverte et Divertissement',
        268 => 'Media',
    ];

}


enum Status
{
    case DRAFT;
    case PUBLISHED;
    case ARCHIVED;

    public function color(): string
    {
        return match ($this) {
            Status::DRAFT => 'grey',
            self::PUBLISHED => 'green',
            Status::ARCHIVED => 'red',
        };
    }

    public const Huge = self::PUBLISHED;

    public static function make(): Status
    {
        return Status::DRAFT;
    }
}

enum Status2: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}

class BlogPost
{
    public function __construct(
        public Status $status,
    ) {
    }
}

$post = new BlogPost(Status::DRAFT);
$status = Status::ARCHIVED;

echo $status->color(); // 'red'
var_dump(Status::cases());
