<?php

namespace AcMarche\Pivot;

use AcMarche\Pivot\Entities\Person;
/*
enum T
{
    case DRAFT;

    public function color(): Person
    {
        return match ($this) {
            T::DRAFT => new Person(),
        };
    }
}

$zeze = T::DRAFT;
$z = $zeze->color();
echo $z->getAge();
*/
class PivotEnum
{
    /*
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
  var_dump(Status::cases());*/
}