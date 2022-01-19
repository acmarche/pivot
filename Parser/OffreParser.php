<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Categorie;
use AcMarche\Pivot\Entities\Communication;
use AcMarche\Pivot\Entities\Contact;
use AcMarche\Pivot\Entities\Description;
use AcMarche\Pivot\Entities\Geocode;
use AcMarche\Pivot\Entities\Horaire;
use AcMarche\Pivot\Entities\Horline;
use AcMarche\Pivot\Entities\Libelle;
use AcMarche\Pivot\Entities\Localite;
use AcMarche\Pivot\Entities\Media;
use AcMarche\Pivot\Entities\Selection;
use AcMarche\Pivot\Utils\PropertyUtils;
use AcMarche\Pivot\Utils\SortUtils;
use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class OffreParser
{
    public PropertyAccessor $propertyAccessor;
    private DOMXPath $xpath;

    public function __construct(
        public DOMDocument $document,
        public DOMElement  $offre
    )
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->xpath = new DOMXPath($document);
    }

    public function offreId(): string
    {
        return $this->getAttribute($this->offre, 'id');
    }

    public function getAttributs(string $name): ?string
    {
        $domList = $this->offre->getElementsByTagName($name);
        if ($domList instanceof DOMNodeList) {
            $domElement = $domList->item(0);
            if ($domElement instanceof DOMElement) {
                return $domElement->nodeValue;
            }
        }

        return null;
    }

    public function getTitre(DOMElement $offreDom): Libelle
    {
        $titles = $this->xpath->query('titre', $offreDom);
        $libelle = new Libelle();
        foreach ($titles as $title) {
            $language = $title->getAttributeNode('lg');
            $libelle->add($language->nodeValue, $title->nodeValue);
        }

        return $libelle;
    }

    public function geocodes(DOMElement $offreDom): Geocode
    {
        $coordinates = new Geocode();
        $geocodes = $this->xpath->query('geocodes', $offreDom);
        $geocode = $geocodes->item(0);
        if (!$geocode instanceof DOMElement) {
            return $coordinates;
        }
        foreach ($geocode->childNodes as $child) {
            if (XML_ELEMENT_NODE === $child->nodeType) {
                foreach ($child->childNodes as $cat) {
                    if (XML_ELEMENT_NODE === $cat->nodeType) {
                        $this->propertyAccessor->setValue($coordinates, $cat->nodeName, $cat->nodeValue);
                    }
                }
            }
        }

        return $coordinates;
    }

    public function localisation(DOMElement $offreDom): Localite
    {
        $data = new Localite();
        $localisations = $offreDom->getElementsByTagName('localisation');
        $localisation = $localisations->item(0);
        if (!$localisation instanceof DOMElement) {
            return $data;
        }

        foreach ($localisation->childNodes as $child) {
            if (XML_ELEMENT_NODE === $child->nodeType) {
                $data->id = $child->getAttributeNode('id')->nodeValue;
                foreach ($child->childNodes as $cat) {
                    if (XML_ELEMENT_NODE === $cat->nodeType) {
                        $this->propertyAccessor->setValue($data, $cat->nodeName, $cat->nodeValue);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @return Contact[]
     */
    public function contacts(DOMElement $offreDom): array
    {
        $data = [];
        $contacts = $this->xpath->query('contacts', $offreDom);
        if (0 === $contacts->length) {
            return [];
        }
        //$propertyUtils = new PropertyUtils();
        foreach ($contacts->item(0)->childNodes as $contactDom) {
            if (XML_ELEMENT_NODE === $contactDom->nodeType) {
                $contact = new Contact();
                //$propertyUtils->initAttributesObject(Contact::class, $contact);
                $data[] = $contact;
                $contact->tri = $contactDom->getAttributeNode('tri')->nodeValue;
                $contact->lib = $this->getLibelle($contactDom);
                $contact->communications = $this->extractCommunications($contactDom);
                foreach ($contactDom->childNodes as $attribute) {
                    if (XML_ELEMENT_NODE === $attribute->nodeType && ('lib' !== $attribute->nodeName && 'communications' !== $attribute->nodeName)) {
                        $this->propertyAccessor->setValue($contact, $attribute->nodeName, $attribute->nodeValue);
                    }
                }
                $data[] = $contact;
            }
        }

        return $data;
    }

    /**
     * @return Description[]
     */
    public function descriptions(DOMElement $offreDom): array
    {
        $data = [];
        $descriptions = $this->xpath->query('descriptions', $offreDom);
        if (!$descriptions->item(0) instanceof DOMElement) {
            return [];
        }
        foreach ($descriptions->item(0)->childNodes as $descriptionDom) {
            if ($descriptionDom instanceof DOMElement) {
                $description = new Description();
                $description->dat = $this->getAttributeNode($descriptionDom, 'dat');
                $description->lot = $this->getAttributeNode($descriptionDom, 'lot');
                $description->tri = $this->getAttributeNode($descriptionDom, 'tri');
                $description->typ = $this->getAttributeNode($descriptionDom, 'typ');
                $description->lib = $this->getLibelle($descriptionDom);
                $libelle = new Libelle();
                $textes = $this->xpath->query('texte', $descriptionDom);
                foreach ($textes as $texte) {
                    $language = $texte->getAttributeNode('lg');
                    if ($language) {
                        $libelle->add($language->nodeValue, $texte->nodeValue);
                    } else {
                        $libelle->add('default', $texte->nodeValue);
                    }
                }
                $description->texte = $libelle;
                $data[] = $description;
            }
        }

        return SortUtils::sortDescriptions($data);
    }

    /**
     * @return Media[]
     */
    public function medias(DOMElement $offreDom): array
    {
        $data = [];
        $medias = $this->xpath->query('medias', $offreDom);
        if (!$medias->item(0) instanceof DOMElement) {
            return [];
        }
        foreach ($medias->item(0)->childNodes as $categoryDom) {
            if ($categoryDom instanceof DOMElement) {
                $media = new Media();
                $media->ext = $categoryDom->getAttributeNode('ext')->nodeValue;
                $media->libelle = $this->getTitre($categoryDom);
                foreach ($categoryDom->childNodes as $cat) {
                    if (XML_ELEMENT_NODE === $cat->nodeType) {
                        $this->propertyAccessor->setValue($media, $cat->nodeName, $cat->nodeValue);
                    }
                }
                $data[] = $media;
            }
        }
        array_map(
            function ($media) {
                $media->url = preg_replace('#http:#', 'https:', $media->url);
            },
            $data
        );

        return $data;
    }

    /**
     * @return Selection[]
     */
    public function selections(): array
    {
        $data = [];
        $object = $this->offre->getElementsByTagName('selections');
        $selections = $object->item(0); //pour par prendre elements parents
        if (!$selections instanceof DOMElement) {
            return [];
        }

        foreach ($selections->childNodes as $child) {
            if (XML_ELEMENT_NODE === $child->nodeType) {
                $selection = new Selection();
                $selection->id = $child->getAttributeNode('id')->nodeValue;
                $selection->cl = $child->getAttributeNode('cl')->nodeValue;
                foreach ($child->childNodes as $cat) {
                    if (XML_ELEMENT_NODE === $cat->nodeType) {
                        $this->propertyAccessor->setValue($selection, $cat->nodeName, $cat->nodeValue);
                    }
                }
                $data[] = $selection;
            }
        }

        return $data;
    }

    /**
     * @return Categorie[]
     */
    public function categories(DOMElement $offreDom): array
    {
        $data = [];
        $categories = $this->xpath->query('categories', $offreDom);
        foreach ($categories->item(0)->childNodes as $categoryDom) {
            if ($categoryDom instanceof DOMElement) {
                $category = new Categorie();
                $category->id = $this->getAttributeNode($categoryDom, 'id');
                $category->tri = $this->getAttributeNode($categoryDom, 'tri');
                $category->lib = $this->getLibelle($categoryDom);
                $data[] = $category;
            }
        }

        return $data;
    }

    public function parents(DOMElement $offreDom): array
    {
        $ids = [];

        $parents = $this->xpath->query('parents', $offreDom);
        if (null === $parents || 0 === $parents->count()) {
            return [];
        }
        $parents = $parents->item(0); //pour par prendre elements parents
        foreach ($parents->childNodes as $offre) {
            if (XML_ELEMENT_NODE === $offre->nodeType) {
                $ids[] = (int)$this->getAttributeNode($offre, 'id');
            }
        }

        return $ids;
    }

    public function enfants(DOMElement $offreDom): array
    {
        $ids = [];

        $enfants = $this->xpath->query('enfants', $offreDom);
        if (null === $enfants || 0 === $enfants->count()) {
            return [];
        }
        $enfants = $enfants->item(0); //pour par prendre elements parents

        foreach ($enfants->childNodes as $offre) {
            if (XML_ELEMENT_NODE === $offre->nodeType) {
                $ids[] = (int)$this->getAttributeNode($offre, 'id');
            }
        }

        return $ids;
    }

    public function horaires(DOMElement $offreDom): array
    {
        $data = [];
        $horaires = $this->xpath->query('horaires', $offreDom);
        $horaires = $horaires->item(0); //pour par prendre elements parents
        if (!$horaires instanceof DOMElement) {
            return [];
        }

        $year = $this->getAttributeNode($horaires, 'an');

        foreach ($horaires->childNodes as $horaireDom) {
            if ($horaireDom instanceof DOMElement) {
                $horaire = new Horaire();
                $horaire->year = $year;
                $labels = $this->xpath->query('lib', $horaireDom);
                $libelle = new Libelle();
                foreach ($labels as $label) {
                    $language = $label->getAttributeNode('lg');
                    if ($language) {
                        $libelle->add($language->nodeValue, $label->nodeValue);
                    } else {
                        $libelle->add('default', $label->nodeValue);
                    }
                }
                $horaire->lib = $libelle;
                $textes = $this->xpath->query('texte', $horaireDom);
                $libelle = new Libelle();
                foreach ($textes as $texte) {
                    $language = $texte->getAttributeNode('lg');
                    if ($language) {
                        $libelle->add($language->nodeValue, $texte->nodeValue);
                    } else {
                        $libelle->add('default', $texte->nodeValue);
                    }
                }
                $horaire->texte = $libelle;
                $horaire->horlines = $this->extractHoraires($horaireDom);
                $data[] = $horaire;
            }
        }

        return $data;
    }

    /**
     * @return Communication[]
     */
    private function extractCommunications(DOMElement $contactDom): array
    {
        $data = [];
        $communications = $this->xpath->query('communications', $contactDom);
        foreach ($communications as $communicationsDom) {
            foreach ($communicationsDom->childNodes as $communicationDom) {
                if ($communicationDom instanceof DOMElement) {
                    $communication = new Communication();
                    $communication->typ = $this->getAttributeNode($communicationDom, 'typ');
                    $communication->tri = $this->getAttributeNode($communicationDom, 'tri');
                    $communication->lib = $this->getLibelle($communicationDom);
                    $vals = $this->xpath->query('val', $communicationDom);
                    if ($vals->count() > 0) {
                        $communication->val = $vals->item(0)->nodeValue;
                        if ('' !== $communication->val) {
                            $data[] = $communication;
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @return Horline[]
     */
    private function extractHoraires(DOMElement $horaireDom): array
    {
        $data = [];
        $horlines = $this->xpath->query('horline', $horaireDom);
        foreach ($horlines as $horlineDom) {
            $horline = new Horline();
            $horline->id = $this->getAttribute($horlineDom, 'id');
            foreach ($horlineDom->childNodes as $node) {
                if (XML_ELEMENT_NODE === $node->nodeType) {
                    $this->propertyAccessor->setValue($horline, $node->nodeName, $node->nodeValue);
                }
            }
            [$horline->day, $horline->month, $horline->year] = explode('/', $horline->date_deb);
            $data[] = $horline;
        }

        return $data;
    }

    private function getAttribute(?DOMElement $element, string $name): string
    {
        if (null !== $element) {
            return $element->getAttribute($name);
        }

        return '';
    }

    private function getAttributeNode(?DOMElement $element, string $name): ?string
    {
        if (null !== $element) {
            $node = $element->getAttributeNode($name);
            if ($node instanceof DOMAttr) {
                return $node->nodeValue;
            }
        }

        return null;
    }

    private function getLibelle($dom): Libelle
    {
        $libelle = new Libelle();
        $libs = $this->xpath->query('lib', $dom);
        foreach ($libs as $lib) {
            $language = $lib->getAttributeNode('lg');
            if ($language) {
                $libelle->add($language->nodeValue, $lib->nodeValue);
            } else {
                $libelle->add('default', $lib->nodeValue);
            }
        }

        return $libelle;
    }
}
