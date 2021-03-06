<?php

namespace AcMarche\Pivot\Entities;

trait LabelTrait
{
    /**
     * @var Label[] $label
     */
    public array $label = [];

    public function labelByLanguage(string $language = Label::FR): string
    {
        foreach ($this->label as $label) {
            if ($label->get($language)) {
                return $label->get($language);
            }
        }
        if ($language != Label::FR) {
            $language = Label::FR;
            foreach ($this->label as $label) {
                if ($label->get($language)) {
                    return $label->get($language);
                }
            }
        }

        return 'Title found';
    }
}
