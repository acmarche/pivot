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
            if (is_array($label)) {
                return $this->getByArray($language);
            }

            return $this->getByObject($language);
        }

        return 'Title not found';
    }

    private function getByArray(string $language): string
    {
        foreach ($this->label as $label) {
            if ($label['lang'] == $language) {
                return $label['value'];
            }
        }
        if ($language != Label::FR) {
            $language = Label::FR;
            foreach ($this->label as $label) {
                if ($label['lang'] == $language) {
                    return $label['value'];
                }
            }
        }

        return 'Title not found';
    }

    private function getByObject(string $language): string
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

        if (isset($this->name) && $this->name != '') {
            return $this->name;
        }

        if (isset($this->nom) && $this->nom != '') {
            return $this->nom;
        }

        return 'Title not found';
    }
}
