<?php

namespace AcMarche\Pivot\Entities\Event;

class DateEvent {

    public ?\DateTimeInterface $dateBegin = null;
    public ?\DateTimeInterface $dateEnd= null;
    public string $ouvertureHeure1 = '';
    public string $fermetureHeure1 = '';
    public string $ouvertureHeure2 = '';
    public string $fermetureHeure2 = '';
    public string $ouvertureDetails = '';


}